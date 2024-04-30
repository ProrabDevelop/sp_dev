<?
namespace app\find;

use app\std_models\speciality_list;
use app\std_models\user;
use core\engine\API;
use core\engine\DATA;
use core\engine\std_module;
use core\engine\view;

class find extends std_module {

    public $layout = 'find';
    protected $forauth = false;

    protected $routes = [
        '/find' => [
            '/' => 'first_step',
        ],
    ];


    public function first_step(){

        DATA::set('header_step', 1);

        if(isset($_POST['chars'])){
            $chars = $_POST['chars'];
            $specialitys = (new speciality_list())->get_like_chars($chars);
            API::response($specialitys);
        }

        if(isset($_POST['find'])){

            if(empty($_POST['find'])){
                $this->redirect('catalog');
            }

            $this->redirect('catalog/'.$_POST['find']);

        }




    }

}