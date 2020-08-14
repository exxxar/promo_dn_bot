@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по статье</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('articles.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('articles.edit',$article->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                        </div>


                    </div>
                </div>


                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Ссылка</td>
                            <td>
                                <a href="{{$article->url}}" target="_blank">{{$article->url}}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Позиция</td>
                            <td>
                                <p>{{$article->position}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Раздел</td>
                            <td>
                                <p>{{$article->part->key}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Отображение</td>
                            <td>
                                @if ($article->is_visible==0)
                                    <i class="fas fa-eye-slash"></i>
                                @else
                                    <i class="fas fa-eye"></i>
                                @endif
                            </td>
                        </tr>


                        <tr>
                            <td></td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('articles.edit',$article->id) }}">
                                    Редактировать <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('articles.destroy', $article->id)}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        </tbody>
                    </table>

            </div>
        </div>
    </div>
@endsection
