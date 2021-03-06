<form action="{{route("users.cashback.add")}}" method="post">
    @csrf
    <h2>Добавить кэшбэк</h2>
    <div class="form-row">
        <div class="col-sm-12 col-md-4 form-group">
            <label for="money_in_check">Сумма в чеке,руб.</label>
            <input class="form-control" id="money_in_check" type="number" min="0" name="money_in_check" required>
        </div>

        <div class="col-sm-12 col-md-4 form-group">
            <label for="check_info">Номер чека</label>
            <input class="form-control" id="check_info" name="check_info" required>
        </div>

        <div class="col-sm-12 col-md-4 form-group">
            <label for="user_phone">Телефон:+38(071)123-45-67 </label>
            <input class="form-control"  data-target="#livesearch" id="user_phone" name="user_phone" value="" required>
            <div id="livesearch"></div>
        </div>


    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-12 form-group">
            <label for="user_phone">Выбор компании</label>
            <select name="company_id" class="form-control" required>
                @foreach($current_user->companies as $company)
                    <option value="{{$company->id}}">{{$company->title}}</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4 form-group">
            <button class="btn btn-primary" name="submit">Добавить</button>
        </div>
    </div>
</form>
