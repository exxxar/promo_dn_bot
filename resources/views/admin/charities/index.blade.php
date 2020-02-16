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
                        <a class="btn btn-primary" href="{{route("charities.create")}}">Новая акция</a>
                    </div>

                    <div class="col-sm-4">
                        <a class="btn btn-primary" href="{{route("charityhistories.index")}}">История донатов</a>
                    </div>

                </div>

                <h1>Благотворительные акции</h1>
                @isset($charities)

                    <div class="row">

                        @foreach($charities as $key => $charity)
                            <div class="col">
                                <div class="wrapper" style="padding: 10px">
                                    <div class="card" style="width:300px;">
                                        <!-- Изображение -->
                                        <img class="card-img-top" src="{{$charity->image_url}}" style="width:100%;">
                                        <!-- Текстовый контент -->
                                        <div class="card-body">
                                            <h5>{{$charity->title}}</h5>
                                            <p>{{$charity->description}}</p>
                                            <p> Позиция в выдаче:<span>{{$charity->position}}</span></p>
                                            <p> Всего донатов по акции:<span><a
                                                            href="{{route("charities.userson",$charity->id)}}"> {{$charity->donates}}</a></span>
                                            </p>
                                            <a href="{{ route('charities.show',$charity->id) }}"
                                               class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('charities.edit',$charity->id) }}" class="btn btn-info"><i
                                                        class="fas fa-edit"></i></a>

                                            <a class="btn btn-info"
                                               href="{{ route('charities.duplication',$charity->id) }}"
                                               title="Дублировать">
                                                <i class="far fa-copy"></i>
                                            </a>

                                            <a class="btn btn-info" href="{{ route('charities.channel',$charity->id) }}"
                                               title="Отправить в канал">
                                                <i class="fab fa-telegram"></i>
                                            </a>

                                            <form class="btn btn-link"
                                                  action="{{ route('charities.destroy', $charity->id)}}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link" type="submit">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            <div class="row">
                                                <div class="col">
                                                    <span class="badge badge-info">{{$charity->is_active?"Активно":"Не активно"}}</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{ $charities->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
