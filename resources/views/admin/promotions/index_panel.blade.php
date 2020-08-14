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

                            <div class="card" style="width: 300px">

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
