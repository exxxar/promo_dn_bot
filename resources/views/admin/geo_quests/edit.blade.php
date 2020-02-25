@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение акции</h2>
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


                <form method="post" action="{{ route('geo_quests.update',$article->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">

                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок</td>
                            <td>
                                <input type="text" class="form-control" name="title" value="{{$quest->title}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание квеста</td>
                            <td>
                                <textarea class="form-control" name="description"
                                          required>{{$quest->description}}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Изображение к квесту (опционально)
                                @if($quest->image_url!=null)
                                    <img src="{{$quest->image_url}}" class="img-thumbnail" alt="">
                                @endif
                            </td>
                            <td>
                                <input type="url" class="form-control" name="image_url" value="{{$quest->image_url??''}}"
                                       required>

                            </td>
                        </tr>
                        <tr>
                            <td>Позиция в выдаче</td>
                            <td>
                                <input type="number" class="form-control" name="position" value="{{$quest->position}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Бонус за выолнение</td>
                            <td>
                                <input type="number" class="form-control" name="reward_bonus" alue="{{$quest->reward_bonus}}" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Дата и время начала гео-задания</td>
                            <td>
                                <input type="datetime-local"
                                       value="{{$quest->start_at->format("Y-m-d").'T'.$quest->start_at->format("H:m:s")}}"
                                       name="start_at"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания гео-задания</td>
                            <td>
                                <input type="datetime-local"
                                       value="{{$quest->end_at->format("Y-m-d").'T'.$quest->end_at->format("H:m:s")}}"
                                       name="end_at"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Отображение</td>
                            <td>


                                <input type="radio" name="is_visible" value="0" required
                                       {{!$quest->is_active?'checked':''}}
                                       id="is_visible_1">
                                <label for="is_visible_1">Не отображать</label>

                                <input type="radio" name="is_visible" value="1" required
                                       {{$quest->is_active?'checked':''}}
                                       id="is_visible_2">
                                <label for="is_visible_2">Отображать</label>


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


            </div>
        </div>
    </div>
@endsection