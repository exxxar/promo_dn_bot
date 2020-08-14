@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по достижению</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('achievements.index') }}"> Назад</a>
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
                                <p>{{$achievement->title}}</p>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <p>{{$achievement->description}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Позиция в размещении</td>
                            <td>
                                <p>{{$achievement->position}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к достижению</td>
                            <td>
                                <img src="{{$achievement->ach_image_url}}" class="img-thumbnail"
                                     style="width:150px;height:150px;" alt=""> "
                            </td>
                        </tr>


                        <tr>
                            <td>Тип триггера достижения</td>
                            <td>
                                {{$achievement->trigger_type->key}}
                            </td>
                        </tr>
                        <tr>
                            <td>Значение триггера</td>
                            <td>
                                <p>{{$achievement->trigger_value}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Описание приза</td>
                            <td>
                                <p>{{$achievement->prize_description}}
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к призу</td>
                            <td>
                                <img src="{{$achievement->prize_image_url}}" class="img-thumbnail"
                                     style="width:150px;height:150px;object-fit: contain" alt=""> "

                            </td>
                        </tr>

                        </tbody>
                    </table>

            </div>
        </div>
    </div>
@endsection
