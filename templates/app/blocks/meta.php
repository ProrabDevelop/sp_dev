<?php if ($meta_page = get_meta()): ?>
    <title><?= $meta_page['meta_title'] ?></title>
    <meta name="description" content="<?= $meta_page['description'] ?>">

    <?php if ($meta_page['keywords']): ?>
        <meta name="keywords" content="<?= $meta_page['keywords'] ?>">
    <?php endif; ?>
<?php endif; ?>
