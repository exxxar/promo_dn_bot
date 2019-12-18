@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Редактировани категории</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('categories.index') }}"> Назад</a>
                        </div>

                        @if (count($errors) > 0)
                            <div class="alert alert-danger mt-2">
                                <strong>Упс!</strong> Возникли ошибки при заполнении полей.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                    </div>
                </div>


                <form method="post" action="{{ route('categories.update',$category->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок</td>
                            <td>

                                <input type="text" name="title" value="{{$category->title}}" class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                    <textarea name="description" class="form-control" required>{{$category->description}}
                    </textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Позиция в размещении</td>
                            <td>
                                <input type="number" value="{{$category->position}}" min="0" name="position"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к категории</td>
                            <td>
                                <input type="url" name="image_url" value="{{$category->image_url}}"
                                       placeholder="https://example.com" pattern="https://.*" size="200"
                                       class="form-control" required>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary">Изменить</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection