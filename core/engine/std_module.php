<?

namespace core\engine;


class std_module{


    protected $active = true;
    protected $routes = [];
    public $layout = 'main';
    public $std_class_name = null;


    public $is_api = false;
    protected $auth = null;
    protected $forauth = true;

    public function __construct($get_info = false){

        $this->auth = \core\engine\AUTH::init();

        $full_class_name = explode('\\', get_called_class());
        $this->std_class_name = end($full_class_name);

        if($get_info){
            if($this->active){
                module::reg_module(get_called_class(), [
                    'layout' => $this->layout,
                    'routes' => $this->routes,
                ]);

            }

        }else{

            if($this->is_api){
                view::set_render_type('json');
            }

            if($this->forauth){
                if(!$this->auth->is_auth()){
                    $this->redirect();
                }
            }

            $this->before_init();
        }
    }

    protected function before_init(){

    }


    protected function set_api(){
        $this->is_api = true;
        view::set_render_type('json');
    }

    public function redirect($pach = null){

        if($pach != null){
            $pach.= '/';
        }

        $url = SUB_URL.mb_strtolower($pach);
        header('Location: '.$url);
        exit();
    }

    public function redirect_to_method($pach = null){

        if($pach != null){
            $pach.= '/';
        }

        $url = SUB_URL.$this->std_class_name.'/'.mb_strtolower($pach);
        header('Location: '.$url);
        exit();
    }

}