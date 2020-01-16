@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Добавление новой компании</h2>
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
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок</td>
                            <td>
                                <input type="text" name="title" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea name="description" class="form-control" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Позиция в размещении</td>
                            <td>
                                <input type="number" value="" min="0" name="position"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Ссылка на бота</td>
                            <td>
                                <input type="url" name="telegram_bot_url" class="form-control" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>Процент CashBack</td>
                            <td>
                                <input type="number" min="0" max="100" name="cashback" value="" class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Адрес</td>
                            <td>
                                <input type="text" name="address" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Телефон</td>
                            <td>
                                <input type="text" name="phone" class="form-control phone" required>
                            </td>
                        </tr>
                        <tr>
                            <td>E-mail</td>
                            <td>
                                <input type="email" name="email" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <td>Ответственное лицо</td>
                            <td>
                                <input type="text" name="bailee" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Ссылка на изображение компании</td>
                            <td>
                                <input type="url" name="logo_url" placeholder="https://example.com"
                                       pattern="http://.*|https://.*" size="200" class="form-control" required>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary">Добавить</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

@endsection
