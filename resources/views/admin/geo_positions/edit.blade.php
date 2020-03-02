@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение квестовой точки</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('geo_positions.index') }}">Назад</a>
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


                <form method="post" action="{{ route('geo_positions.update',$position->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">

                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Название точки</td>
                            <td>
                                <input type="text" class="form-control" name="title" value="{{$position->title}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Краткое описание положения</td>
                            <td>
                                <textarea type="text" maxlength="1000" class="form-control" name="description"
                                          required>{{$position->description}}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Фотография квестовой точки</td>
                            <td>
                                <img src="{{$position->url}}" class="img-thumbnail mb-2"
                                     style="width:200px;height: 200px;object-fit: cover;" alt="">
                                <input type="url" class="form-control" name="image_url" value="{{$position->image_url}}" required>
                            </td>
                        </tr>

                        <tr>
                            <td>latitude & longitude</td>
                            <td>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <input type="number" class="form-control" name="latitude"
                                               value="{{$position->latitude}}" required>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <input type="number" class="form-control" name="longitude"
                                               value="{{$position->longitude}}" required>
                                    </div>
                                </div>

                            </td>
                        </tr>

                        <tr>
                            <td>Локальный радиус видимости, км (1м = 0.001км)</td>
                            <td>
                                <input type="number" class="form-control" name="radius" value="{{$position->radius}}"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Бонус за прохождение текущей точки</td>
                            <td>
                                <input type="number" class="form-control" min="0" value="{{$position->local_reward}}"
                                       name="local_reward"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Локальный бонус из акции</td>
                            <td>
                                <select class="form-control" name="local_promotion_id" id="local_promotion_id">
                                    <option value="" selected>Не выбрано</option>
                                    @foreach($promotions as $promotion)
                                        @if ($promotion->id==$position->local_promotion_id)
                                            <option value="{{$promotion->id}}" selected>{{$promotion->title}}</option>
                                        @else
                                            <option value="{{$promotion->id}}">{{$promotion->title}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Активация через временной промежуток</td>
                            <td>

                                <label class="c-switch c-switch-label c-switch-pill c-switch-opposite-primary">
                                    <input class="c-switch-input" type="checkbox"
                                           name="in_time_range" {{$position->in_time_range?"checked":""}}>
                                    <span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>Временной промежуток активации (в минутах от 1 до 60)</td>
                            <td>
                                <input type="number" class="form-control" min="1" max="60"
                                       value="{{$promotion->range_time_value}}"
                                       name="range_time_value"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Время начала доступности точки</td>
                            <td>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <input type="time" value="{{strtotime("".$promotion->time_start)}}" name="time_start"
                                               class="form-control" required>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <input type="time" value="{{strtotime("".$promotion->time_end)}}" name="time_end"
                                               class="form-control" required>
                                    </div>
                                </div>
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
