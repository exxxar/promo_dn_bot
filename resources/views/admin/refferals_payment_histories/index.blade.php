@extends('layouts.app')

@section("content")
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

    @isset($payments)
        <table class="table mt-2">

            <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Пользователь</th>
                <th scope="col">Сотрудник</th>
                <th scope="col">Списаное значение</th>

            </tr>
            </thead>
            <tbody>
            @foreach($payments as $key => $payment)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td><a href="{{ route('users.show',$payment->user()->id) }}">
                            {{$payment->user()->phone}}</a>
                    </td>
                    <td><a href="{{ route('users.show',$payment->employee()->id) }}">
                            {{$payment->employee()->phone}}</a>
                    </td>

                    <td>{{$payment->value}} баллов.</td>

                </tr>
            @endforeach

            </tbody>
        </table>

        {{ $payments->links() }}
    @endisset

@endsection