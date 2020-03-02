@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по гео-точке</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('geo_positions.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('geo_positions.edit',$position->id) }}">
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
                        <td>Название точки</td>
                        <td>
                            <p>{{$position->title}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Краткое описание положения</td>
                        <td>
                            <p>{{$position->description}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Фотография квестовой точки</td>
                        <td>
                            <img src="{{$position->url}}" class="img-thumbnail mb-2"
                                 style="width:200px;height: 200px;object-fit: cover;" alt="">

                        </td>
                    </tr>

                    <tr>
                        <td>latitude & longitude</td>
                        <td>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <p>{{$point->latitude}}</p>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <p>{{$position->longitude}}</p>
                                </div>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td>Локальный радиус видимости, км (1м = 0.001км)</td>
                        <td>
                            <p>{{$position->radius}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Бонус за прохождение текущей точки</td>
                        <td>
                            <p>{{$position->local_reward}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Локальный бонус из акции</td>
                        <td>
                            @if($position->local_promotion_id!=null)
                                <p>{{$position->promotion->title}}</p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>Активация через временной промежуток</td>
                        <td>
                            {{$position->in_time_range?"Да":"Нет"}}
                        </td>
                    </tr>

                    <tr>
                        <td>Временной промежуток активации (в минутах от 1 до 60)</td>
                        <td>
                            <p>{{$promotion->range_time_value}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Время начала доступности точки</td>
                        <td>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <p>{{$promotion->time_start}}</p>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <p>{{$promotion->time_end}}</p>
                                </div>
                            </div>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
