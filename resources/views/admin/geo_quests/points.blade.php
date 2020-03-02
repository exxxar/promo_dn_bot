@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Добавление контрольных точек к Гео-заданию</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('geo_quests.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('geo_quests.points.store',$quest->id) }}">
                    @csrf
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Квестовая точка</th>
                        <th>Номер точки в квесте</th>
                        <th>Последний в цепочке</th>
                        </thead>
                        <tbody>
                        @foreach($quest->positions as $position)
                            <tr>
                                <td>
                                    <select class="form-control" name="point[]">
                                        <option value="">Не задано</option>
                                        @foreach($points as $point)
                                            @if ($position->id==$point->id)
                                                <option value="{{$point->id}}" selected>{{$point->title}}</option>
                                            @else
                                                <option value="{{$point->id}}">{{$point->title}}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="position[]"
                                           value="{{$position->position}}">
                                </td>
                                <td>
                                    <label class="c-switch c-switch-label c-switch-pill c-switch-opposite-primary">
                                        <input class="c-switch-input" type="checkbox"
                                               name="is_last[]" {{$position->is_last?"checked":""}}>
                                        <span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                    </label>
                                </td>
                            </tr>
                        @endforeach

                        @if (count($quest->positions)<10)
                            @for($i=0;$i<10-count($quest->positions);$i++)
                                <tr>
                                    <td>
                                        <select class="form-control" name="point[]">
                                            <option value="">Не задано</option>
                                            @foreach($points as $point)
                                                <option value="{{$point->id}}">{{$point->title}}</option>

                                            @endforeach

                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="position[]"
                                               value="0">
                                    </td>
                                    <td>
                                        <label class="c-switch c-switch-label c-switch-pill c-switch-opposite-primary">
                                            <input class="c-switch-input" type="checkbox"
                                                   name="is_last[]">
                                            <span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                        </label>
                                    </td>
                                </tr>
                            @endfor
                        @endif
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary">Добавить</button>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
