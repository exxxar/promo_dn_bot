<form action="{{route("users.incoming_massage")}}" method="post">
    @csrf
    <h2>Приветственное сообщение</h2>
    <div class="form-row">
        <div class="col-sm-12 col-md-6 form-group">
            <label for="incoming_message">Заголовок оповещения</label>
            <textarea class="form-control" id="incoming_message" name="incoming_message">{!! $incoming_message !!}</textarea>
        </div>
    </div>


    <div class="form-row">
        <div class="col form-group">
            <button class="btn btn-primary" name="submit">Сохранить</button>
        </div>
    </div>
</form>
