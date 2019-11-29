<form action="{{route("users.announce")}}" method="post">
    @csrf
    <h2>Мгновенное оповещение всем пользователям</h2>
    <div class="form-row">
        <div class="col form-group">
            <label for="announce_title">Заголовок оповещения</label>
            <input class="form-control" id="announce_title" name="announce_title">
        </div>


        <div class="col form-group">
            <label for="announce_url">Изображение к оповещению(ссылка)</label>
            <input class="form-control" id="announce_url" placeholder="https://example.com" type="url" name="announce_url">
        </div>
    </div>

    <div class="form-row">
        <div class="col form-group">
            <label for="announce_message">Текст оповещения</label>
            <textarea class="form-control" id="announce_message" name="announce_message"></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="col form-group">
            <button class="btn btn-primary" name="submit">Найти</button>
        </div>
    </div>
</form>