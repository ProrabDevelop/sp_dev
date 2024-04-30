<?
namespace app\main;


use core\engine\AUTH;
use core\engine\DATA;

use core\engine\std_module;

class main extends std_module {

    protected $forauth = false;
    public $layout = 'landing';

    protected $routes = [
        '/' => [
            'do' => 'main',
        ],
        '/login_with_user' => [
            '/{id:\d+}/' => 'login_with_user'
        ]
    ];

    public function main(){
        //$this->auth->set_session_key('id', null);
        //header('Location: '.URL.'dashboard');
    }

    public function login_with_user($data){
        AUTH::auth_admin_with_user(intval($data['id']));
        $this->redirect('dashboard');
    }


}