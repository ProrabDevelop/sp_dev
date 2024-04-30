<h2>Профиль пользователя</h2>

<?php
$user = \core\engine\DATA::get('profile');
?>

<table class="table table-hover" style="table-layout: fixed;">
    <thead>
    <tr>
        <th>Поле</th>
        <th>Значение</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>ID</td>
        <td><?= $user->get('id') ?></td>
    </tr>
    <tr>
        <td>Имя</td>
        <td><?= $user->get('name') ?></td>
    </tr>
    <tr>
        <td>Телефон</td>
        <td><?= $user->get('phone') ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><?= $user->get('mail') ?></td>
    </tr>
    <tr>
        <td>Роль</td>
        <td><?= $user->get('role') ?></td>
    </tr>
    <tr>
        <td>Статус</td>
        <td><?= $user->get('status') ?></td>
    </tr>
    <tr>
        <td>Удален</td>
        <td><?= $user->get('deleted_at') ?></td>
    </tr>
    </tbody>
</table>
