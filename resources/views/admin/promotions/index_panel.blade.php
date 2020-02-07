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
                        <a class="btn btn-primary" href="{{route("promotions.create")}}">Новая акция</a>
                    </div>
                </div>

                <h1>Акции</h1>
                @isset($promotions)

                    @if(count($promotions)===0)
                        <h2>К сожалению, доступных для просмотра акций нет!</h2>

                    @endif
                    <div class="row justify-content-around">
                        @foreach($promotions as $key => $promotion)

                            <div class="card card-accent-success" style="width: 300px">
                                <div class="card-header ">
                                    <a class="btn btn-link" href="{{ route('promotions.edit',$promotion->id) }}"
                                       title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a class="btn btn-link" href="{{ route('promotions.copy',$promotion->id) }}"
                                       title="Копировать">
                                        <i class="far fa-copy"></i>
                                    </a>

                                    <a class="btn btn-link" href="{{ route('promotions.channel',$promotion->id) }}"
                                       title="Отправить в канал">
                                        <i class="fab fa-telegram"></i>
                                    </a>
                                    <form class="btn btn-link" action="{{ route('promotions.destroy', $promotion->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit" title="Удалить безвозвратно"><i
                                                    class="fas fa-times"></i></button>
                                    </form>
                                </div>
                                <img class="card-img-top" src="{{$promotion->promo_image_url}}">
                                <div class="card-body">
                                    <h5 class="card-title">{{$promotion->title}}</h5>
                                    <a class="btn btn-primary" href="{{ route('promotions.show',$promotion->id) }}">
                                        Подробнее</a>

                                </div>
                            </div>




                        @endforeach
                    </div>


                    {{ $promotions->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
