@extends('layouts.app')

@section("content")

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ $message }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <h5>Добавление нового промокода</h5>
                        <form method="post" action="{{ route('promocodes.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <input type="text" name="code" class="form-control" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <select name="company_id" class="form-control">
                                        @foreach($companies as $company)
                                            <option value="{{$company->id}}">{{$company->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <button class="btn btn-primary">Добавить</button>
                                </div>
                            </div>


                        </form>
                    </div>
                </div>

                <h1>Промокоды</h1>
                @isset($promocodes)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Код</th>
                            <th scope="col">Статус активации</th>
                            <th scope="col">Статус приза</th>
                            <th scope="col">Пользователь</th>
                            <th scope="col">Компания</th>
                            <th scope="col">Действие</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($promocodes as $key => $promocode)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>
                                    {{$promocode->code}}

                                </td>
                                <td>{{$promocode->activated?"Активирован":"Не активирован"}}</td>
                                <td>



                                    <a class="btn btn-info" href="{{ route('promocodes.changestatus',$promocode->id) }}" title="Обновить статус">
                                        {{$promocode->prize_has_taken?"Приз не получен":"Приз получен"}}
                                    </a>
                                </td>
                                <td>
                                    @if($promocode->user)
                                        <a href="{{$promocode->user->id}}">{{$promocode->user->email}}</a>
                                    @else
                                        <p>Нет пользователя</p>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('companies.show',$promocode->company->id) }}">
                                        {{$promocode->company->title}}</a>
                                </td>
                                <td>
                                    <form action="{{ route('promocodes.destroy', $promocode->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $promocodes->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
