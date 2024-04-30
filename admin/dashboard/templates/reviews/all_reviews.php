<?php

use core\engine\DATA;

/* @var $review \app\std_models\review */
$reviews = DATA::get('reviews');


?>


<h2>Модерация отзывов</h2>
<hr>
<?
if(!empty($reviews)){

    foreach ($reviews as $review){?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><?= $review->reviewer_name?></span>

                <div><?= $review->render_fa_stars() ?></div>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-4 p-3">
                        <h5>Отзыва:</h5>
                        <?= $review->content;?>
                    </div>
                    <div class="col-4 p-3">
                        <h5>Жалоба:</h5>
                        <?= $review->complaint;?>
                    </div>
                    <div class="col-4 p-3 d-flex flex-column justify-content-center">
                        <form method="post">
                            <button type="submit" name="delete_review" value="<?= $review->id; ?>" class="btn btn-danger w-100">Удалить отзыв</button>
                            <p class="my-2 text-center">Или</p>
                            <button type="submit" name="delete_complaint" value="<?= $review->id; ?>" class="btn btn-warning w-100">Удалить жалобу</button>
                        </form>
                    </div>

                </div>

            </div>

            <div class="card-footer">

                <div class="d-flex justify-content-between">
                    <div>id заказчика: <?= $review->user_id?></div>
                    <div>id мастера: <?= $review->master_id?></div>
                    <div>Время отзыва: <?= (new DateTime())->format('d.m.Y H:s:i')?></div>
                </div>
            </div>

        </div>

    <?}

    ?><br><?

    \Core\Engine\pagination::view();

}else{
    echo '<p>Нет отзывов для модерации</p>';
}

?>
