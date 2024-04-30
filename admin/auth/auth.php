<?
namespace admin\auth;

use admin\dashboard\dashboard;

use admin\std_models\user;
use core\engine\std_module_admin;
use core\engine\view;


class auth extends std_module_admin {

    public $active = true;
    public $layout = 'auth';
    protected $forauth = false;

    protected $routes = [
        '/login' => [
            '/' => 'login',
        ]
    ];

    public function login(){



        $fields = \validator::ALL_POST([
            'login' => ['req', 'str'],
            'pass' => ['req', 'str'],
        ]);

        if($fields->POST()){

            if($fields->errors){
                view::validator_errors($fields->errors);
            }else{

                $user_model = new user();
                $user = $user_model->auth($fields);

                if($user && $user->id != null){

                    $user->last_ip = getIp();
                    $user->update();

                    $_SESSION['id'] = $user->id;
                    $_SESSION['login'] = $user->login;

                    $this->redirect('dashboard');

                }

            }

        }



    }


}