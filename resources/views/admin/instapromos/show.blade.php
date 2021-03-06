@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации об акции</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('instapromos.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('instapromos.edit',$promo->id) }}">
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
                            <p>{{$promo->title}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Описание</td>
                        <td>
                            <p>{{$promo->description}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Картинка к акцие</td>
                        <td>

                            <img src="{{$promo->photo_url}}" class="img-thumbnail" alt=""
                                 style="object-fit: cover;width:200px;height:200px;">
                        </td>
                    </tr>

                    <tr>
                        <td>Бонус за выполнение условий акции</td>
                        <td>

                            <p>{{$promo->promo_bonus}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Позиция в выдаче</td>
                        <td>

                            <p>{{$promo->position}}</p>
                        </td>
                    </tr>


                    <tr>
                        <td>Доступность акции</td>
                        <td>
                            <p>{{$promo->is_active?"Активно":"Не активно"}}</p>
                        </td>
                    </tr>


                    <tr>
                        <td>Компания</td>
                        <td>
                            <p>{{$promo->company->title}}</p>
                        </td>
                    </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
