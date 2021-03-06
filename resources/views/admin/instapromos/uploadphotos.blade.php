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

                <h1>Загруженные фотографии</h1>
                @isset($uploadphotos)

                    <div class="row">

                        @foreach($uploadphotos as $key => $photo)
                            <div class="col">
                                <div class="wrapper" style="padding: 10px">
                                    <div class="card" style="width:250px;">
                                        <!-- Изображение -->
                                        <img class="card-img-top" src="{{$photo->url}}" style="width:100%;height:250px;object-fit: contain;">
                                        <!-- Текстовый контент -->
                                        <div class="card-body">
                                            <h5><a href="{{route("users.show",$photo->user_id)}}">{{$photo->user->name??$photo->user->telegram_chat_id}}</a></h5>

                                            <form action="{{ route('users.uploadphotos.accept', $photo->id)}}"
                                                  method="post">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <select name="insta_promotions_id" id="insta_promotions_id"
                                                                class="form-control" required>
                                                            @foreach($instapromos as $promo)
                                                                <option value="{{$promo->id}}">{{$promo->title}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-sm-6">
                                                        <button class="btn btn-success" type="submit">
                                                            Подтвердить
                                                        </button>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <a class="btn btn-danger"
                                                           href="{{route("users.uploadphotos.decline",$photo->id)}}">
                                                            Отменить
                                                        </a>
                                                    </div>
                                                </div>
                                            </form>


                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{ $uploadphotos->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
