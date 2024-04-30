<?

namespace core\engine;

class std_component{

    protected $active = true;
    protected $data = [];
    public $component_class_name = null;

    public function __construct($get_info = false){

        $full_class_name = explode('\\', get_called_class());
        $this->component_class_name = end($full_class_name);

        if($get_info){

            if($this->active){

                component::reg_component(get_called_class(), [
                    'data' => $this->data,
                ]);

            }

        }
    }



}