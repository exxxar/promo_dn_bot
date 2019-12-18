@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Добавление нового достижения</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('achievements.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('achievements.store') }}">
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
                                <input type="text" value="" name="title" class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea name="description" class="form-control"
                                          required></textarea>
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
                                <input type="url" name="ach_image_url" value=""
                                       placeholder="https://example.com" pattern="http://.*|https://.*" size="200"
                                       class="form-control" required>
                            </td>
                        </tr>


                        <tr>
                            <td>Тип триггера достижения</td>
                            <td>
                                <select name="trigger_type" id="trigger" class="form-control">
                                    @foreach ($triggers as $trigger)
                                        <option value="{{$trigger->value}}">{{$trigger->key}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Значение триггера</td>
                            <td>
                                <input type="number" value="" min="0" name="trigger_value"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Описание приза</td>
                            <td>
                                <textarea name="prize_description" id="prize_description" cols="30" rows="10"
                                          class="form-control">

                                </textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к призу</td>
                            <td>
                                <input type="url" name="prize_image_url" value=""
                                       placeholder="https://example.com" pattern="http://.*|https://.*" size="200"
                                       class="form-control" required>
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