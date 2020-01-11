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
                        <a class="btn btn-primary" href="{{route("achievements.create")}}">Новое достижение</a>
                    </div>
                </div>

                    <h1>Достижения</h1>
                @isset($achievements)
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
                        @foreach($achievements as $key => $achievement)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('achievements.show',$achievement->id) }}">
                                        {{$achievement->title}}</a>
                                    <a class="btn btn-link" href="{{ route('achievements.edit',$achievement->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                </td>
                                <td>{{$achievement->description}}</td>


                                <td>

                                    <a class="btn btn-link" href="{{ route('achievements.channel',$achievement->id) }}" title="Отправить в канал">
                                        <i class="fab fa-telegram"></i>
                                    </a>

                                    <form action="{{ route('achievements.destroy', $achievement->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $achievements->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
