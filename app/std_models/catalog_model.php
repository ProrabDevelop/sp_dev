<?php

namespace app\std_models;

use app\catalog\service;
use core\engine\DATA;

class catalog_model{

    public function __construct(){

    }

    public function get_by_spec_id($id){

        $spec_id = $id;
        $speciality = new speciality($spec_id);




        $search_data = [
            'spec_id' => $spec_id,
            'enabled' => 1,
        ];

        $city_id = mapping_city_to_finder();
        if($city_id){
            $search_data['city_id'] = $city_id;
        }



        $services_query = \ORM::for_table('sort_rating')->where($search_data);





        if(isset($_GET['sort_by'])){
            switch ($_GET['sort_by']){
                case 'rating_desc':
                    $services_query = $services_query->order_by_asc('rating_summ');//order_by_desc
                    //$services_query = $services_query->_add_order_by("rating", "asc");//order_by_desc
                    break;

                default:
                    $services_query = $services_query->order_by_desc('rating_summ');//order_by_asc
                    break;
            }
        }else{
            $services_query = $services_query->order_by_asc('rating_summ'); //order_by_desc
        }

        //reviews_asc
        //reviews_desc

        $services = $services_query->find_many();





















        for($i = 0; $i<count($services); $i++){
            /*print_r("<pre>");
            print_r($services[$i]->_data["rating"]);
            print_r("</pre>");
            print_r("<br><br><br>");*/
            $arr_tmp[$services[$i]->_data["rating"]][] = $services[$i];
        }


        if(isset($_GET['sort_by'])){
            switch ($_GET['sort_by']){
                case 'rating_desc':

                    krsort($arr_tmp);
                    break;

                default:

                    ksort($arr_tmp);
                    break;
            }
        }else{
            krsort($arr_tmp);
        }

        $arr_res_temp = [];
        $arr_res = [];
        foreach($arr_tmp AS $k=>$v){
            $arr_tmp2 = [];
            foreach($v AS $k2=>$v2){
                $arr_tmp2[$v2->_data["rating_summ"]] = $v2;

            }

            if(isset($_GET['sort_by'])){
                switch ($_GET['sort_by']){
                    case 'rating_desc':

                        krsort($arr_tmp2);
                        break;

                    default:

                        ksort($arr_tmp2);
                        break;
                }
            }else{
                krsort($arr_tmp2);
            }

            /*print_r($arr_tmp2);
            print_r("<pre>");
            print_r("</pre>");
            print_r("<br>");*/
            if(count($arr_res_temp) == 0){
                //print_r(1);
                //print_r("<br><br><br>");
                $arr_res_temp = $arr_tmp2;
                //print_r(count($arr_res_temp));
            } else {
                //print_r(2);
                //print_r("<br><br><br>");
                $arr_res = array_merge($arr_res_temp, $arr_tmp2);
                $arr_res_temp = $arr_res;
            }
        }


        $services = $arr_res_temp;















        if(!$services){
            return false;
        }

        /*
        $temp = [];
        foreach ($services as $service){
            $temp[] = [
                'user' => $service->user_id,
                'spec' => $service->spec_id,
                'summ' => $service->rating_summ
            ];
        }

        dump($temp);
        */

        $data = [];
        $user_ids = [];
        foreach ($services as $service){
            $user_ids[] = $service->user_id;
            $data[$service->user_id] = [
                'range_price' => $service->range_price,
                'service_id' => $service->id,
            ];
        }



        $users = (new users_list())->add_data($data)->get($user_ids);

        if($users){
            foreach ($users as $user){
                $user->reviews = (new reviews_list())->get_by_spec_for_master($user->id, $speciality->id);
            }
        }


        $count = count($services);

        foreach ($services as $service){
            $service->user = $users[$service->user_id];
        }


        /////////////////////////////////////////////////////
        DATA::set('speciality_id', $spec_id);
        DATA::set('speciality', $speciality);
        DATA::set('services', $services);
        DATA::set('count', $count);

        return true;

    }

    public function no_spec(){

        $services_query = \ORM::for_table('sort_rating');

        $user_query = \ORM::for_table('users');

        $users_1 = $services_query->find_many();
        /*
                /* @var $speciality \app\std_models\speciality *--/
        /* @var $user \app\std_models\user */
        $speciality = DATA::get('speciality');
        $user = DATA::get('user');

        /* @var $service \app\std_models\user_service*--/


        $user = $service->user;


    $all_score = null;
    if (!empty($user->reviews)) {

        $temp_score = 0;
        foreach ($user->reviews as $review) {
            $temp_score += $review->score;
        }

        $all_score = round($temp_score / count($user->reviews), 1);
    }

*/
//print_r($users_1);

        $search_data = [
            'enabled' => 1,
        ];



        $city_id = mapping_city_to_finder();
        if($city_id){
            $search_data['city_id'] = $city_id;
        }

        $services_query = $services_query->where($search_data);

        if(isset($_GET['sort_by'])){
            switch ($_GET['sort_by']){
                case 'rating_desc':
                    $services_query = $services_query->order_by_asc('rating_summ');//order_by_desc
                    break;

                default:
                    $services_query = $services_query->order_by_desc('rating_summ');//order_by_asc
                    break;
            }
        }else{
            $services_query = $services_query->order_by_asc('rating_summ');//order_by_desc
        }

        $services = $services_query->find_many();

        $arr_tmp = [];

        for($i = 0; $i<count($services); $i++){
            /*print_r("<pre>");
            print_r($services[$i]->_data["rating"]);
            print_r("</pre>");
            print_r("<br><br><br>");*/
            $arr_tmp[$services[$i]->_data["rating"]][] = $services[$i];
        }


        if(isset($_GET['sort_by'])){
            switch ($_GET['sort_by']){
                case 'rating_desc':

                    krsort($arr_tmp);
                    break;

                default:

                    ksort($arr_tmp);
                    break;
            }
        }else{
            krsort($arr_tmp);
        }

        $arr_res_temp = [];
        $arr_res = [];
        foreach($arr_tmp AS $k=>$v){
            $arr_tmp2 = [];
            foreach($v AS $k2=>$v2){
                $arr_tmp2[$v2->_data["rating_summ"]] = $v2;

            }

            if(isset($_GET['sort_by'])){
                switch ($_GET['sort_by']){
                    case 'rating_desc':

                        krsort($arr_tmp2);
                        break;

                    default:

                        ksort($arr_tmp2);
                        break;
                }
            }else{
                krsort($arr_tmp2);
            }

            /*print_r($arr_tmp2);
            print_r("<pre>");
            print_r("</pre>");
            print_r("<br>");*/
            if(count($arr_res_temp) == 0){
                //print_r(1);
                //print_r("<br><br><br>");
                $arr_res_temp = $arr_tmp2;
                //print_r(count($arr_res_temp));
            } else {
                //print_r(2);
                //print_r("<br><br><br>");
                $arr_res = array_merge($arr_res_temp, $arr_tmp2);
                $arr_res_temp = $arr_res;
            }
        }
        /*
        print_r("333333333333<pre>");
        print_r($arr_res_temp);
        print_r("</pre>");


        print_r("2222");

*/
        $services = $arr_res_temp;

















        if(!$services){
            return false;
        }

        $data = [];
        $user_ids = [];
        $specs_ids = [];
        foreach ($services as $service){
            $user_ids[] = $service->user_id;
            $specs_ids[] = $service->spec_id;
            $data[$service->user_id] = [
                'range_price' => $service->range_price,
                'service_id' => $service->id,
                //'speciality' => new speciality($service->spec_id),
            ];
        }

        $user_ids = array_unique($user_ids);
        $specs_ids = array_unique($specs_ids);

        $speciality_list = (new speciality_list())->get_by_ids($specs_ids);

        $users = (new users_list())->add_data($data)->get($user_ids);

        if($users){
            foreach ($users as $user){
                //$user->speciality = $speciality_list[$service->spec_id];
                $user->reviews = (new reviews_list())->get_by_spec_for_master($user->id, $service->spec_id);

//                foreach ($services as $service){
//                    $service->reviews[$service->id] = (new reviews_list())->get_by_spec_for_master($user->id, $service->spec_id);
//                }


            }
        }


        $count = count($services);

        foreach ($services as $service){
            $service->user = $users[$service->user_id];
            $service->speciality = new speciality($service->spec_id);
        }


        /////////////////////////////////////////////////////
        DATA::set('speciality', $speciality_list);
        DATA::set('services', $services);
        DATA::set('count', $count);

        return true;

    }

    public function get_favorites($ids)
    {
        if (empty($ids)) {
            return;
        }

        $services = \ORM::for_table('sort_rating')
            ->where_in('id', $ids)
            ->where('enabled', 1)
            ->order_by_desc('rating_summ')
            //Сортировка по рейтингу + цена
            //->order_by_asc('range_price')
            ->find_many();

        if (!$services) {
            return;
        }

        $data = [];
        $user_ids = [];
        $spec_ids = [];
        foreach ($services as $service) {
            $user_ids[] = $service->user_id;
            $spec_ids[] = $service->spec_id;
            $data[$service->user_id] = [
                'range_price' => $service->range_price,
                'service_id'  => $service->id,
                'spec_id'     => $service->spec_id,
            ];
        }

        $users = (new users_list())->add_data($data)->get($user_ids);

        $speciality_list = (new speciality_list())->get_by_ids($spec_ids);

        foreach ($services as $service){
            $service->user = $users[$service->user_id];
            $service->speciality = new speciality($service->spec_id);
        }

        DATA::set('speciality', $speciality_list);
        DATA::set('services', $services);
    }

    public function get_orders($orders){

        if(!empty($orders)){

            $ids = [];

            foreach ($orders as $key => $order){
                $ids[$key] = $order['id'];
            }

            $services = \ORM::for_table('sort_rating')
                ->where_in('id',$ids)
                ->where('enabled', 1)
                ->order_by_desc('rating_summ')
                //Сортировка по рейтингу + цена
                //->order_by_asc('range_price')
                ->find_many();

            if(!$services){
                return false;
            }


            $data = [];
            $user_ids = [];
            $spec_ids = [];
            foreach ($services as $service){
                $user_ids[] = $service->user_id;
                $spec_ids[] = $service->spec_id;
                $data[$service->user_id] = [
                    'range_price' => $service->range_price,
                    'service_id' => $service->id,
                    'spec_id' => $service->spec_id,
                ];



            }

            $users = (new users_list())->add_data($data)->get($user_ids);

            if($users){

                $speciality_list = (new speciality_list())->get_by_ids($spec_ids);

                foreach ($users as $user_k => $user ){

                    $key = array_search($user->service_id, $ids);

                    if(!isset($orders[$key]['status'])){
                        //print_r($orders[$key]['status']);
                        //unset($users[$user_k]);
                        $orders[$key]['status'] = 3;
                    } else {
                        //print_r($users[$user_k]);
                        //unset($users[$user_k]);
                    }
                    $user->history_date = $orders[$key]['date'];
                    $user->history_status = $orders[$key]['status'];
                    $user->history_this_user_id = $orders[$key]['this_user_id'];
                    $user->history_to_whom_user_id = $orders[$key]['to_whom_user_id'];

                    $user->speciality = $speciality_list[$user->spec_id];

                }

            }
            $users = array_values($users);
            /////////////////////////////////////////////////////
            DATA::set('users', $users);





        }



    }

    public function get_service($id){

        $sorted_service = \ORM::for_table('sort_rating')
            ->where('enabled', 1)
            ->find_one($id);

        if($sorted_service){

            $speciality = new speciality($sorted_service->spec_id);

            $user = new user($sorted_service->user_id);
            $user->range_price = $sorted_service->range_price;

            $user->service_id = $sorted_service->id;

            $user->services = (new services_list())->get_by_spec_for_user($user->id, $speciality->id);
            $user->works = (new works_list())->get_by_spec_for_user($user->id, $speciality->id);
            $user->reviews = (new reviews_list())->get_by_spec_for_master($user->id, $speciality->id);


            DATA::set('speciality', $speciality);
            DATA::set('user', $user);

            return true;

        }

        return false;

    }







}
