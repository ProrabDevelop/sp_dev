<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" href="<?= URL?>assets/img/favicon.ico" type="image/x-icon">

    <?php get_block('meta'); ?>

    <? \core\engine\debugger::render_head()?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
    <style>
        #map{
            height: 500px;
        }
    </style>

    <link rel="stylesheet" href="<?= URL?>admin_assets/css/bootstrap.css" />
    <link rel="stylesheet" href="<?= URL?>admin_assets/css/menu.css">
    <link rel="stylesheet" href="<?= URL?>admin_assets/css/app.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="<?= URL?>admin_assets/js/core/json-viewer.js"></script>
    <link rel="stylesheet" href="<?= URL ?>admin_assets/css/json-viewer.css">



</head>
<body>



<div id="all-page" style="position: relative;">
    <? \core\engine\debugger::render()?>


