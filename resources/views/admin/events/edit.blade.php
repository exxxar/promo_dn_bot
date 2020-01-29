@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение мероприятия</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('events.index') }}"> Назад</a>
                            <form action="{{ route('events.destroy', $event->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                            </form>
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


                <form method="post" action="{{ route('events.update',$event->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок</td>
                            <td>
                                <input type="text" value="{{$event->title}}" name="title" class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea name="description" class="form-control"
                                          required>{{$event->description}}</textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Позиция в размещении</td>
                            <td>
                                <input type="number" value="{{$event->position}}" min="0" name="position"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к мероприятию</td>
                            <td>
                                <input type="url" name="event_image_url" value="{{$event->event_image_url}}"
                                       placeholder="https://example.com" pattern="http://.*|https://.*" size="200"
                                       class="form-control" required>
                            </td>
                        </tr>


                        <tr>
                            <td>Дата и время начала мероприятия</td>
                            <td>
                                <p>Старая дата: {{$event->start_at}}</p>
                                <input type="datetime-local" value="{{$event->start_at}}" name="start_at"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания мероприятия</td>
                            <td>
                                <p>Старая дата: {{$event->end_at}}</p>
                                <input type="datetime-local" value="{{$event->end_at}}" name="end_at"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Выбрать компанию</td>
                            <td>
                                <select name="company_id" class="form-control" required>
                                    @foreach($companies as $compay)
                                        @if($event->company_id==$compay->id)
                                            <option value="{{$compay->id}}" selected>{{$compay->title}}</option>
                                        @else
                                            <option value="{{$compay->id}}">{{$compay->title}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Прикрепить акцию (не обязательно)</td>
                            <td>
                                <select name="promo_id" class="form-control">
                                    @foreach($promotions as $promotion)
                                        <option value="">Не выбрано</option>
                                        @if($event->promo_id==$promotion->id)
                                            <option value="{{$promotion->id}}" selected>{{$promotion->title}}</option>
                                        @else
                                            <option value="{{$promotion->id}}">{{$promotion->title}}</option>
                                        @endif
                                    @endforeach
                                </select>
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
