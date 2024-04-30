<?php

namespace app\std_models;

class works_list{

    protected $works_list = [];

    public function get_by_spec_for_user($user_id, $spec_id){

        $works = \ORM::for_table('works')->where([
            'user_id' => $user_id,
            'spec_id' => $spec_id,
        ])->find_many();

        if($works){
            foreach ($works as $work){
                $this->works_list[$work->id] = (new work())->set_ormdata($work);
            }
        }

        return $this->works_list;
    }


    public function get_by_spec_for_user_count_only($user_id, $spec_id){

        $count = \ORM::for_table('works')->where([
            'user_id' => $user_id,
            'spec_id' => $spec_id,
        ])->count();

        return $count;
    }


    public function get_all_for_user(user $user){

        $works = \ORM::for_table('works')->where([
            'user_id' => $user->id,
        ])->order_by_desc('id')->find_many();


        if($works){
            foreach ($user->get_specs() as $spec){
                $this->works_list[$spec->id] = [];

                foreach ($works as $work){
                    if($spec->id == $work->spec_id){
                        $this->works_list[$spec->id][] = (new work())->set_ormdata($work);
                    }
                }

            }

        }

        return $this->works_list;
    }


}