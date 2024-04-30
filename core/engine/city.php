<?

namespace core\engine;

use core\engine\interfaces\i_ip_to_city;

class city extends std_model{

    public $id, $ip, $name, $final_name, $country, $country_iso_code,
        $federal_district, $region_type, $postal_code, $area_type,
        $area_type_full, $area, $city_type, $city_type_full;

    protected $table_name = 'cityes';

    public function check_ip($ip, i_ip_to_city $city_adapter){

        $city_data = \ORM::for_table($this->table_name)->where('ip', $ip)->find_one();

        if($city_data){

            $this->set_ormdata($city_data);
            return $this;

        }else{
            $city_data = $city_adapter->get_city($ip);

            if($city_data){
                return $city_data;
            }else{
                new city();
            }
        }

    }

}