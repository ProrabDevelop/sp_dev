<?

namespace core\engine;

class DATA{

    private static $container = [];
    /*
    protected static $_instance = null;

    public static function init(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct(){
        return $this;
    }
    */

    public static function set($key, $value){
        if(!isset(self::$container[$key])){
            self::$container[$key] = $value;
            return true;
        }
        debugger::error('уже существует ключ <b>'.$key.'</b> DI контейнера');
        return false;
    }

    public static function get($key){
        if(isset(self::$container[$key])){
            return self::$container[$key];
        }
        debugger::error('Не существует ключа <b>'.$key.'</b> в DI контейнере');
        return false;
    }

    public static function has($key){
        if(isset(self::$container[$key])){
            return true;
        }
        return false;
    }

    public static function debug(){
        dump(self::$container);
    }

}