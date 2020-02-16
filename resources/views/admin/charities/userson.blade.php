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

                @isset($donates)

                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>

                            <th scope="col">#</th>
                            <th scope="col">Имя</th>
                            <th scope="col">Величина доната</th>
                            <th scope="col">Компания</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($donates as $key => $donate)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td><a href="{{ route('users.show',$donate->user->id) }}">
                                        {{$donate->user->fio_from_telegram??$donate->user->email}}</a>
                                    <a class="btn btn-link" href="{{ route('users.edit',$donate->user->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                                <td>{{$donate->donated_money}}</td>
                                <td>
                                    {{$donate->company->title}}
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $donates->links() }}
                @endisset

            </div>
        </div>
    </div>
@endsection
