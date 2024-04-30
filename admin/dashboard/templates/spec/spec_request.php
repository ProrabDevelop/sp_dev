<?

use core\engine\DATA;

$specs = DATA::get('specs');

?>

<h2>Запросы на создание специализации</h2>

<hr>

<table class="table table-hover">
    <tr>
        <th>id</th>
        <th>user_id</th>
        <th>Специализация</th>
        <th>Удалить</th>
    </tr>

    <?

    if($specs){
        foreach ($specs as $spec){?>
            <tr>
                <td><?= $spec->id ?></td>
                <td><?= $spec->user_id ?></td>
                <td><?= $spec->spec_name ?></td>
                <td><a href="<?= URL ?>spec/delete_request/<?= $spec->id ?>/" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a></td>

            </tr>
        <?}
    }else{?>
        <tr>
            <td colspan="4"><span>Новых запросов не найдено</span></td>
        </tr>
    <?}?>

</table>

<? \Core\Engine\pagination::view();?>
