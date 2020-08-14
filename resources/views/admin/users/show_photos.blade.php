@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр рекламных фотографий пользователя <a
                                        href="{{route("users.show",$user->id)}}">{{$user->name}}</a></h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('users.edit',$user->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                        </div>


                    </div>
                </div>



                <h1>Выполненные акции</h1>
                @isset($uploadphotos)

                    <div class="row">

                        @foreach($uploadphotos as $key => $photo)
                            <div class="col">
                                <div class="wrapper" style="padding: 10px">
                                    <div class="card" style="width:220px;">
                                        <!-- Изображение -->
                                        <img class="card-img-top" src="{{$photo->url}}" style="width:100%;">
                                        <!-- Текстовый контент -->
                                        <div class="card-body">


                                            <div class="row">
                                                <div class="col-3">
                                                    <a class="btn btn-success" href="{{route("uploadphotos.confirm",$photo->id,true)}}">Принять</a>
                                                </div>
                                                <div class="col-3">
                                                    <a class="btn btn-warning" href="{{route("uploadphotos.confirm",$photo->id,false)}}">Отклонить</a>
                                                </div>
                                            </div>





                                            <form action="{{ route('uploadphotos.destroy', $photo->id)}}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link" type="submit">Удалить</button>
                                            </form>

                                            <form action="{{ route('uploadphotos.change.company', $photo->id)}}" method="post">
                                                @csrf
                                                <select class="form-control" name="company_id" required>
                                                    @foreach($companies as $company)
                                                            <option value="{{$company->id}}">{{$company->title}}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-link" type="submit">Обновить компанию</button>
                                            </form>

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
