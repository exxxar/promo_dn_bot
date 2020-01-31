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

                    @foreach($events as $key => $event)


                        <div class="card card-accent-success" style="width: 300px">
                            <div class="card-header">
                                <a class="btn btn-link" href="{{ route('events.edit',$event->id) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-link" href="{{ route('events.channel',$event->id) }}"
                                   title="Отправить в канал">
                                    <i class="fab fa-telegram"></i>
                                </a>

                                @if($event->promo_id!=null)
                                    <a class="btn btn-link" href="{{ route('promotions.show',$event->promo_id) }}"
                                       title="Связано с акцией">
                                        <i class="fas fa-link"></i>
                                    </a>
                                @endif
                                <form class="btn btn-link" action="{{ route('events.destroy', $event->id)}}"
                                      method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                </form>
                            </div>
                            <img class="card-img-top" src="{{$event->event_image_url}}" >
                            <div class="card-body">
                                <h5 class="card-title">{{$event->title}}</h5>
                                <p class="card-text">{{$event->description}}</p>
                                <a class="btn btn-primary" href="{{ route('events.show',$event->id) }}">
                                    {{$event->title}}</a>
                            </div>
                        </div>

                        @endforeach

                        </tbody>
                        </table>

                        {{ $events->links() }}
                        @endisset
            </div>
        </div>
    </div>
@endsection
