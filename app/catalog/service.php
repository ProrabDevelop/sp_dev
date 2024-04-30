<?
namespace app\catalog;

use app\std_models\catalog_model;
use app\std_models\speciality;
use app\std_models\speciality_list;
use app\std_models\user_service;
use app\std_models\users_list;
use core\engine\API;
use core\engine\DATA;
use core\engine\media;
use core\engine\media_list;
use core\engine\std_module;
use core\engine\view;


class service extends std_module {

    public $layout = 'find';
    protected $forauth = false;

    protected $routes = [
        '/service' => [
            '/' => 'redirect_to_catalog',
            '/{id:\d+}/' => 'services_by_spec_id',


            //api
            '/add/' => 'create_service',
            '/single/' => 'single_service',
            '/edit/' => 'edit_service',
            '/delete/' => 'delete_service',
        ]
    ];

    public function redirect_to_catalog(){
        $this->redirect('catalog');
    }

    public function services_by_spec_id($data){
        DATA::set('header_step', 3);

        $catalog_status = (new catalog_model())->get_service($data['id']);

        if(!$catalog_status){
            $this->redirect('catalog');
        }

        $user = DATA::get('user');
        $speciality = DATA::get('speciality');

        set_meta_page('/service', [
            'name' => $user->name,
            'speciality' => $speciality->name,
        ]);
    }

    public function single_service(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $service = new user_service($fields->id);
            if($service){
                API::response($service);
            }

            API::error('179', 'there is no service with id = '.$fields->id);

        });



    }


    public function edit_service(){

        $this->is_api = true;


        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'name' => ['req', 'str'],
            'amount' => ['int'],
            'payment_type' => ['req', 'int'],
            'amount_type' => ['req', 'int'],
        ]);


        if(empty($_POST)){
            $_POST = file_get_contents('php://input');
        }

        if($fields->POST()){

            $user = DATA::get('USER');
            if($user){

                $service = (new user_service($fields->id));
                if($service){

                    if($service->user_id == $user->id){


                        $fields->on_error_replace(['amount' => 0]);

                        if($fields->payment_type != 3 && $fields->amount == 0){
                            $fields->errors['amount'] = ['no_empty'=>'no_empty'];
                            API::error_validator($fields);
                        }

                        if($fields->errors){
                            API::error_validator($fields);
                        }else{
                            $service->update($fields);
                            API::response(['amount_text' => $service->get_correct_price()]);
                        }

                    }

                    API::error('210', 'no user permission for edit this service');

                }

                API::error('179', 'there is no service with id = '.$fields->id);



            }

            API::error('015', 'no_auth');

        }else{
            API::no_post_data();
        }





    }

    public function delete_service()
    {
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            if ($service = new user_service($fields->id)) {
                $service->delete();

                API::response();
            }

            API::error('179', 'there is no service with id = '.$fields->id);
        });
    }

    public function create_service(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'spec_id' => ['req', 'int'],
            'name' => ['req', 'str'],
            'amount' => ['int'],
            'payment_type' => ['req', 'int'],
            'amount_type' => ['req', 'int'],
        ]);


        if(empty($_POST)){
            $_POST = file_get_contents('php://input');
        }

        if($fields->POST()){

            $fields->on_error_replace(['amount' => 0]);

            if($fields->payment_type != 3 && $fields->amount == 0){
                $fields->errors['amount'] = ['no_empty'=>'no_empty'];
                API::error_validator($fields);
            }

            if($fields->errors){
                API::error_validator($fields);
            }else{

                $user = DATA::get('USER');
                if($user){

                    $last_service = (new user_service())->get_last_service($user->id, $fields->spec_id);
                    if($last_service){
                        $fields->add_field('sort_id', $last_service->sort_id + 1);
                    }else{
                        $fields->add_field('sort_id', 1);
                    }

                    $fields->add_field('user_id', $user->id);
                    $fields->add_field('currency', 'RUB');

                    $service = (new user_service())->create($fields);

                    $service->correct_price = $service->get_correct_price();


                    API::response($service);

                }

                API::error('015', 'no_auth');

            }

        }else{
            API::no_post_data();
        }

    }




}
