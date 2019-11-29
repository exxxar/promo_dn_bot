<form action="{{route("home.qr")}}" method="post">
    @csrf
    <h2>Генерация реферальной ссылки</h2>
    <div class="form-row">

        <div class="col form-group">
            <label for="user_id">Пользователь</label>
            <select name="user_id" class="form-control" id="user_id">
                <option value="0">Нет пользователя</option>
                @foreach($users as $user)
                  {{--  @if(strlen(trim($user->phone))>0)--}}
                        <option value="{{$user->id}}">{{$user->phone}}</option>
                    {{--@endif--}}
                @endforeach
            </select>
        </div>


        <div class="col form-group">
            <label for="promotion_id">Акция</label>
            <select name="promotion_id" class="form-control" id="promo_id">
                <option value="0">Любая акция</option>
                @foreach($promotions as $promotion)
                    @if(strlen(trim($promotion->title))>0)
                        <option value="{{$promotion->id}}">{{$promotion->title}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="col form-group">
            <button class="btn btn-primary" name="submit">Сгенерировать</button>
        </div>
    </div>
</form>

@isset($qrimage)
    <h3>Сохраните данный QR-код</h3>
    <div class="row">
        <div class="col">
            <img src="{!! $qrimage !!}" class="img-thumbnail" style="width:150px;height:150px;"
                 alt="">
        </div>
    </div>
@endisset