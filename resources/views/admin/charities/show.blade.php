@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации о благотворительной акции</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('charities.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('charities.edit',$charity->id) }}">
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
                            <p>{{$charity->title}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Описание</td>
                        <td>
                            <p>{{$charity->description}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Картинка к акцие</td>
                        <td>

                            <img src="{{$charity->image_url}}" class="img-thumbnail" alt=""
                                 style="object-fit: cover;width:200px;height:200px;">
                        </td>
                    </tr>

                    <tr>
                        <td>Позиция в выдаче</td>
                        <td>

                            <p>{{$charity->position}}</p>
                        </td>
                    </tr>


                    <tr>
                        <td>Доступность акции</td>
                        <td>
                            <p>{{$charity->is_active?"Активно":"Не активно"}}</p>
                        </td>
                    </tr>


                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
