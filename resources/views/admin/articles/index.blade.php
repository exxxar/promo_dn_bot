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
                        <a class="btn btn-primary" href="{{route("articles.create")}}">Новая статья</a>
                    </div>
                </div>

                    <h1>Статьи</h1>
                @isset($articles)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Ссылка</th>
                            <th scope="col">Раздел</th>
                            <th scope="col">Состояние</th>
                            <th scope="col">Действие</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($articles as $key => $article)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('articles.show',$article->id) }}">
                                        {{$article->url}}</a>
                                    <a class="btn btn-link" href="{{ route('articles.edit',$article->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                </td>
                                <td>

                                    {{$article->part->key}}
                                </td>
                                <td>
                                    @if ($article->is_visible==0)
                                        <i class="fas fa-eye-slash"></i>
                                    @else
                                        <i class="fas fa-eye"></i>
                                    @endif

                                </td>

                                <td>
                                    <form action="{{ route('articles.destroy', $article->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $articles->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection