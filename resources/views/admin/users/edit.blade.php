@extends('layouts.app')


@section('content')
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

    <form method="post" action="{{ route('users.store') }}">
        @csrf
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
            <td></td>
            <td>
               <button class="btn btn-primary">Изменить</button>
            </td>
        </tr>
        </tbody>
    </table>
    </form>
@endsection