<?php

use core\engine\DATA;

$services = DATA::get('services');
?>

<?php if ($services): ?>
    <div class="service-list">
        <?php get_block('services'); ?>
    </div>
<?php else: ?>
    <div class="catalog-empty">
        <img src="<?= URL ?>assets/img/icons/find.svg" alt="" width="64" height="64">
        <p class="catalog-empty-heading">Список избранного пуст</p>
        <p class="catalog-empty-description">Вы можете добавить в избранное на странице <a href="<?= URL.'catalog/' ?>">каталога</a></p>
    </div>
<?php endif; ?>
