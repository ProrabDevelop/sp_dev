<?

namespace core\engine;

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;

class debugger{

    protected static $_instance = null;

    protected static $enabled = false;

    protected static $debugger = null;
    protected static $debugger_renderer = null;


    public static function init($config = false){

        if(self::$_instance == null){
            self::$_instance = new self($config);
        }

        return self::$_instance;

    }

    protected function __construct($config){

        if($config){





            self::$enabled = true;

            self::$debugger = new DebugBar();



            self::$debugger_renderer = self::$debugger->getJavascriptRenderer(URL.'debugbur/');

            self::$debugger->addCollector(new PhpInfoCollector());
            self::$debugger->addCollector(new MessagesCollector());
            self::$debugger->addCollector(new RequestDataCollector());
            self::$debugger->addCollector(new TimeDataCollector());
            self::$debugger->addCollector(new MemoryCollector());
            //self::$debugger->addCollector(new ExceptionsCollector());

        }

    }

    public static function enabled(){
        if(self::$enabled){
            return true;
        }
        return false;
    }

    public static function render_head(){
        if(self::$enabled){
            $debugbar_render = self::$debugger_renderer;


            ob_start();

            echo '<style>';
            $debugbar_render->dumpCssAssets();
            echo '</style>';

            echo '<script>';
            $debugbar_render->dumpJsAssets();
            echo '</script>';

            echo '<link rel="stylesheet" href="'.SUB_URL.'assets/css/debugbar.css">';

            echo ob_get_clean();

        }
    }

    public static function render(){
        if(self::$enabled){
            echo self::$debugger_renderer->render();
        }
    }

    public static function log($message){
        if(self::$enabled){
            self::$debugger['messages']->info($message);
        }
    }

    public static function error($message){
        if(self::$enabled){
            self::$debugger['messages']->error($message);
        }
    }

    public static function warning($message){
        if(self::$enabled){
            self::$debugger['messages']->warning($message);
        }
    }

    public static function timeline_start($op_name, $description = null){
        if(self::$enabled){
            self::$debugger['time']->startMeasure($op_name, $description);
        }
    }

    public static function timeline_stop($op_name){
        if(self::$enabled){
            self::$debugger['time']->stopMeasure($op_name);
        }
    }



}