@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение акции</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('articles.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('articles.update',$article->id) }}">
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
                                <input type="text" class="form-control" name="url" value="{{$article->url}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Позиция в выдаче</td>
                            <td>
                                <input type="number" class="form-control" name="position" value="{{$article->position}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Раздел</td>
                            <td>
                                <select name="part" id="part" class="form-control">
                                    @foreach($parts as $part)
                                        @if ($part->value==$article->part->value)
                                            <option value="{{$part->value}}" selected>{{$part->key}}</option>
                                        @else
                                            <option value="{{$part->value}}">{{$part->key}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Отображение</td>
                            <td>


                                <input type="radio" name="is_visible" value="0" required
                                       {{!$article->is_visible?'checked':''}}
                                       id="is_visible_1">
                                <label for="is_visible_1">Не отображать</label>

                                <input  type="radio" name="is_visible" value="1" required
                                        {{$article->is_visible?'checked':''}}
                                        id="is_visible_2">
                                <label for="is_visible_2">Отображать</label>


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
