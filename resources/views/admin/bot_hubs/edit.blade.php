@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение параметров бота</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('bot_hubs.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('bot_hubs.update',$bot->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">

                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Ссылка на БОТа</td>
                            <td>
                                <input type="text" class="form-control" name="bot_url" value="{{$bot->bot_url}}"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>WebHook url</td>
                            <td>
                                <input type="url" class="form-control" name="webhook_url" value="{{$bot->webhook_url}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Ссылка на картинку для бота</td>
                            <td>
                                <input type="url" class="form-control" name="bot_pic" value="{{$bot->bot_pic}}"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea class="form-control" name="description"
                                          required>{{$bot->description}}</textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Token Production</td>
                            <td>
                                <input type="text" class="form-control" name="token_prod" value="{{$bot->token_prod}}"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Token Development</td>
                            <td>
                                <input type="text" class="form-control" name="token_dev" value="{{$bot->token_dev}}"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Баланс на счету</td>
                            <td>
                                <input type="number" class="form-control" name="money" value="{{$bot->money}}" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Тариф (за день)</td>
                            <td>
                                <input type="number" class="form-control" name="money_per_day"
                                       value="{{$bot->money_per_day}}" required>
                            </td>
                        </tr>


                        <tr>
                            <td>Отображение</td>
                            <td>
                                <label class="c-switch c-switch-label c-switch-pill c-switch-opposite-primary">
                                    <input class="c-switch-input" type="checkbox"
                                           name="is_active" {{$bot->is_active?"checked":""}}>
                                    <span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>Компания, от которой БОТ</td>
                            <td>
                                <select class="form-control" name="company_id" id="company_id" required>
                                    @foreach($companies as $company)
                                        @if ($company->id==$bot->company_id)
                                            <option value="{{$company->id}}" selected>{{$company->title}}</option>
                                        @else
                                            <option value="{{$company->id}}">{{$company->title}}</option>
                                        @endif
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
