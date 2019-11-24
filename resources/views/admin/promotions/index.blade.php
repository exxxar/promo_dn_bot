@extends('layouts.app')

@section("content")
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

    @isset($promotions)
        <table class="table mt-2">

            <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Заголовок</th>
                <th scope="col">Описание</th>
                <th scope="col">Изображение</th>
                <th scope="col">Начало</th>
                <th scope="col">Окончание</th>
                <th scope="col">Количество активаций</th>
                <th scope="col">Действие</th>

            </tr>
            </thead>
            <tbody>
            @foreach($promotions as $key => $promotion)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td><a href="{{ route('promotions.show',$promotion->id) }}">
                            {{$promotion->title}}</a>
                        <a class="btn btn-link" href="{{ route('promotions.edit',$promotion->id) }}">
                            <i class="fas fa-edit"></i>
                        </a>

                    </td>
                    <td>{{$promotion->description}}</td>
                    <td><img class="img-thumbnail" style="width:150px;height:150px;" src="{{$promotion->promo_image_url}}" alt=""></td>

                    <td>{{$promotion->start_at}}</td>
                    <td>{{$promotion->end_at}}</td>
                    <td>{{$promotion->activation_count}}</td>


                    <td>
                        <form action="{{ route('companies.destroy', $promotion->id)}}" method="post">
                            @csrf
                            @method('DELETE')
                            <a class="btn btn-link" type="submit"><i class="fas fa-times"></i></a>
                        </form>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

        {{ $promotions->links() }}
    @endisset

@endsection