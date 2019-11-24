@extends('layouts.app')

@section("content")
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

    @isset($users)
        <table class="table mt-2">

            <thead class="thead-light">
            <tr>

                <th scope="col">#</th>
                <th scope="col">Имя</th>
                <th scope="col">Телефон</th>
                <th scope="col">Администратор</th>

            </tr>
            </thead>
            <tbody>
            @foreach($users as $key => $user)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td><a href="{{ route('users.show',$user->id) }}">
                            {{$user->fio_from_telegram}}</a>
                        <a class="btn btn-link" href="{{ route('users.edit',$user->id) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    <td>{{$user->phone}}</td>
                    <td>
                        {{$user->is_admin?"Администратор":"Пользователь"}}
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

        {{ $users->links() }}
    @endisset

@endsection