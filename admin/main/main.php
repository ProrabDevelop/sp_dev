<?
namespace admin\main;


use core\engine\std_module_admin;

class main extends std_module_admin {

    public $layout = 'dashboard';
    public $forauth = true;

    protected $routes = [
        '/' => [
            'do' => 'main',
        ],
    ];

    public function main(){
        //echo 'main page admin';
        $this->redirect('dashboard');
    }

}