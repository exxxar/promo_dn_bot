@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по мероприятию</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('events.index') }}"> Назад</a>

                        </div>


                    </div>
                </div>


                <form method="post" action="{{ route('events.store') }}">
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
                                <h1>{{$event->title}}</h1>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <p>{{$event->description}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Позиция в размещении</td>
                            <td>
                                <p>{{$event->position}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к мероприятию</td>
                            <td>
                                <a href="{{$event->event_image_url}}}" target="_blank"><img
                                            src="{{$event->event_image_url}}"
                                            style="width:150px;height: 150px;"
                                            class="img-thumbnail" alt="">
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td>Дата и время начала акции</td>
                            <td>
                                <p>{{$event->start_at}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания акции</td>
                            <td>
                                <p>{{$event->end_at}}</p>
                            </td>
                        </tr>


                        <tr>
                            <td>Компания</td>
                            <td>
                                <p>{{$event->company->title}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('events.edit',$event->id) }}">
                                    Редактировать <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('events.destroy', $event->id)}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i></button>
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