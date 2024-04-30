<?


$filters = [
    'rating_desc' => 'Высокий рейтинг',
    'rating_asc' => 'Низкий рейтинг',
    //'reviews_asc' => 'Много отзывов',
    //'reviews_desc' => 'Мало отзывов',
];

if(!isset($_GET['sort_by'])){
    $current_filter = $filters['rating_desc'];
}else{

    if(!isset($filters[$_GET['sort_by']])){
        $current_filter = $filters['rating_desc'];
    }else{
        $current_filter = $filters[$_GET['sort_by']];
    }

}



?>

<div class="services_sort_menu_wrapper">
    <div class="dropdown_title">
        <span><?= $current_filter ?></span>
        <img src="<?= URL ?>assets/img/icons/sort.svg" alt="" />
    </div>
    <div class="dropdown_content">

        <? foreach ($filters as $value => $name): ?>
            <a href="?sort_by=<?= $value ?>" class="<?= $current_filter === $name ? 'active' : '' ?>">
                <?= $name ?>
                <img src="<?= URL ?>assets/img/icons/check.svg" alt="" />
            </a>
        <? endforeach; ?>



    </div>
</div>
