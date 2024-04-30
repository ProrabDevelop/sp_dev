<h2>Редактор пользователя</h2>
<br>

<?

/* @var \app\std_models\user $user */
$user = \core\engine\DATA::get('user_data');



?>

<form method="post" class="row">
    <div class="col-md-6">

        <table class="table table-hover" style="table-layout: fixed;">

            <tr>
                <td>Activity state</td>
                <td><input class="form-control" type="text" name="activity_state" value="<?= $user->activity_state ?>"></td>
            </tr>
            <tr>
                <td>Имя Фамилия</td>
                <td><input class="form-control" type="text" name="name" value="<?= $user->name ?>"></td>
            </tr>
            <tr>
                <td>E-mail</td>
                <td><input class="form-control" type="text" name="mail" value="<?= $user->mail ?>"></td>
            </tr>
            <tr>
                <td>ID Города</td>
                <td><input class="form-control" type="text" name="city" value="<?= $user->city ?>"></td>
            </tr>

        </table>




    </div>


    <div class="col-md-6">

        <table class="table table-hover" style="table-layout: fixed;">
            <tr>
                <td>ID</td>
                <td><input class="form-control" type="text" value="<?= $user->id ?>" disabled></td>
            </tr>
            <tr>
                <td>Телефон</td>
                <td><input class="form-control" type="text" value="<?= formatphone($user->phone) ?>" disabled></td>
            </tr>
            <tr>
                <td>Роль</td>
                <td><input class="form-control" type="text" value="<?= $user->role ?>" disabled></td>
            </tr>
            <tr>
                <td>Статус</td>
                <td><input class="form-control" type="text" value="<?= $user->status ?>" disabled></td>
            </tr>
            <tr>
                <td>День рождения</td>
                <td><input class="form-control" type="text" value="<?= $user->birthday ?>" disabled></td>
            </tr>
            <tr>
                <td>E-mail Подписка</td>
                <td><input class="form-control" type="text" value="<?= $user->email_subscribe ?>" disabled></td>
            </tr>
            <tr>
                <td>Зарегистрирован</td>
                <td><input class="form-control" type="text" value="<?= $user->reg_time ?>" disabled></td>
            </tr>
            <tr>
                <td>Запуск сессии</td>
                <td><input class="form-control" type="text" value="<?= $user->last_visit ?>" disabled></td>
            </tr>
            <tr>
                <td>Последний IP</td>
                <td><input class="form-control" type="text" name="last_ip" value="<?= $user->last_ip ?>" disabled></td>
            </tr>
        </table>

    </div>

    <button class="btn btn-success col-md-3 offset-md-9">Сохранить</button>

</form>

