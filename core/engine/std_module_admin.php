<?

namespace core\engine;

class std_module_admin{


    protected $active = true;
    protected $routes = [];
    public $layout = 'main';
    public $std_class_name = null;


    public $is_api = false;
    protected $auth = null;
    protected $forauth = true;

    public function method_trigger($method_name){

        //$method_name

        if($this->forauth){
            if(!$this->auth->can($method_name)){
                $this->redirect('');
            }
        }




    }


    public function __construct($get_info = false){

        $this->auth = \core\engine\ADMIN_AUTH::init();

        $full_class_name = explode('\\', get_called_class());
        $this->std_class_name = end($full_class_name);

        if($get_info){
            if($this->active){

                module::reg_module(get_called_class(), [
                    'layout' => $this->layout,
                    'routes' => $this->routes,
                ]);

                $this->before_init();


            }

        }else{
            if($this->std_class_name == 'auth'){
                if($this->auth->is_auth()){
                    $this->redirect('dashboard');
                }
            }else{

                if($this->forauth){
                    if(!$this->auth->is_auth()){
                        $this->redirect('login');
                    }
                }
            }
        }
    }

    protected function before_init(){

    }

    public function redirect_to_method($pach = null){

        if($pach != null){
            $pach.= '/';
        }

        $url = SUB_URL.$this->std_class_name.'/'.mb_strtolower($pach);

        header('Location: '.$url);
    }

    public function redirect($pach = null){

        if($pach != null){
            $pach.= '/';
        }

        $url = SUB_URL.mb_strtolower($pach);
        //dump($url);
        header('Location: '.$url);
    }

}