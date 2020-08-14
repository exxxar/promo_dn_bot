@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Информация о гео-задании</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('geo_quests.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('geo_quests.edit',$quest->id) }}">
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

                        </td>
                        <td>
                            @if($quest->image_url!=null)
                                <img src="{{$quest->image_url}}" style="width:200px;height: 200px;object-fit: contain;"
                                     class="img-thumbnail" alt="">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Позиция в выдаче</td>
                        <td>
                            <p>{{$quest->position}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Бонус за выолнение</td>
                        <td>
                            <p>{{$quest->reward_bonus}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Дата и время начала гео-задания</td>
                        <td>
                            <p>{{$quest->start_at}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Дата и время окончания гео-задания</td>
                        <td>
                            <p>{{$quest->end_at}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Отображение</td>
                        <td>

                            @if ($quest->is_active)
                                <p>Отображать</p>
                            @else
                                <p>Не отображать</p>

                            @endif
                        </td>
                    </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
