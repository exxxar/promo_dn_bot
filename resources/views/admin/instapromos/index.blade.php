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
                        <a class="btn btn-primary" href="{{route("instapromos.create")}}">Новая акция для Instagram</a>
                    </div>
                    @if($hasUploadPhotos)
                        <div class="col-sm-4">
                            <a class="btn btn-primary" href="{{route("users.uploadphotos.index")}}">Пользователи по акциям</a>
                        </div>
                    @endif
                </div>

                <h1>Акции для Instagram</h1>
                @isset($instapromos)

                    <div class="row">

                        @foreach($instapromos as $key => $promo)
                            <div class="col">
                                <div class="wrapper" style="padding: 10px">
                                    <div class="card" style="width:350px;">
                                        <!-- Изображение -->
                                        <img class="card-img-top" src="{{$promo->photo_url}}" style="width:100%;">
                                        <!-- Текстовый контент -->
                                        <div class="card-body">
                                            <h5>{{$promo->title}}</h5>
                                            <p>{{$promo->description}}</p>
                                            <p><strong>{{$promo->promo_bonus}}</strong> бонусов за выполнение</p>
                                            <p> Позиция в выдаче:<span>{{$promo->position}}</span></p>
                                            <p> Всего активаций:<span><a
                                                            href="{{route("instapromos.userson",$promo->id)}}"> {{$promo->summary}}</a></span>
                                            </p>
                                            <a href="{{ route('instapromos.show',$promo->id) }}"
                                               class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('instapromos.edit',$promo->id) }}" class="btn btn-info"><i
                                                        class="fas fa-edit"></i></a>

                                            <a class="btn btn-info"
                                               href="{{ route('instapromos.duplication',$promo->id) }}"
                                               title="Дублировать">
                                                <i class="far fa-copy"></i>
                                            </a>

                                            <a class="btn btn-info" href="{{ route('instapromos.channel',$promo->id) }}"
                                               title="Отправить в канал">
                                                <i class="fab fa-telegram"></i>
                                            </a>

                                            <form class="btn btn-link"
                                                  action="{{ route('instapromos.destroy', $promo->id)}}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link" type="submit">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            <div class="row">
                                                <div class="col">
                                                    <span class="badge badge-primary">{{$promo->company->title}}</span>
                                                    <span class="badge badge-info">{{$promo->is_active?"Активно":"Не активно"}}</span>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{ $instapromos->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
