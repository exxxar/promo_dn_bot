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

                    <h1>Пользователи</h1>
                @isset($users)
                        <form class="row mb-2" action="{{route("users.search")}}" method="post">
                            @csrf
                            <div class="form-group col-md-5">
                                <input type="text" class="form-control pull-right" id="usersSearch"
                                       name="users-search" placeholder="Поиск по таблице">
                            </div>
                            <div class="form-group col-md-1">
                                <button type="submit" class="btn btn-info btn-pill pull-right">Искать</button>
                            </div>


                        </form>

                        <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>

                            <th scope="col">#</th>
                            <th scope="col">Имя</th>
                            <th scope="col">Телефон</th>
                            <th scope="col">Root</th>
                            <th scope="col">Администратор</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $key => $user)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('users.show',$user->id) }}">
                                        {{$user->fio_from_telegram??$user->email}}</a>
                                    <a class="btn btn-link" href="{{ route('users.edit',$user->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                                <td>{{$user->phone}}</td>
                                <td>
                                    @isset($refferal->parent)
                                    <a href="{{ route('users.show',$refferal->parent->id) }}">
                                        {{$refferal->parent->phone??$refferal->parent->name??$refferal->parent->telegram_chat_id}}</a>
                                    @endisset

                                </td>
                                <td>
                                    {{$user->is_admin?"Администратор":"Пользователь"}}
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $users->links() }}
                @endisset

            </div>
        </div>
    </div>
@endsection