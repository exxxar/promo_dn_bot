@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение гео-задания</h2>
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


                <form method="post" action="{{ route('geo_quests.update',$quest->id) }}">
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
                                <input type="text" class="form-control" name="title" value="{{$quest->title}}" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание квеста</td>
                            <td>
                                <textarea class="form-control" name="description"
                                          required>{{$quest->description}}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Изображение к квесту (опционально)

                            </td>
                            <td>
                                @if($quest->image_url!=null)
                                    <img src="{{$quest->image_url}}" class="img-thumbnail"
                                         style="width: 200px;height:200px;object-fit: contain" alt="">
                                @endif
                                <input type="url" class="form-control mt-2" name="image_url"
                                       value="{{$quest->image_url??''}}"
                                       required>

                            </td>
                        </tr>
                        <tr>
                            <td>Позиция в выдаче</td>
                            <td>
                                <input type="number" class="form-control" name="position" value="{{$quest->position}}"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Бонус из акции</td>
                            <td>
                                <select class="form-control" name="promotion_id" id="promotion_id">
                                    <option value="" selected>Не выбрано</option>
                                    @foreach($promotions as $promotion)
                                        @if ($quest->promotion!=null)
                                            @if($promotion->id==$quest->promotion->id)
                                                <option value="{{$promotion->id}}"
                                                        selected>{{$promotion->title}}</option>
                                            @else
                                                <option value="{{$promotion->id}}">{{$promotion->title}}</option>
                                            @endif
                                        @else
                                            <option value="{{$promotion->id}}">{{$promotion->title}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Бонус за выолнение</td>
                            <td>
                                <input type="number" class="form-control" name="reward_bonus"
                                       value="{{$quest->reward_bonus}}" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Дата и время начала гео-задания</td>
                            <td>
                                <input type="datetime-local"
                                       value="{{$quest->start_at->format("Y-m-d").'T'.$quest->start_at->format("H:m:s")}}"
                                       name="start_at"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания гео-задания</td>
                            <td>
                                <input type="datetime-local"
                                       value="{{$quest->end_at->format("Y-m-d").'T'.$quest->end_at->format("H:m:s")}}"
                                       name="end_at"
                                       class="form-control"
                                       required>
                            </td>
                        </tr>

                        <tr>
                            <td>Отображение</td>
                            <td>


                                <label class="c-switch c-switch-label c-switch-pill c-switch-opposite-primary">
                                    <input class="c-switch-input" type="checkbox"
                                           name="is_active" {{!$quest->is_active?'checked':''}}>
                                    <span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>Компания, от которой квест</td>
                            <td>
                                <select class="form-control" name="company_id" id="company_id" required>
                                    @foreach($companies as $company)
                                        @if ($company->id==$quest->company->id)
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
