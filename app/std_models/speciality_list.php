<?php

namespace app\std_models;

use app\std_models\speciality;
use Core\Engine\pagination;
use core\engine\std_model;


class speciality_list {


    protected $speciality_list = [];


    protected function get_all(){

    }

    public function get_by_ids($ids){

        $specialitys = \ORM::for_table('speciality')
            ->where_in('id', $ids)
            ->where([
                'publish' => 1,
            ])
            ->find_many();

        if($specialitys){
            foreach ($specialitys as $speciality){
                $this->speciality_list[$speciality->id] = (new speciality())->set_ormdata($speciality);
            }
        }

        return $this->speciality_list;

    }

    public function get_like_chars($chars, $limit = 10){

        $chars = trim($chars);

        $specialitys = \ORM::for_table('speciality')
            ->where_like('name', $chars.'%')
            ->where('publish', 1)
            //->order_by_expr("CASE WHEN name LIKE '".$chars."' THEN 1 WHEN name LIKE '".$chars."%' THEN 2 WHEN name LIKE '%".$chars."' THEN 4 ELSE 3 END")
            ->limit($limit)
            ->find_many();

        if($specialitys){
            foreach ($specialitys as $speciality){
                $this->speciality_list[$speciality->id] = (new speciality())->set_ormdata($speciality);
            }
        }

        return $this->speciality_list;

    }


    public function get_all_and_paginate(){

        $specialitys_count = \ORM::for_table('speciality')->count();
        pagination::set_total($specialitys_count);

        $specialitys = \ORM::for_table('speciality')
            ->limit(pagination::get_limit())
            ->offset(pagination::get_offset())
            ->find_many();

        if($specialitys){
            foreach ($specialitys as $speciality){
                $this->speciality_list[$speciality->id] = (new speciality())->set_ormdata($speciality);
            }
        }

        return $this->speciality_list;

    }



}