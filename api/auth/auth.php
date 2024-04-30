<?
namespace api\auth;

use app\std_models\user;
use core\engine\API;
use core\engine\sms;
use core\engine\std_module;

/**
 * @deprecated
 */
class auth extends std_module {

    public $active = true;
    public $forauth = false;

    protected $routes = [
        '/auth' => [
            '/' => 'main',
            '/login/' => 'login',
            '/registration/' => 'registration',
            '/regsms/' => 'regsms',
            '/lostpass/' => 'lostpass',
            '/lostsms/' => 'lostsms',
            '/getid/' => 'getid',
        ],
    ];

    public function main(){
        echo 'this is main auth system api';
    }

    public function getid(){

        $user = new user($this->auth->get_session_data('id'));

        if($user->id != null){
            $res = [
                'id' => $user->id,
                'type' => $user->role,
            ];

            API::response($res);
        }

        API::error('015', 'no_user');

    }

    /**
     * @deprecated
     */
    public function login(){

        $fields = \validator::ALL_POST([
            'phone' => ['req', 'phone'],
            'pass' => ['req', 'str'],
            'role' => ['req', ['enum', ['customer','master']]],
            'remember_me' => ['bool'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $user_model = new user();
            $user = $user_model->auth($fields);

            if ($user) {
                $user->role = $fields->role;
                $user->update();

                $app_uid = $this->auth->set_session_key('id', $user->id);

                API::response(['app-uid' => $app_uid]);
            }


            API::error(170, 'Авторизация не удалась');

        });

    }

    /**
     * @deprecated
     */
    public function registration(){

        $fields = \validator::ALL_POST([
            'name' => ['req', 'str'],
            'mail' => ['req', 'email'],
            'phone' => ['req', 'phone'],
            'pass' => ['req', 'str'],
            'role' => ['req', ['enum', ['customer','master']]],
            'remember_me' => ['bool'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $user_model = new user();

            $response = $user_model->create($fields->get_fields());

            if($response['id']){

                $app_uid = $this->auth->set_session_key('id', $response['id']);

                API::response([
                    'app-uid' => $app_uid,
                    'sms_hash' => $response['sms_hash'],
                    'sms_debug' => $response['sms_debug'],
                ]);
            }

            API::error(170, 'Авторизация не удалась');

        });

    }

    /**
     * @deprecated
     */
    public function regsms(){

        $fields = \validator::ALL_POST([
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
            'code' => ['req', 'str', ['min', 6], ['max', 6]],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $confirm_data = sms::confirm($fields->sms_hash, $fields->code);

            if(is_array($confirm_data) && $confirm_data['action'] == 'reg'){
                $user = new user($confirm_data['user_id']);
                $user->confirm_reg();

                API::response();

            }

            API::error(170, 'Авторизация не удалась');

        });

    }

    /**
     * @deprecated
     */
    public function lostpass(){

        $fields = \validator::ALL_POST([
            'phone' => ['req', 'phone'],
        ]);

        if($fields->POST()){

            if($fields->errors){
                API::error_validator($fields);
            }else{

                $user = new user();

                $response = $user->lostpass($fields->phone);

                if($response){

                    API::response([
                        'sms_hash' => $response['sms_hash'],
                        'sms_debug' => $response['sms_debug'],
                    ]);
                }

                API::error(171, 'Пользователь не найден не удалась');

            }


        }else{
            API::no_post_data();
        }

    }

    /**
     * @deprecated
     */
    public function lostsms(){

        $fields = \validator::ALL_POST([
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
            'code' => ['req', 'str', ['min', 6], ['max', 6]],
            'pass' => ['req', 'str', ['min', 8]],
            'pass_confirm' => ['req', 'str', ['min', 8]],
        ]);


        API::auto_validate($fields, function (\validator $fields){

            $confirm_data = sms::confirm($fields->sms_hash, $fields->code);

            if(is_array($confirm_data) && $confirm_data['action'] == 'lostpass'){
                $user = new user($confirm_data['user_id']);

                if($fields->pass === $fields->pass_confirm){
                    $user->change_passwors($fields->pass);

                    $app_uid = $this->auth->set_session_key('id', $user->id);

                    API::response([
                        'app-uid' => $app_uid,
                    ]);

                }else{
                    API::error(170, 'Пароли не совпадают');
                }

            }

            API::error(195, 'Не верный SMS код');

        });

    }

}
