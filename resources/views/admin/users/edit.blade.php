@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Редактирование информации о пользователе</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Назад</a>
                        </div>


                    </div>
                </div>

                <form method="post" action="{{ route('users.update',$user->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>

                        <tr>
                            <td>Роль пользователя</td>
                            <td>
                                <select name="is_admin" class="form-control">
                                    <option value="0">Пользователь</option>
                                    <option value="1">Администратор</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Компании пользователя (только для администраторов)</td>
                            <td>
                                <select name="company_ids[]" class="form-control" multiple>
                                    @foreach($user->companies as $company)
                                        <option value="{{$company->id}}">{{$company->title}}</option>
                                        @endforeach
                                </select>
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