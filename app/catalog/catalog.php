<?
namespace app\catalog;

use app\std_models\catalog_model;
use core\engine\DATA;
use core\engine\std_module;


class catalog extends std_module {

    public $layout = 'find';
    protected $forauth = false;

    protected $routes = [
        '/catalog' => [
            '/' => 'all_cats',
            '/{id:\d+}/' => 'cat_by_id',
        ]
    ];



    public function all_cats(){
        DATA::set('header_step', 2);

        $catalog_status = (new catalog_model())->no_spec();

        set_meta_page('/catalog');
    }

    public function cat_by_id($data){

        DATA::set('header_step', 2);

        $catalog_status = (new catalog_model())->get_by_spec_id($data['id']);

        if(!$catalog_status){
            $this->redirect('catalog');
        }

        set_meta_page('/catalog');
    }





}
