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
                            <a class="btn btn-primary" href="{{ route('ingredients.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('ingredients.update',$prize->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">

                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Название</td>
                            <td>
                                <input type="text" class="form-control" name="title" value="{{$prize->title}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea name="description" class="form-control" id="description" cols="30"
                                          rows="10">{{$prize->description}}</textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Картинка к призу (ссылка)</td>
                            <td>

                                <input type="text" class="form-control" name="image_url" value="{{$prize->image_url}}"
                                       required>


                            </td>
                        </tr>

                        <tr>
                            <td>Компания</td>
                            <td>
                                <select class="form-control" name="company_id" required>
                                    @foreach($companies as $company)
                                        @if ($prize->company_id==$company->id)
                                            <option value="{{$company->id}}" selected>{{$company->title}}</option>
                                        @else
                                            <option value="{{$company->id}}">{{$company->title}}</option>
                                            @endforeach
                                            @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Количество доступных призов</td>
                            <td>

                                <input type="number" min="0" class="form-control" name="summary_activation_count" value="{{$prize->summary_activation_count}}" required>
                            </td>
                        </tr>


                        <tr>
                            <td>Доступность приза</td>
                            <td>
                                <input type="checkbox" name="is_active" class="form-control" {{$prize->is_active?"checked":""}}>
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
