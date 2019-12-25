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

                    <h1>История оплаты</h1>

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
                                <td>
                                    @isset($payment->user)
                                    <a href="{{ route('users.show',$payment->user->id) }}">
                                        {{$payment->user->phone??$payment->user->name??$payment->user->telegram_chat_id}}
                                    </a>
                                        @endisset
                                </td>
                                <td>

                                    @isset($payment->employee)
                                    <a href="{{ route('users.show',$payment->employee->id) }}">
                                        {{$payment->employee->phone??$payment->employee->name??$payment->employee->telegram_chat_id}}
                                    </a>
                                    @endisset

                                        @if(!$payment->employee)
                                            <p>Сотрудник не указан</p>
                                        @endif
                                </td>

                                <td>{{$payment->value}} баллов.</td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $payments->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection