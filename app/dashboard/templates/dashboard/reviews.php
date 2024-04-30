<?
use app\std_models\reviews_list;
use \core\engine\DATA;

$user = DATA::get('USER');
$reviews = (new reviews_list())->get_all($user->id);
$reply = true;
?>

<?php if ($reviews): ?>
    <h2 class="page_title">Отзывы</h2>
    <div class="reviews">
        <?php get_block('reviews', null, compact('reviews', 'reply')); ?>
    </div>
<?php else: ?>
    <div class="catalog-empty">
        <img src="<?= URL ?>assets/img/icons/find.svg" alt="" width="64" height="64">
        <p class="catalog-empty-heading">Список отзывов пуст</p>
        <p class="catalog-empty-description">После выполнения работы, не забывайте просить заказчиков, оставить отзыв о вашей работе.</p>
    </div>
<?php endif; ?>

