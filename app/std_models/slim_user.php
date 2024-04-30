<?php

namespace app\std_models;

use core\engine\API;
use core\engine\AUTH;
use core\engine\debugger;
use core\engine\sms;
use core\engine\std_model;
use core\engine\view;
use mysql_xdevapi\Table;

class slim_user extends std_model {

    public $id, $name, $role, $last_visit;

    protected $ormdata = null;

    protected $can_create = false;
    protected $can_update = false;
    protected $can_delete = false;

    protected $table_name = 'users';

    //////////////////
    protected $slim_users_list = [];


    public function get_list_by_ids(array $ids){

        $users = \ORM::for_table('users')
            ->where_null('deleted_at')
            ->where_in('id', $ids)
            ->find_many();

        if($users){
            foreach ($users as $user){
                $this->slim_users_list[$user->id] = (new slim_user())->set_ormdata($user);
            }
        }

        return $this->slim_users_list;
    }

    public function is_master(){
        if($this->role == 'master'){
            return true;
        }
        return false;
    }

    public function is_customer(){
        if($this->role == 'customer'){
            return true;
        }
        return false;
    }


}
