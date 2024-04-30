<?php

namespace app\std_models;

use core\engine\std_model;

class user_service extends std_model {

    public $id, $user_id, $spec_id, $sort_id, $name, $amount, $currency, $payment_type, $amount_type;

    protected $table_name = 'service';


    public function get_correct_price(){

        $payment_types = [
            1 => 'от ',
            2 => '',
            3 => 'По договоренности ',
        ];

        $amount_types = [
            1 => 'услуга',
            2 => 'час',
            3 => 'м²',
            4 => 'Кг',
        ];

        $price = $payment_types[$this->payment_type].money_beautiful($this->amount).'₽/'.$amount_types[$this->amount_type];

        if($this->payment_type == 3){
            $price = $payment_types[3];
        }

        return $price;

    }

    public function get_last_service($user_id, $spec_id){

        $service = \ORM::for_table($this->table_name)->where([
            'user_id' => $user_id,
            'spec_id' => $spec_id,
        ])->order_by_desc('sort_id')->find_one();

        if($service){

            return $this->set_ormdata($service);
        }

        return false;
    }

}
