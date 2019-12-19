<form action="{{route("users.search")}}" method="post">
    @csrf
    <h2>Поиск пользователя</h2>
    <div class="form-row">
        <div class="col form-group">
            <label for="phone">Телефон пользователя</label>
            <input class="form-control phone" id="phone" data-target="#livesearch-2" name="phone">
            <div id="livesearch-2"></div>
        </div>
    </div>

    <div class="form-row">
        <div class="col form-group">
            <button class="btn btn-primary" name="submit">Найти</button>
        </div>
    </div>
</form>