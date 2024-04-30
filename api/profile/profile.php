<?php

namespace api\profile;

use core\engine\API;
use core\engine\std_module;

class profile extends std_module {

    public $active = true;
    public $forauth = true;

    protected $routes = [
        '/profile' => [
            '/' => 'main',
            '/edit/' => 'edit',
        ],
    ];

    public function main(){
        echo 'main profile api';
    }

    public function edit(){
        //temp($_POST);

        //temp($_COOKIE);

        //API::response('ok');

    }

}