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

                @isset($cashbacks)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Денег в чеке</th>
                            <th scope="col">Статус</th>
                            <th scope="col">Сотрудник</th>
                            <th scope="col">Клиент</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cashbacks as $key => $cashback)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$cashback->money_in_check}} руб.</td>
                                <td>
                                    {{$cashback->activated?"Активирован":"Не активирован"}}
                                </td>
                                <td><a href="{{ route('users.show',$cashback->employee->id) }}">
                                        {{$cashback->employee->phone}}</a>
                                </td>
                                <td><a href="{{ route('users.show.phone',$cashback->user_phone) }}">
                                        {{$cashback->user_phone}}</a>
                                </td>


                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $cashbacks->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection