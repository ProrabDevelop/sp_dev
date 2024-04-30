<?php

namespace app\std_models;

class customer_data extends custom_user_data {
    public $data = [];
    public $data_arrmap = ['favorites', 'orders'];
    public $data_arrmap_names = ['favorites' => 'favorites', 'orders' => 'orders'];
    protected $table = 'customer_data';

    public function get($index){

        if($index == 'favorites' && empty($this->data[$index])){
            return [];
        }

        if(isset($this->data[$index])){
            return json_unformat($this->data[$index]);
        }

        return false;

    }

}