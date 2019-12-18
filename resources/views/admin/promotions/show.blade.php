@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по акции</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('promotions.index') }}"> Назад</a>
                            <a class="btn btn-secondary" href="#"> Активации по акции</a>
                            <a class="btn btn-link" href="{{ route('promotions.edit',$promotion->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                        </div>


                    </div>
                </div>


                <form method="post" action="{{ route('promotions.store') }}">
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
                                <h1>{{$promotion->title}}</h1>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <p>{{$promotion->description}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Позиция в размещении</td>
                            <td>
                                <input type="number" value="{{$promotion->position}}" min="0" name="position"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к акции</td>
                            <td>
                                <a href="{{$promotion->promo_image_url}}}" target="_blank"><img
                                            src="{{$promotion->promo_image_url}}"
                                            style="width:150px;height: 150px;"
                                            class="img-thumbnail" alt="">
                                </a>


                            </td>
                        </tr>


                        <tr>
                            <td>Дата и время начала акции</td>
                            <td>
                                <p>{{$promotion->start_at}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания акции</td>
                            <td>
                                <p>{{$promotion->end_at}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Колличество активаций</td>
                            <td>
                                <p>{{$promotion->activation_count}}</p>
                        </tr>
                        <tr>
                            <td>Адрес расположения</td>
                            <td>
                                <p>{{$promotion->location_address}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Координаты расположения</td>
                            <td>
                                <p>{{$promotion->location_coords}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Текст для пользователя после активации</td>
                            <td>
                                <p>{{$promotion->activation_text}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Активировать сразу после выполнения условий</td>
                            <td>
                                <p> {{$promotion->immediately_activate?"Активировать":"Не активировать"}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Реферальный бонус</td>
                            <td>
                                <p>{{$promotion->refferal_bonus}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Компания</td>
                            <td>
                                <p>{{$promotion->company->title}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Категория</td>
                            <td>
                                <p>{{$promotion->category->title}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Обработчик</td>
                            <td>
                                <p>{{$promotion->handler}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('promotions.edit',$promotion->id) }}">
                                    Редактировать <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('promotions.destroy', $promotion->id)}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection