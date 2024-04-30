<?

namespace core\engine;


use mysql_xdevapi\Exception;

class view{

    public static $title = null;
    public static $description = null;
    public static $keywords = null;

    private static $layout = 'main';

    private static $tpl_name = 'main';
    private static $module_path = null;

    private static $main_content = null;

    private static $loaded_tpl = null;
    private static $loaded_layout = null;

    private static $scripts = [];

    private static $render_type = 'normal';

    public static function set_render_type($type){

        if($type == 'json'){
            header('Content-Type: application/json; charset=utf-8');
            self::$render_type = $type;
        }


    }

    public static function is_json(){
        if(self::$render_type == 'json'){
            return true;
        }
        return false;
    }

    public static function set($tpl_name, $module_path = null){
        if(!empty($tpl_name)){
            self::$tpl_name = $tpl_name;
        }
        if($module_path != null){
            self::$module_path = $module_path;
        }

        debugger::log(['set_tpl' => [
            'tpl_name' => $tpl_name,
            'module_path' => $module_path,
        ]]);
    }

    protected static function get_tpl_content(){

        if(self::$render_type == 'normal'){

            $module_tpl = APP.self::$module_path.self::$tpl_name.'.php';
            $root_tpl   = TEMPLATES.self::$tpl_name.'.php';


            debugger::log(['request_tpl' => [
                'module_tpl' => $module_tpl,
                'root_tpl' => $root_tpl,
            ]]);

            debugger::timeline_start('pre_render_content');

            ob_start();

            if(file_exists($module_tpl)){
                self::$loaded_tpl = ['loaded_tpl' => $module_tpl];

                require($module_tpl);



            }else{

                if(file_exists($root_tpl)){
                    self::$loaded_tpl = ['loaded_tpl' => $root_tpl];
                    require($root_tpl);
                }else{
                    debugger::error('Шаблон: '.self::$module_path.self::$tpl_name.' Не найден');
                }

            }

            $content = ob_get_clean();
            $content = self::cut_scripts($content);
            self::$main_content = $content;

            debugger::log(self::$loaded_tpl);
            debugger::timeline_stop('pre_render_content');

        }

    }


    protected static function cut_scripts($content){
        $pattern = "#<script.*?>.*?</script>#si";

        $scripts_arr = [];

        preg_match_all($pattern, $content, $scripts_arr);

        if(!empty($scripts_arr)){
            self::$scripts = array_merge(self::$scripts, $scripts_arr);
            $content = preg_replace($pattern,'', $content);
        }

        return $content;

    }


    public static function render_scripts(){
        if(!empty(self::$scripts)){
            foreach (self::$scripts as $scripts){

                if(is_array($scripts)){

                    foreach ($scripts as $script){
                        echo $script;
                    }

                }else{
                    echo $scripts;
                }

            }
        }
    }

    protected static function render_layout($layout){

        if(self::$render_type == 'normal'){

            self::$layout = $layout;

            $layout_path_raw = explode('/', self::$module_path);


            $layout_path = '';
            if(isset($layout_path_raw[0]) && isset($layout_path_raw[1])){
                $layout_path = $layout_path_raw[0].'/'.$layout_path_raw[1];
            }


            $root_layout = TEMPLATES.'layout/'.self::$layout.'.php';
            $module_layout = $file = APP.$layout_path.'/layout/'.self::$layout.'.php';


            debugger::log(['request_layout' => [
                'module_layout' => $module_layout,
                'root_layout' => $root_layout,
            ]]);

            if(file_exists($module_layout)){

                self::$loaded_layout = ['loaded_layout' => $module_layout];
                debugger::log(self::$loaded_layout);
                require($module_layout);

            }else{
                if(file_exists($root_layout)){

                    self::$loaded_layout = ['loaded_layout' => $root_layout];
                    debugger::log(self::$loaded_layout);
                    require($root_layout);

                }else{

                    debugger::error('layout: '.$layout.' Не найден');

                }
            }

        }

    }

    public static function get_meta()
    {
        if (!DATA::has('meta_page')) {
            set_meta_page();
        }

        return DATA::get('meta_page');
    }


    public static function get_content(){
        return self::$main_content;
    }


    public static function block($name, $module = null, $args = [])
    {
        $root_block = TEMPLATES.'blocks/'.$name.'.php';
        $module_block = APP.$module.'/templates/blocks/'.$name.'.php';

        if ($module && file_exists($module_block)) {
            static::render_template($module_block, $args);
            return;

        } else if (file_exists($root_block)) {
            static::render_template($root_block, $args);
            return;
        }

        debugger::error($module
            ? 'Блок: '.$name.' в модуле: '.$module.' не существует'
            : 'Блок: '.$name.' не существует'
        );
    }

    private static function render_template($_path, $args = [])
    {
        unset($args['_path']);

        extract($args);

        require $_path;
    }

    public static function render($layout = 'main'){

        debugger::timeline_start('render');

        ob_start("sanitize_output");

        self::get_tpl_content();
        self::render_layout($layout);

        ob_end_flush();

        exit();

    }

    public static function set_notification($type, $data){

        self::create_notyfication_session();

        $buttons = array();

        if(isset($data['buttons'])){
            $buttons = $data['buttons'];
        }

        $_SESSION['notifications'][] = array(
            'type' => $type,
            'title' => $data['title'],
            'content' => $data['content'],
            'buttons' => $buttons,
        );

    }

    public static function get_notifications(){

        self::create_notyfication_session();

        $notifications = $_SESSION['notifications'];

        unset($_SESSION['notifications']);

        return $notifications;


    }

    private static function create_notyfication_session(){
        if(!isset($_SESSION['notifications'])){
            $_SESSION['notifications'] = [];
        }
    }


    public static function validator_errors($all_errors){

        foreach ($all_errors as $f_name => $errors){
            foreach ($errors as $index => $error){
                view::set_notification('error', [
                    'title' => 'Ошибка поля ввода',
                    'content' => 'Не корректно поле: '.$f_name.' - '.$index
                ]);
            }
        }

    }



}

