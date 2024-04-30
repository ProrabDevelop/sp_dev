<?php

namespace app\std_models;

class services_list{

    protected $services_list = [];

    public function get_by_spec_for_user($user_id, $spec_id){

        $services = \ORM::for_table('service')->where([
            'user_id' => $user_id,
            'spec_id' => $spec_id,
        ])->order_by_asc('sort_id')->find_many();

        if($services){
            foreach ($services as $service){
                $this->services_list[$service->id] = (new user_service())->set_ormdata($service);
            }
        }

        return $this->services_list;
    }


    public function get_by_spec_for_user_count_only($user_id, $spec_id){

        $count = \ORM::for_table('service')->where([
            'user_id' => $user_id,
            'spec_id' => $spec_id,
        ])->count();

        return $count;
    }



    public function get_all_for_user(user $user){

        $services = \ORM::for_table('service')
            ->where('user_id', $user->id)
            ->order_by_asc('sort_id')
            ->find_many();

        if($services){

            foreach ($user->get_specs() as $spec){

                $this->services_list[$spec->id] = [];

                foreach ($services as $service){
                    if($spec->id == $service->spec_id){
                        $this->services_list[$spec->id][] = (new user_service())->set_ormdata($service);
                    }
                }
            }

        }

        return $this->services_list;


    }



}