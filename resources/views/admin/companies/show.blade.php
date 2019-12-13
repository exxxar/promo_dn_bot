@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр компании</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('companies.index') }}"> Назад</a>
                            <a class="btn btn-primary" href="#">Пользователи компании</a>
                            <a class="btn btn-link" href="{{ route('companies.edit',$company->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>

                        @if (count($errors) > 0)
                            <div class="alert alert-danger mt-2">
                                <strong>Упс!</strong> Возникли ошибки при заполнении полей.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                    </div>
                </div>


                <table class="table mt-2">
                    <thead class="thead-light ">
                    <th>Параметр</th>
                    <th>Значение</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Заголовок</td>
                        <td>
                            <h2>{{$company->title}}</h2>
                        </td>
                    </tr>
                    <tr>
                        <td>Описание</td>
                        <td>
                            <p>{{$company->description}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Процент CashBack</td>
                        <td>
                            <p>{{$company->cashback}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Адрес</td>
                        <td>
                            <p>{{$company->address}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Телефон</td>
                        <td>
                            <p>{{$company->phone}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>E-mail</td>
                        <td>
                            <p>{{$company->email}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Ответственное лицо</td>
                        <td>
                            <p>{{$company->bailee}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Ссылка на изображение компании</td>
                        <td>
                            <a href="{{$company->logo_url}}}" target="_blank"><img src="{{$company->logo_url}}"
                                                                                   style="width:150px;height: 150px;"
                                                                                   class="img-thumbnail" alt="">
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('companies.edit',$company->id) }}">
                            Редактировать <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('companies.destroy', $company->id)}}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i></button>
                        </form>
                    </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection