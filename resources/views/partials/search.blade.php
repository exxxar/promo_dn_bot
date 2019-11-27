<form action="{{route("users.search")}}" method="post">
    @csrf
    <h2>Поиск пользователя</h2>
    <div class="form-row">
        <div class="col form-group">
            <label for="phone">Телефон пользователя</label>
            <input class="form-control phone" id="phone" name="phone">
        </div>
    </div>

    <div class="form-row">
        <div class="col form-group">
            <button class="btn btn-primary" name="submit">Найти</button>
        </div>
    </div>
</form>