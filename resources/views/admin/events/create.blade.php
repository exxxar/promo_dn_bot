@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Добавление нового мероприятия</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('events.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('events.store') }}">
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
                            <td>Ссылка на изображение к мероприятию</td>
                            <td>
                                <input type="url" name="event_image_url" placeholder="https://example.com"
                                       pattern="http://.*|https://.*" size="200" class="form-control" required>
                            </td>
                        </tr>


                        <tr>
                            <td>Дата и время начала мероприятия</td>
                            <td>
                                <input type="datetime-local" name="start_at" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания мероприятия</td>
                            <td>
                                <input type="datetime-local" name="end_at" class="form-control" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Выбрать компанию</td>
                            <td>
                                <select name="company_id" class="form-control" required>
                                    @foreach($companies as $compay)
                                        <option value="{{$compay->id}}">{{$compay->title}}</option>
                                    @endforeach
                                </select>
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