@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Добавление нового Гео-задания</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('geo_quests.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('geo_quests.store') }}">
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
                                <input type="text" class="form-control" name="title" value="" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание квеста</td>
                            <td>
                                <textarea class="form-control" name="description" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Изображение к квесту (опционально)</td>
                            <td>
                                <input type="url" class="form-control" name="image_url" value="" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Позиция в выдаче</td>
                            <td>
                                <input type="number" class="form-control" name="position"  required>
                            </td>
                        </tr>
                        <tr>
                            <td>Бонус за выолнение</td>
                            <td>
                                <input type="number" class="form-control" name="reward_bonus"  required>
                            </td>
                        </tr>

                        <tr>
                        <td>Дата и время начала гео-задания</td>
                        <td>
                            <input type="datetime-local" value="" name="start_at" class="form-control" required>
                        </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания гео-задания</td>
                            <td>
                                <input type="datetime-local" value="" name="end_at" class="form-control" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Отображение</td>
                            <td>

                                <input type="radio" name="is_active" value="0" required id="is_active_1">
                                <label for="is_active_1">Не отображать</label>

                                <input  type="radio" name="is_active" value="1" required id="is_active_2">
                                <label for="is_active_2">Отображать</label>

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
