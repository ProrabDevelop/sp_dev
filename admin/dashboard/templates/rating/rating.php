<?

use core\engine\DATA;

$rating_ranges = DATA::get('rating_ranges');

/* @var $rating_range \core\engine\rating_range */

?>

<div class="d-flex justify-content-between align-items-center">

    <h2>Рапределение весов рейтинга</h2>

    <?

    $disabled = '';//disabled
    $time = new DateTime();
    $hour = intval($time->format('h'));

    if($hour >= RATING_UPDATE_TIME_MIN && $hour < RATING_UPDATE_TIME_MAX){
        $disabled = '';
    }

    if(isset($_GET['ignore'])){
        $disabled = '';
    }

    ?>

    <form method="post">
        <button type="submit" name="action" value="refresh_rating" class="btn btn-danger" <?= $disabled; ?>>Запустить перерасчет</button>
    </form>


</div>
<br>
<div class="alert alert-warning d-flex justify-content-between"><span>Перерасчет доступен с <?=RATING_UPDATE_TIME_MIN?> до <?=RATING_UPDATE_TIME_MAX?> утра, по времени сервера.</span><span>Текущее время: <?= $time->format('H:i')?></span></div>

<hr>
<form method="post">
    <table class="table table-hover">
        <tr>
            <th>Параметр</th>
            <th>Значение</th>
        </tr>

        <? foreach ($rating_ranges as $param => $rating_range){?>
            <tr>
                <td><?= $rating_range->ru_name?></td>
                <td>
                    <input type="range" name="<?= $param?>" class="form-range rating_range"
                           min="0" max="100" value="<?= $rating_range->value?>" oninput="document.getElementById('<?= $param?>').innerText = this.value">
                </td>
                <td width="100" id="<?= $param?>"><?= $rating_range->value?></td>
            </tr>
        <?}?>

    </table>

    <div class="row px-3">
        <div class="offset-md-9 col-3">
            <input type="submit" class="btn btn-success w-100" value="Сохранить">
        </div>
    </div>

</form>


