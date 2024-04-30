<?php

namespace core\engine;
use core\engine\Logger;
class std_model{


    protected $table_name;

    protected $orm_data;

    protected $can_create = true;
    protected $can_update = true;
    protected $can_delete = true;


    public function __construct($id = null){
        if($id){
            $status = \ORM::for_table($this->table_name)->find_one($id);
            if($status){
                $this->set_ormdata($status);
            }
        }
        return $this;
    }

    protected function after_construct(){

    }

    protected function after_remap(){

    }

    public function set_ormdata(\ORM $orm_data){
        $this->orm_data = $orm_data;
        $this->remap_data();
        return $this;
    }

    protected function remap_data(){
        foreach ($this->orm_data->_data as $key => $value){
            $this->$key = json_unformat($value);
        }
        $this->after_remap();
    }

    protected function remap_data_by_validator(\validator $fields){

        foreach ($this->orm_data->_data as $key => $value){
            if(isset($fields->$key)){
                $this->$key = $fields->$key;
            }
        }

    }

    public function update(\validator $fields = null){

        if($this->can_update){
            if($fields){
                $this->remap_data_by_validator($fields);
            }

            foreach ($this->orm_data->_data as $key => $value){

                $field = $this->$key;
                if(is_array($this->$key)){
                    $field = json_encode($this->$key, JSON_UNESCAPED_UNICODE);
                }
                $this->orm_data->$key = $field;

            }
            $this->orm_data->save();
            return true;
        }

       return false;
    }

    public function create(\validator $fields = null){
        if($this->can_create) {

            if ($fields) {
                $cols = $this->get_columns();
                foreach ($cols as $key => $col) {
                    if (isset($fields->$key)) {
                        $this->$key = $fields->$key;

                        $field = $fields->$key;
                        if (is_array($fields->$key)) {
                            $field = json_encode($fields->$key, JSON_UNESCAPED_UNICODE);
                        }
                        $this->$key = $field;
                    }
                }
            }


            $this->create_by_cols();
        }

        return $this;
    }


    public function delete(){

        if($this->can_delete) {
            $this->orm_data->delete();
            return true;
        }

        return false;
    }

    protected function create_by_cols(){
        $data = \ORM::for_table($this->table_name)->create();

        $cols = $this->get_columns();

        foreach ($cols as $key => $col){
            $field = $this->$key;
            if(is_array($this->$key)){
                $field = json_encode($this->$key, JSON_UNESCAPED_UNICODE);
            }
            $data->$key = $field;
        }

        $data->save();
        $this->set_ormdata($data);

        return $this;
    }

    protected function get_columns(array $ignore_cols = null){
        //always ignore ID
        $ignore_cols[] = 'id';

        \ORM::raw_execute("SHOW columns FROM $this->table_name;");
        $statement = \ORM::get_last_statement();
        $cols = [];
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            if(!in_array($row['Field'], $ignore_cols)){
                $cols[$row['Field']] = '';
            }
        }
        return $cols;

    }


}