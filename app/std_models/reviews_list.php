<?php

namespace app\std_models;

use Core\Engine\pagination;

class reviews_list{

    protected $reviews_list = [];

    public function get_by_spec_for_master($master_id, $spec_id){

        $reviews = \ORM::for_table('reviews')->where([
            'spec_id' => $spec_id,
            'master_id' => $master_id,
        ])->order_by_desc('time')->find_many();

        if($reviews){
            foreach ($reviews as $review){
                $this->reviews_list[$review->id] = (new review())->set_ormdata($review);
            }
        }

        return $this->reviews_list;
    }


    public function get_by_spec_for_master_count_only($master_id, $spec_id){

        $count = \ORM::for_table('reviews')->where([
            'spec_id' => $spec_id,
            'master_id' => $master_id,
        ])->count();

       return $count;

    }

    public function get_all($master_id){
        $reviews = \ORM::for_table('reviews')->where([
            'master_id' => $master_id,
        ])->order_by_desc('time')->find_many();

        if($reviews){
            foreach ($reviews as $review){
                $this->reviews_list[$review->id] = (new review())->set_ormdata($review);
            }
        }

        return $this->reviews_list;
    }

    public function get_to_moderate(){


        $count = \ORM::for_table('reviews')->where_not_null('complaint')->count();
        pagination::set_total($count);

        $reviews = \ORM::for_table('reviews')
            ->where_not_null('complaint')
            ->offset(pagination::get_offset())
            ->limit(pagination::get_limit())
            ->find_many();

        if($reviews){
            foreach ($reviews as $review){
                $this->reviews_list[$review->id] = (new review())->set_ormdata($review);
            }
        }

        return $this->reviews_list;
    }



}