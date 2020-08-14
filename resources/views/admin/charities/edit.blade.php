@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение благотворительной акции</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('charities.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('charities.update',$charity->id) }}">
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
                                <input type="text" class="form-control" name="title" value="{{$charity->title}}"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea name="description" class="form-control" id="description" cols="30"
                                          rows="10">{{$charity->description}}</textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Картинка к акцие</td>
                            <td>

                                <input type="url" class="form-control" name="image_url" value="{{$charity->image_url}}"
                                       required>


                            </td>
                        </tr>

                        <tr>
                            <td>Позиция в выдаче</td>
                            <td>

                                <input type="number" min="0" class="form-control" name="position"
                                       value="{{$charity->position}}" required>
                            </td>
                        </tr>


                        <tr>
                            <td>Доступность акции</td>
                            <td>
                                <input type="checkbox" name="is_active"
                                       {{$charity->is_active?"checked":""}} class="form-control">
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
