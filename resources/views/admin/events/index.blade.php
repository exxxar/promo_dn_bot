@extends('layouts.app')

@section("content")

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ $message }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-4">
                        <a class="btn btn-primary" href="{{route("events.create")}}">Новое мероприятие</a>
                    </div>
                </div>

                    <h1>События\мероприятия</h1>
                @isset($events)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Заголовок</th>
                            <th scope="col">Описание</th>
                            <th scope="col">Действие</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $key => $event)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('events.show',$event->id) }}">
                                        {{$event->title}}</a>
                                    <a class="btn btn-link" href="{{ route('events.edit',$event->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                </td>
                                <td>{{$event->description}}</td>


                                <td>
                                    <a class="btn btn-link" href="{{ route('events.channel',$event->id) }}" title="Отправить в канал">
                                        <i class="fab fa-telegram"></i>
                                    </a>

                                    @if($event->promo_id!=null)
                                        <a class="btn btn-link" href="{{ route('promotions.show',$event->promo_id) }}" title="Связано с акцией">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        @endif
                                    <form action="{{ route('events.destroy', $event->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $events->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
