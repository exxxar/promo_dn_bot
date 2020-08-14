@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации о призе</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('prizes.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('prizes.edit',$prize->id) }}">
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
                            <td>Название</td>
                            <td>
                              <p>{{$prize->title}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <p>{{$prize->description}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Изображение приза</td>
                            <td>

                                <img src="{{$prize->image_url}}" class="img-thumbnail" alt="" style="width:150px;height: 150px;object-fit: cover;">


                            </td>
                        </tr>

                        <tr>
                            <td>Компания</td>
                            <td>
                               <p>{{$prize->company->title}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Количество доступных призов</td>
                            <td>

                              <p> {{$prize->summary_activation_count}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Текущее количество полученных призов</td>
                            <td>

                                <p> {{$prize->current_activation_count}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Доступность приза</td>
                            <td>
                               <p> {{$prize->is_active?"Приз участвует в розыгрыше":"Приз участвует в розыгрыше"}} </p>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('prizes.edit',$prize->id) }}">
                                    Редактировать <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('prizes.destroy', $prize->id)}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        </tbody>
                    </table>
            </div>
        </div>
    </div>
@endsection
