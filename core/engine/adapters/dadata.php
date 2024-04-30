<?

namespace core\engine\adapters;



use core\engine\AUTH;
use core\engine\city;
use core\engine\DATA;
use core\engine\interfaces\i_ip_to_city;

class dadata implements i_ip_to_city {

    private static $api_key = '3a91c4bf2fe910255e626dfdd2d1457c3690916f';

    function get_city($ip) : city{

        $ch = curl_init('https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip='.$ip);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token '.self::$api_key));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, true);


        $city = new city();

        if(isset($res['location']) && $res['location'] != null){

            $city->ip                   = $ip;
            $city->name                 = $res['location']['data']['city'];
            $city->final_name           = $res['location']['value'];

            $city->country              = $res['location']['data']['country'];
            $city->country_iso_code     = $res['location']['data']['country_iso_code'];
            $city->federal_district     = $res['location']['data']['federal_district'];
            $city->region_type          = $res['location']['data']['region_with_type'];
            $city->postal_code          = $res['location']['data']['postal_code'];

            $city->area_type            = $res['location']['data']['area_type'];
            $city->area_type_full       = $res['location']['data']['area_type_full'];
            $city->area                 = $res['location']['data']['area'];

            $city->city_type            = $res['location']['data']['city_type'];
            $city->city_type_full       = $res['location']['data']['city_type_full'];


            $city->create();

        }


        return $city;

    }



}