<h2>Создание специализации</h2>

<hr>

<form method="post">
    <table class="table table-hover">
        <tr>
            <th>Им.падеж</th>
            <th>Им.падеж мн.число</th>
            <th>Вин.падеж</th>
            <th>Работы</th>
        </tr>
        <tr>
            <th>Сантехник</th>
            <th>Сантехники</th>
            <th>Сантехников</th>
            <th>Сантехнические работы</th>
        </tr>

        <tr>
            <td><input type="text" class="form-control" name="name" required></td>
            <td><input type="text" class="form-control" name="name_many" required></td>
            <td><input type="text" class="form-control" name="name_of" required></td>
            <td><input type="text" class="form-control" name="name_cat" required></td>
        </tr>

    </table>

    <div class="row px-3">

        <div class="offset-md-9 col-3">
            <input type="submit" class="btn btn-success w-100" value="Создать специализацию">
        </div>


    </div>

</form>


