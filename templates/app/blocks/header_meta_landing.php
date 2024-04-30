<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <?php get_block('meta'); ?>

    <? \core\engine\debugger::render_head()?>
    <link rel="stylesheet" href="<?= URL?>assets/css/app.css">
    <link rel="stylesheet" href="<?= URL?>assets/css/planshet.css">
    <link rel="stylesheet" href="<?= URL?>assets/css/header-home-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?= URL?>assets/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= URL?>assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= URL?>assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= URL?>assets/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="<?= URL?>assets/img/favicon/safari-pinned-tab.svg" color="#000000">
    <link rel="shortcut icon" href="<?= URL?>assets/img/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="<?= URL?>assets/img/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <script>
        window.URL = '<?= URL?>';
        window.API = '<?= API_URL?>';
        <? if(\core\engine\AUTH::init()->is_auth()){?>
        window.UID = '<?= \core\engine\AUTH::init()->user->id ?>'
        <?}?>
    </script>

</head>
<body class="landing_page">
<? \core\engine\debugger::render()?>
<div id="app">



