<?

namespace core\engine;

class module{

    protected static $_instance = null;
    private static $modules = [];

    public static function init(){

        if(self::$_instance == null){
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    protected function __construct(){

        $this->search_all_modules();

    }

    public function get_routs($sub = null){

        $routes = [];

        foreach (self::$modules as $module_name => $module){

            if(isset($module['routes']) && !empty($module['routes'])){

                foreach ($module['routes'] as $key => $rout){
                    $routes[$key] = ['module_name' => $module_name] + $module['routes'][$key];
                }

            }

        }
        return $routes;

    }


    protected function search_all_modules(){


        $bundles = array_diff(scandir(APP), ['.','..']);
        $bundles = array_values($bundles);

        foreach ($bundles as $bundle){

            $modules = array_diff(scandir(APP.'/'.$bundle), ['.','..','src','templates']);
            $modules = array_values($modules);


            foreach ($modules as $module){

                $module_name = substr($module, 0, -4);

                $module_init = SUB.'\\'.$bundle.'\\'.$module_name;

                if(is_subclass_of($module_init, 'core\\engine\\std_module')){
                    (new $module_init(true));
                }
                if(is_subclass_of($module_init, 'core\\engine\\std_module_admin')){
                    (new $module_init(true));
                }

            }

        }

    }


    public static function reg_module($name, $value){
        if(!self::has_module($name)){
            self::$modules[$name] = $value;
            return true;
        }

        debugger::error('Модуль '.$name.' уже существует');
        return false;
    }

    public static function get_module($name){

        if(self::has_module($name)){
            return self::$modules[$name];
        }

        debugger::error('Модуль '.$name.' не зарегестрирован');
        return false;
    }

    public static function has_module($name){
        if(isset(self::$modules[$name])){
            return true;
        }
        return false;
    }


}