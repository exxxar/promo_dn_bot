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
                @isset($companies)
                    @foreach($companies as $company)
                    <h2>{{$company->title}}</h2>
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
                        @foreach($company->promotions()->paginate(10) as $key => $promotion)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('promotions.show',$promotion->id) }}">
                                        {{$promotion->title}}</a>


                                </td>


                                <td>{{$promotion->description}}</td>

                                <td>
                                    <a class="btn btn-link" href="{{ route('promotions.edit',$promotion->id) }}" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a class="btn btn-link" href="{{ route('promotions.copy',$promotion->id) }}" title="Копировать">
                                        <i class="far fa-copy"></i>
                                    </a>

                                    <a class="btn btn-link" href="{{ route('promotions.channel',$promotion->id) }}" title="Отправить в канал">
                                        <i class="fab fa-telegram"></i>
                                    </a>
                                    <form action="{{ route('promotions.destroy', $promotion->id)}}" method="post" >
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit" title="Удалить безвозвратно"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                            {{ $companies->promotions()->links() }}
                    @endforeach
                    {{ $companies->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
