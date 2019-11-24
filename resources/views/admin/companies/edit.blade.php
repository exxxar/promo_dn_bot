@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Изменение компании</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('companies.index') }}"> Назад</a>
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


    <form method="post" action="{{ route('companies.store') }}">
        @csrf
        <input type="hidden" value="{{$company->phone}}" name="id">
        <table class="table mt-2">
            <thead class="thead-light ">
            <th>Параметр</th>
            <th>Значение</th>
            </thead>
            <tbody>
            <tr>
                <td>Заголовок</td>
                <td>
                    <input type="text" name="title" value="{{$company->title}}" class="form-control" required>
                </td>
            </tr>
            <tr>
                <td>Описание</td>
                <td>
                    <textarea name="description"  class="form-control" required>{{$company->description}}
                    </textarea>
                </td>
            </tr>
            <tr>
                <td>Адрес</td>
                <td>
                    <input type="text" name="address" value="{{$company->address}}" class="form-control" required>
                </td>
            </tr>
            <tr>
                <td>Телефон</td>
                <td>
                    <input type="text" name="phone" value="{{$company->phone}}" class="form-control" required>
                </td>
            </tr>
            <tr>
                <td>E-mail</td>
                <td>
                    <input type="email" name="email" value="{{$company->email}}" class="form-control" required>
                </td>
            </tr>
            <tr>
                <td>Ответственное лицо</td>
                <td>
                    <input type="text" name="bailee" value="{{$company->bailee}}" class="form-control" required>
                </td>
            </tr>
            <tr>
                <td>Ссылка на изображение компании</td>
                <td>
                    <input type="url" name="logo_url"  value="{{$company->logo_url}}" placeholder="https://example.com"  pattern="http://.*|https://.*" size="200" class="form-control" required>
                </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <button class="btn btn-primary">Изменить</button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
@endsection