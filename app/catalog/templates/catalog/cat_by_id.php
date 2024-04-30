<?php

use core\engine\DATA;

$services = DATA::get('services');
$count = DATA::get('count');
?>

<div class="finder_form_catalog">
    <?php get_block('finder_form'); ?>
</div>

<?php if ($services): ?>
    <div class="count_and_sort_panel">
        <div class="services_count"><?= $count ?> найдено</div>

        <span class="sort_description"></span>
        <? get_block('sorter_filter');?>
    </div>


    <div class="service-list">
        <?php get_block('services'); ?>
    </div>

<?php else: ?>
    <div class="catalog-empty">
        <img src="<?= URL ?>assets/img/icons/find.svg" alt="" width="64" height="64">
        <p class="catalog-empty-heading">Специалисты не найдены</p>
        <p class="catalog-empty-description">Попробуйте скорректировать поиск, изменив специальность и регион</p>
    </div>
<?php endif; ?>



