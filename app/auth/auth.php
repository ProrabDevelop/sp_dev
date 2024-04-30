<?
namespace app\auth;

use app\std_models\user;
use core\engine\API;
use core\engine\sms;
use core\engine\std_module;
use http\Cookie;

class auth extends std_module {

    public $active = true;
    public $forauth = false;

    protected $routes = [
        '/auth' => [
            '/' => 'main',
            '/login/' => 'login',
            '/registration/' => 'registration',
            '/registration/check/' => 'check_phone',
            '/registration/confirm/' => 'confirm_phone',
            '/forgot/' => 'forgot',
            '/forgot/confirm/' => 'forgot_confirm_phone',
            '/forgot/change/' => 'forgot_change_password',
            '/sms/resend/' => 'resend_sms',
            '/getid/' => 'getid',

            '/switch/' => 'switch_user_type',
            '/logoutall/' => 'logoutall',
            '/logout/' => 'logout',
        ],
    ];

    public function main(){
        echo 'this is main auth system api';
    }

    public function getid(){

        $user = new user();

        $res = [
            'id' => $this->auth->user->id,
            'type' => $this->auth->user->role,
        ];

        API::response($res);

    }

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

            if ($user){
                $user->role = $fields->role;
                $user->update();

                $jwt_data = $this->auth->create_jwt($user->id);

                API::response(['jwt_data' => $jwt_data]);

            }

            API::error(170, 'Телефон или пароль не верны');

        });

    }

    public function check_phone()
    {
        $fields = \validator::ALL_POST([
            'phone' => ['req', 'phone'],
        ]);

        API::auto_validate($fields, function (\validator $fields){
            $user_model = new user();
            $user = $user_model->get_user_by_phone($fields->phone);

            if (!$user) {
                $sms = sms::init($fields->phone, 'reg');
                $smsDelay = sms::getDelay($sms);

                $res = [
                    'sms_hash' => $sms->hash,
                    'sms_delay' => $smsDelay > 0 ? $smsDelay : 0,
                ];

                if (sms_debug) {
                    $res['sms_debug'] = $sms->code;
                }

                API::response($res);
            }

            API::error(171, 'Пользователь с таким номером уже зарегистрирован');
        });
    }

    public function confirm_phone()
    {
        $fields = \validator::ALL_POST([
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
            'code'     => ['req', 'str', ['min', 6], ['max', 6]],
            'phone'    => ['req', 'phone'],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            $sms = sms::confirm($fields->phone, $fields->sms_hash, $fields->code, 'reg');

            if ($sms) {
                API::response();
            }

            API::error(170, 'Неверный код');
        });
    }

    public function registration()
    {
        $fields = \validator::ALL_POST([
            'name' => ['req', 'str'],
            'phone' => ['req', 'phone'],
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
            'pass' => ['req', 'str', ['min', 8], ['max', 20]],
            'role' => ['req', ['enum', ['customer','master']]],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            if (!sms::checkConfirmed($fields->phone, $fields->sms_hash, 'reg')) {
                API::error(170, 'Номер телефона не подтвержден');
            }

            $user_model = new user();
            $user = $user_model->create([
                'phone' => $fields->phone,
                'name' => $fields->name,
                'pass' => $fields->pass,
                'role' => $fields->role,
            ]);

            if ($user) {
                $jwt_data = $this->auth->create_jwt($user->id);

                $user->confirm_reg();

                API::response([
                    'jwt_data' => $jwt_data,
                ]);
            }

            API::error(171, 'Пользователь с таким номером уже зарегистрирован');
        });
    }

    public function forgot()
    {
        $fields = \validator::ALL_POST([
            'phone' => ['req', 'phone'],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            $user_model = new user();
            $user = $user_model->get_user_by_phone($fields->phone);

            if ($user) {
                $sms = sms::init($fields->phone, 'lostpass');
                $smsDelay = sms::getDelay($sms);

                $res = [
                    'sms_hash' => $sms->hash,
                    'sms_delay' => $smsDelay > 0 ? $smsDelay : 0,
                ];

                if (sms_debug) {
                    $res['sms_debug'] = $sms->code;
                }

                API::response($res);
            }

            /*
             * Send fake hash if phone not found
             */
            API::response([
                'sms_hash' => uniqid('', false),
                'sms_delay' => sms::DELAY,
            ]);
        });
    }

    public function forgot_confirm_phone()
    {
        $fields = \validator::ALL_POST([
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
            'code'     => ['req', 'str', ['min', 6], ['max', 6]],
            'phone'    => ['req', 'phone'],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            $sms = sms::confirm($fields->phone, $fields->sms_hash, $fields->code, 'lostpass');

            if ($sms) {
                API::response();
            }

            API::error(170, 'Неверный код');
        });
    }

    public function forgot_change_password()
    {
        $fields = \validator::ALL_POST([
            'phone'        => ['req', 'phone'],
            'sms_hash'     => ['req', 'str', ['min', 13], ['max', 13]],
            'pass'         => ['req', 'str', ['min', 8]],
            'pass_confirm' => ['req', 'str', ['min', 8]],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            if ($fields->pass !== $fields->pass_confirm) {
                API::error(170, 'Пароли не совпадают');
            }

            if (!sms::checkConfirmed($fields->phone, $fields->sms_hash, 'lostpass')) {
                API::error(170, 'Номер телефона не подтвержден');
            }

            $user_model = new user();
            $user = $user_model->get_user_by_phone($fields->phone);

            $jwt_data = '';

            if ($user) {
                $user = new user($user->id);
                $user->change_passwors($fields->pass);

                $jwt_data = $this->auth->create_jwt($user->id);
            }

            API::response([
                'jwt_data' => $jwt_data,
            ]);
        });
    }

    public function resend_sms()
    {
        $fields = \validator::ALL_POST([
            'phone' => ['req', 'phone'],
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            $sms = sms::resend($fields->phone, $fields->sms_hash);

            if ($sms) {
                $res = [
                    'sms_delay' => sms::DELAY,
                ];

                if (sms_debug) {
                    $res['sms_debug'] = $sms->code;
                }

                API::response($res);
            }

            API::response([
                'sms_delay' => sms::DELAY,
            ]);
        });
    }

    public function switch_user_type(){

        if($this->auth->is_auth()){
            $this->auth->user->switch_role();
            $this->redirect('dashboard');
        }
        $this->redirect();


    }

    public function logoutall(){
        $this->auth->destroy_all_user_session($this->auth->user->id);
        $this->redirect();
    }

    public function logout(){
        $this->auth->destroy_session();
        $this->redirect();
    }

}
