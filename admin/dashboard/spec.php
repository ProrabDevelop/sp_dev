<?
namespace admin\dashboard;

use app\std_models\speciality;
use app\std_models\speciality_list;
use app\std_models\user;
use core\engine\AUTH;
use core\engine\DATA;
use Core\Engine\pagination;
use core\engine\std_module;
use core\engine\std_module_admin;
use core\engine\view;

class spec extends std_module_admin {

    public $forauth = true;

    protected $routes = [
        '/spec' => [
            '/' => 'all_real_specs',
            '/spec_request/' => 'spec_request',
            '/delete_request/' => 'spec_request',
            '/delete_request/{id:\d+}/' => 'delete_request',

            '/add_spec/' => 'add_spec',
        ],

    ];



    public function all_real_specs(){

        $specs = (new speciality_list())->get_all_and_paginate();
        DATA::set('specs', $specs);

    }


    public function spec_request(){

        $count = \ORM::for_table('new_specs')->count();
        pagination::set_total($count);

        $specs = \ORM::for_table('new_specs')->find_many();

        DATA::set('specs', $specs);

    }


    public function delete_request($data){

        $this->is_api = true;

        $spec = \ORM::for_table('new_specs')->find_one($data['id']);

        if($spec){
            $spec->delete();
        }

        $this->redirect('spec/spec_request');

    }


    public function add_spec(){

        $fields = \validator::ALL_POST([
            'name' => ['req', 'str'],
            'name_many' => ['req', 'str'],
            'name_of' => ['req', 'str'],
            'name_cat' => ['req', 'str'],
        ]);

        if($fields->POST()){

            if($fields->errors){
                view::validator_errors($fields->errors);
            }else{
                $fields->add_field('publish', 1);
                $spec = (new speciality())->create($fields);

                if($spec){
                    view::set_notification('success', [
                        'title' => 'Успешное сохранение',
                        'content' => 'Специализация создана, ее ID = '.$spec->id,
                    ]);
                }

            }

        }



    }


}