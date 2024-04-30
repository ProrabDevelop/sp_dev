<?php

namespace app\std_models;

class sort_rating_prices{

    protected $specs_prices = [];

    //todo refactor - method deprecated
    public function __construct($user = null){

        if($user){
            $sort_ratings = \ORM::for_table('sort_rating')->where([
                'user_id' => $user->id,
                'enabled' => 1,
                ])->find_many();
            if($sort_ratings){
                foreach ($sort_ratings as $sort_rating){
                    $this->specs_prices[$sort_rating->spec_id] = intval($sort_rating->range_price);
                }
            }
        }

        return $this;

    }

    public function get_prices(){
        return $this->specs_prices;
    }

    //todo refactor - method deprecated
    public function update_price(\validator $fields){
        $sort_rating = \ORM::for_table('sort_rating')->where([
            'spec_id' => $fields->spec_id,
            'user_id' => $fields->user_id,
            'enabled' => 1,
        ])->find_one();

        if($sort_rating){
            $sort_rating->range_price = $fields->price;
            $sort_rating->save();
            return true;
        }

        return false;
    }



}