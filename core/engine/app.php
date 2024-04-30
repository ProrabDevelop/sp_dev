<?

namespace core\engine;

use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

class APP{

    protected static $_instance = null;

    protected $routes = [];
    protected $uri;

    public static function init(){

        if(self::$_instance == null){
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    protected function __construct(){

        if(SUB == 'admin'){
            ADMIN_AUTH::init();
        }else{
            AUTH::init();
        }

        $this->clean_uri();
        $this->remap_GET();

        $bandles = module::init();

        debugger::timeline_start('get_routs');

        $this->routes = $bandles->get_routs();

        debugger::timeline_stop('get_routs');

    }

    public function run(){

        $this->get_module();

    }

    protected function clean_uri(){

        $uri = $_SERVER['REQUEST_URI'];

        if(false !== $pos = strpos($uri, '?')){
            $uri = substr($uri, 0, $pos);
        }

        $this->uri = rawurldecode($uri);
    }

    protected function remap_GET(){

        $_GET = array_filter($_GET);

        foreach ($_GET as $key => $get){
            $_GET[$key] = trim($get, '\\/');
        }

        pagination::set_base_url($this->uri);
        if(isset($_GET['page'])){
            if(is_numeric($_GET['page'])){
                pagination::set_page((int) $_GET['page']);
            }
        }

        if(isset($_GET['format'])){
            if($_GET['format'] == 'json'){
                view::set_render_type('json');
            }
        }

    }

    protected function get_module(){

        $dispatcher = simpleDispatcher(new RouteMapper($this->routes));
        //$httpmethod = $_SERVER['REQUEST_METHOD'];
        $httpmethod = 'GET';

        $route_info = $dispatcher->dispatch($httpmethod, $this->uri);

        debugger::log(['modules_routs' => $this->routes]);
        debugger::log(['route_data' => $route_info]);

        switch ($route_info[0]){

            case Dispatcher::NOT_FOUND:
                $this->load_404();
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                echo '<h1>405</h1>';
                break;

            case Dispatcher::FOUND:
                $this->load_module($route_info);
                break;

        }

    }

    protected function load_404(){
        http_response_code(404);
        view::set('er404');
        view::render();
    }

    protected function load_module($rout_info){
        $controller_info = $rout_info[1];
        $data = $rout_info[2];

        $method = $controller_info['do'];

        $bandle_namespace = $controller_info['module_name'];

        $bandle_raw_path = explode('\\', $bandle_namespace);

        $sub_module_name = end($bandle_raw_path);

        array_shift($bandle_raw_path);
        array_pop($bandle_raw_path);

        $bandle_template_path = implode('/', $bandle_raw_path).'/templates/'.$sub_module_name.'/';
        $bandle = new $controller_info['module_name']();

        if(method_exists($bandle, $method)){

            $reflection = new \ReflectionClass($controller_info['module_name']);

            $bandle_method_reflect = $reflection->getMethod($method);
            $params_count = $bandle_method_reflect->getNumberOfParameters();

            if(!$bandle->is_api){
                view::set($method, $bandle_template_path);
            }

            if($params_count > 0){
                $bandle->$method($data);
            }else{
                $bandle->$method();
            }


            view::render($bandle->layout);

        }



        if(method_exists($bandle, 'er404')){

            view::set('er404', $bandle_template_path);

            $bandle->er404();

            view::render($bandle->layout);

        }

        $this->load_404();




    }

}
