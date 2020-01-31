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
                        <a class="btn btn-primary" href="{{route("categories.create")}}">Новая категория</a>
                    </div>
                </div>

                <h1>Категории</h1>
                @isset($categories)
                    <div class="row justify-content-around">
                        @foreach($categories as $key => $category)

                            <div class="card card-accent-success" style="width: 300px">
                                <div class="card-header ">
                                    <a class="btn btn-link" href="{{ route('categories.edit',$category->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form class="btn btn-link" action="{{ route('categories.destroy', $category->id)}}"
                                          method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                                <img class="card-img-top" src="{{$category->promo_image_url}}">
                                <div class="card-body">
                                    <h5 class="card-title">{{$category->title}}</h5>
                                    <a class="btn btn-primary" href="{{ route('categories.show',$category->id) }}">
                                        Подробнее</a>
                                    <a class="btn btn-info" href="{{ route('promotions.in_category',$category->id) }}">
                                        Акции в категории</a>
                                </div>
                            </div>




                        @endforeach
                    </div>
                    {{ $categories->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
