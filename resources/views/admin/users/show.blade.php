@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации о пользователе</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Назад</a>
                            <a class="btn btn-primary" href="{{ route('users.cashback.index',$user->id) }}"> Начислить
                                кэшбэк</a>
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
                        <td>Имя из телеграм</td>
                        <td>
                            <h6>{{$user->name}}</h6>
                            <h1>{{$user->fio_from_telegram}}</h1>

                        </td>
                    </tr>
                    <tr>
                        <td>Введенное имя</td>
                        <td>
                            <p>{{$user->fio_from_request}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Аватар пользователя</td>
                        <td>
                            <a href="{{$user->avatar_url}}}" target="_blank"><img src="{{$user->avatar_url}}"
                                                                                  style="width:150px;height: 150px;"
                                                                                  class="img-thumbnail" alt="">
                            </a>


                        </td>
                    </tr>


                    <tr>
                        <td>Телефон</td>
                        <td>
                            <p>{{$user->phone}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Пол</td>
                        <td>
                            <p>{{$user->sex==0?"Мужской":"Женский"}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Возраст</td>
                        <td>
                            <p>{{$user->age}}</p>
                    </tr>
                    <tr>
                        <td>Чат telegram id</td>
                        <td>
                            <p>{{$user->telegram_chat_id}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Количество рефералов</td>
                        <td>
                            <p>{{$user->referrals_count}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Реферальный бонус</td>
                        <td>
                            <p>{{$user->referral_bonus_count}}</p>
                        </td>
                    </tr>


                    <tr>
                        <td>Кэшбэк бонус</td>
                        <td>
                            <p>{{$user->cashback_bonus_count}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Роль пользователя</td>
                        <td>
                            <p>{{$user->is_admin?"Администратор":"Пользователь"}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">
                                Редактировать <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('users.destroy', $user->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>

                @if(isset($user->promos))
                    <h3>Участие в акциях</h3>
                    <ul>
                        @foreach($user->promos as $promotion)
                            <li>
                                <a href="{{ route('promotions.show',$promotion->id) }}">
                                    {{$promotion->title}}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if(isset($user->companies))
                    <h3>Сотрудник компаний</h3>
                    <ul>
                        @foreach($user->companies as $company)
                            <li>
                                <a href="{{ route('companies.show',$company->id) }}">
                                    {{$company->title}}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection