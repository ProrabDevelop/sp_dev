<?

use core\engine\DATA;

$specs = DATA::get('specs');

/* @var $spec \app\std_models\speciality */

?>

<h2>Специализации зарегистрированные в системе</h2>

<div align="right">
    <a href="<?=URL?>spec/add_spec" class="btn btn-primary">Создать</a>
</div>

<hr>

<table class="table table-hover">
    <tr>
        <th>id</th>
        <th>Им.падеж</th>
        <th>Им.падеж мн.число</th>
        <th>Вин.падеж</th>
        <th>Работы</th>
    </tr>
    <tr>
        <th>Пример</th>
        <th>Сантехник</th>
        <th>Сантехники</th>
        <th>Сантехников</th>
        <th>Сантехнические работы</th>
    </tr>

    <? foreach ($specs as $spec){?>
    <tr>
        <td><?= $spec->id ?></td>
        <td><?= $spec->name ?></td>
        <td><?= $spec->name_many ?></td>
        <td><?= $spec->name_of ?></td>
        <td><?= $spec->name_cat ?></td>
    </tr>
    <?}?>



</table>

<? \Core\Engine\pagination::view();?>
