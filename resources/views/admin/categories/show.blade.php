@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Просмотр категории</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('categories.index') }}"> Назад</a>
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
                <h2>{{$category->title}}</h2>
            </td>
        </tr>
        <tr>
            <td>Описание</td>
            <td>
                <p>{{$category->description}}</p>
            </td>
        </tr>
        <tr>
            <td>Ссылка на изображение к категории</td>
            <td>
                <a href="{{$category->image_url}}}" target="_blank"><img src="{{$category->image_url}}"
                                                                         style="width:150px;height: 150px;"
                                                                         class="img-thumbnail" alt="">
                </a>
            </td>
        </tr>


        </tbody>
    </table>

@endsection