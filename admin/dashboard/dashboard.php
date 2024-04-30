<?
namespace admin\dashboard;

use core\engine\std_module;
use core\engine\std_module_admin;
use core\engine\view;

class dashboard extends std_module_admin {

    public $forauth = true;

    protected $routes = [
        '/dashboard' => [
            '/' => 'main',
        ],
    ];

    public function main(){
        $this->redirect('users');
    }



}