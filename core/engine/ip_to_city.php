<?

namespace core\engine;


use core\engine\adapters\dadata;
use core\engine\interfaces\i_ip_to_city;

class IP_to_city{

    private i_ip_to_city $_adapter;

    protected city $city;

    protected function config(){
        $this->_adapter = new dadata();
    }

    public function __construct($ip){

        $this->config();

        $city_model = new city();
        $city = $city_model->check_ip($ip, $this->_adapter);


        $this->city = $city;

        return $this;
    }

    public function get_city_data(){
        return $this->city;
    }



}