<?


spl_autoload_register(function ($class){

    $class = mb_strtolower($class);
    $class = str_replace('\\', '/', $class);

    if(file_exists(ROOT.'/'.$class.'.php')){
        require(ROOT.'/'.$class.'.php');
    }

});