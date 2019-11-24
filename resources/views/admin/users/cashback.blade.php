@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Добавление кэшбэка пользователю</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('users.index') }}"> Назад</a>
            </div>


        </div>
    </div>

    <form method="post" action="{{ route('usercashback.add',$user->id) }}">
        @csrf

        <input type="hidden"  name="id" value="{{$user->id}}" >
    <table class="table mt-2">
        <thead class="thead-light ">
        <th>Параметр</th>
        <th>Значение</th>
        </thead>
        <tbody>

        <tr>
            <td>Сумма чека</td>
            <td>
                <input type="number" class="form-control" min="0" name="money_in_check" placeholder="Введите полную целую сумму из чека" required>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
               <button class="btn btn-primary">Добавить</button>
            </td>
        </tr>
        </tbody>
    </table>
    </form>
@endsection