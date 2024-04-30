<?php

namespace core\engine;

use admin\std_models\user;

class ADMIN_AUTH{

    protected static $_instance = null;
    private user $user;


    public static function init(){

        if(self::$_instance == null){
            session_start();
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    protected function __construct(){

        if($this->is_auth()){
            $user = new user($_SESSION['id']);
            $this->user = $user;
            DATA::set('USER', $this->user);
        }

    }

    public function is_auth(){
        if(isset($_SESSION['id'])){
            return true;
        }
        return false;
    }


}