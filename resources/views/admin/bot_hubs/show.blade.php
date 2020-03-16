@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по БОТу</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('bot_hubs.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('bot_hubs.edit',$bot->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                        </div>


                    </div>
                </div>


                <table class="table mt-2">
                    <thead class="thead-light ">
                    <th>Параметр</th>
                    <th>Значение</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Ссылка на БОТа</td>
                        <td>
                            <a herf="{{$bot->bot_url}}">{{$bot->bot_url}}</a>
                        </td>
                    </tr>

                    <tr>
                        <td>WebHook url</td>
                        <td>
                            <div class="row">
                                <div class="col-sm-4">
                                    <p>{{$bot->webhook_url}}</p>
                                </div>
                                <div class="col-sm-4">
                                    <a class="btn btn-info" href="{{route("bot_hubs.setwebhook",$bot->id)}}">Установить</a>
                                </div>
                                <div class="col-sm-4">
                                    <a class="btn btn-info" href="{{route("bot_hubs.unsetwebhook",$bot->id)}}">Убрать</a>
                                </div>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td>Ссылка на картинку для бота</td>
                        <td>
                            <img src="{{$bot->bot_pic}}" class="img-thumbnail"
                                 style="width:200px;height:200px;object-fit: cover;">
                        </td>
                    </tr>

                    <tr>
                        <td>Описание</td>
                        <td>
                            <p>{{$bot->description}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Token Production</td>
                        <td>
                            <p>{{$bot->token_prod}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Token Development</td>
                        <td>
                            <p>{{$bot->token_dev}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Баланс на счету</td>
                        <td>
                            <p>{{$bot->money}} ₽</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Тариф (за день)</td>
                        <td>
                            <p>{{$bot->money_per_day}} ₽</p>
                        </td>
                    </tr>


                    <tr>
                        <td>Отображение</td>
                        <td>
                            <p>{{$bot->is_active?"Работает":"Не работает"}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Компания, от которой БОТ</td>
                        <td>
                            <p>{{$bot->company->title}}</p>
                        </td>
                    </tr>


                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
