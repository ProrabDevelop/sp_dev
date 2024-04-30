<?php

use core\engine\API;

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');

define('ROOT', dirname(__DIR__));

const CORE = ROOT . '/core/';
const CONFIG = ROOT . '/config/';
const WEB = ROOT . '/web/';

const ROOTPANEL = CORE . 'rootpanel/';

require_once(CORE.'helpers/base_helpers.php');
require_once(CORE.'helpers/phone.php');

$sub = get_sub_domain();

if($sub){

    if($sub == 'api'){
        header('Content-Type: application/json; charset=utf-8');
    }

    define('SUB_URL', $_SERVER['REQUEST_SCHEME'].'://'.$sub.'.'.get_domain().'/');
    define('APP', ROOT.'/'.$sub.'/');
    define('SUB', $sub);
    define('URL', SUB_URL);
}else{
    define('SUB_URL', $_SERVER['REQUEST_SCHEME'].'://'.get_domain().'/');
    define('APP', ROOT.'/app/');
    define('SUB', 'app');
    define('URL', SUB_URL);
}

const TEMPLATES = ROOT . '/templates/' . SUB . '/';

define('BASE_URL', $_SERVER['REQUEST_SCHEME'].'://'.get_domain().'/');
define('API_URL', $_SERVER['REQUEST_SCHEME'].'://api.'.get_domain().'/');
define('ADMIN_URL', $_SERVER['REQUEST_SCHEME'].'://admin.'.get_domain().'/');

require_once(ROOT.'/vendor/autoload.php');
require_once(CORE.'autoload.php');
require_once(CONFIG.'config.php');
require_once(CORE.'const.php');

(new \core\engine\media_upload());






