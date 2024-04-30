<?php

namespace app\std_models;

use core\engine\AUTH;
use core\engine\debugger;
use core\engine\sms;
use core\engine\view;

class users_list{

    protected $users_list = [];

    protected $custom_data = [];

    public function get(array $user_ids){

        $raw_users = \ORM::for_table('users')->where_in('id', $user_ids)->find_many();

        if($raw_users){
            foreach ($raw_users as $raw_user){
                $user = (new user())->set_user($raw_user);

                if(isset($this->custom_data[$user->id])){
                    foreach ($this->custom_data[$user->id] as $key => $value){
                        $user->$key = $value;
                    }
                }

                $this->users_list[$raw_user->id] = $user;

            }
        }

        return $this->users_list;

    }

    public function add_data(array $data = []){
        $this->custom_data = $data;

        return $this;
    }

}