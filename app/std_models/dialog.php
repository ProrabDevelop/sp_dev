<?php

namespace app\std_models;

use core\engine\std_model;

class dialog extends std_model {

    public $id, $user_id_1, $user_id_2, $last_update;

    public $unread_count;

    public ?slim_user $user_1;
    public ?slim_user $user_2;
    public ?slim_user $companion;


    protected $table_name = 'dialogs';

    public function set_users(?slim_user $user_1, ?slim_user $user_2){
        $this->user_1 = $user_1;
        $this->user_2 = $user_2;
        return $this;
    }

    public function isset_dialog($uid_1, $uid_2){

        $dialog = \ORM::for_table($this->table_name)
            ->where_raw('( user_id_1 = ? AND user_id_2 = ? ) OR ( user_id_1 = ? AND user_id_2 = ? ) ', array($uid_1, $uid_2, $uid_2, $uid_1))
            ->find_one();

        if($dialog){
            return $dialog->id;
        }

        return false;

    }

    public function set_companion(?slim_user $companion){
        $this->companion = $companion;
        return $this;
    }

}