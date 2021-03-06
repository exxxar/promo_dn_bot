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

                <h1>История рефералов</h1>
                @isset($refferals)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Кто поделился</th>
                            <th scope="col">Кто пришел</th>
                            <th scope="col">Подтверждение</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($refferals as $key => $refferal)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>
                                    @isset($refferal->sender)
                                        <a href="{{ route('users.show',$refferal->sender->id) }}">
                                            {{$refferal->sender->phone??$refferal->sender->name??$refferal->sender->telegram_chat_id}}</a>
                                    @endisset
                                </td>

                                <td>
                                    @isset($refferal->recipient)
                                        <a href="{{ route('users.show',$refferal->recipient->id) }}">
                                            {{$refferal->recipient->phone??$refferal->recipient->name??$refferal->recipient->telegram_chat_id}}</a>
                                    @endisset

                                    @if(!$refferal->recipient)
                                        <p>Пользователь удален или изменен администратором</p>
                                    @endif
                                </td>
                                <td>
                                    {{$refferal->activated?"Активирован":"Не активирован"}}
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $refferals->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
