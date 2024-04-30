<?php

namespace app\std_models;

use core\engine\API;

class custom_user_data{

    public $data = [];
    public $data_arrmap = ['fields_names', 'array'];
    public $data_arrmap_names = [
        'fields_names' => 'Название поля на рус 1',
        'array' => 'Название поля на рус 1',
    ];
    protected $table = 'table_name';

    protected $orm_data;


    public function get($index){

        if(isset($this->data[$index])){
            return json_unformat($this->data[$index]);
        }

        return false;

    }

    public function set($index, $value){

        if(in_array($index, $this->data_arrmap)){

            $field = $value;

            if(is_array($value)){
                $field = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            $this->data[$index] = $field;
            return true;
        }

        return false;

    }


    public function update(\validator $fields){

        foreach ($this->data_arrmap as $key){

            if(isset($fields->$key)){
                $field = $fields->$key;

                if($field == 'true'){
                    $field = 1;
                }
                if($field == 'false'){
                    $field = 0;
                }

                if(is_array($fields->$key)){
                    $field = json_encode($fields->$key, JSON_UNESCAPED_UNICODE);
                }
                $this->orm_data->$key = $field;
            }

        }

        $this->orm_data->save();

    }

    public function save_data(){

        foreach ($this->data_arrmap as $key){

            if(isset($this->data[$key])){
                $field = $this->data[$key];

                if($field == 'true'){
                    $field = 1;
                }
                if($field == 'false'){
                    $field = 0;
                }

                if(is_array($this->data[$key])){
                    $field = json_encode($this->data[$key], JSON_UNESCAPED_UNICODE);
                }
                $this->orm_data->$key = $field;
            }

        }

        $this->orm_data->save();

    }

    public function __construct($uid = null){

        if($uid != null){
            foreach ($this->data_arrmap as $key){
                $this->data[$key] = null;
            }

            $data = \ORM::for_table($this->table)->where('user_id', $uid)->find_one();
            if(!$data){
                $data = $this->create_empty_row($uid, true);
            }

            $this->orm_data = $data;
            foreach ($data->_data as $key => $val){
                if(in_array($key, $this->data_arrmap)){
                    $this->data[$key] = $val;
                }
            }

        }

        return $this;

    }

    public function create_empty_row($uid, $return_obj = false){
        $new_data = \ORM::for_table($this->table)->create();
        $new_data->user_id = $uid;
        $new_data->save();

        if($return_obj){
            return $new_data;
        }



    }

    public function create_row_data($uid, $data){

        $temp_data = \ORM::for_table($this->table)->where('user_id', $uid)->find_one();

        if(!$temp_data){
            $new_data = \ORM::for_table($this->table)->create();

            $new_data->user_id = $uid;

            foreach ($this->data_arrmap as $index => $key){
                if(isset($data[$key])){
                    $new_data->$key = $data[$key];
                }
            }

            $new_data->save();
        }

    }

}