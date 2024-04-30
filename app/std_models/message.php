<?php

namespace app\std_models;

use core\engine\std_model;

class message extends std_model {

    public $id, $dialog_id, $sender, $time, $readed, $type, $body;

    protected $table_name = 'messages';
    protected $messages_list = [];


    public function read(){

        if($this->readed != 1){
            $this->readed = 1;
            $this->update();
            return true;
        }
        return false;
    }

    //////////////////

    public function get_messages_by_dialog($dialog_id){

        $messages = \ORM::for_table($this->table_name)->where('dialog_id', $dialog_id)->find_many();

        if($messages){
            foreach ($messages as $message){
                $this->messages_list[$message->id] = (new message())->set_ormdata($message);
            }
        }

        return $this->messages_list;
    }

    public function get_all_unread_count($user_id){
        $messages_count = \ORM::for_table($this->table_name)
            ->where_not_equal('readed', 1)
            ->where_equal('reader', $user_id) //sender
            ->count();
        return $messages_count;
    }

    public function get_unread_count($dialog_id, $user_id){
        //print_r($dialog_id."----------".$user_id."<br>");
        $messages_count = \ORM::for_table($this->table_name)
            ->where('dialog_id', $dialog_id)
            ->where_not_equal('readed', 1)
            //>where_not_equal('sender', $user_id)
            ->where_equal('reader', $user_id)
            ->count();
        return $messages_count;
    }

    public function read_messages($dialog_id, $user_id){
        $messages = \ORM::for_table($this->table_name)
            ->where('dialog_id', $dialog_id)
            //->where_not_equal('sender', $user_id)
            ->where_equal('reader', $user_id)
            ->where('readed',   0)
            ->find_many();

        if($messages){
            foreach ($messages as $message){
                $message->readed = 1;
                $message->save();
            }
            return true;
        }

        return false;
    }


}
