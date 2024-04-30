<?php

namespace core\engine;

class old_dadata{

    private static $api_key = '3a91c4bf2fe910255e626dfdd2d1457c3690916f';

    public static function get_city(){

        //$ch = curl_init('https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip='.$_SERVER['REMOTE_ADDR']);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token '.self::$api_key));
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_HEADER, false);
        //$res = curl_exec($ch);
        //curl_close($ch);

        //$res = json_decode($res, true);

        //$dest['final_name'] = $res['location']['value'];

        //$dest['country'] = $res['location']['data']['country'];
        //$dest['country_iso_code'] = $res['location']['data']['country_iso_code'];
        //$dest['federal_district'] = $res['location']['data']['federal_district'];
        //$dest['region_type'] = $res['location']['data']['region_with_type'];
        //$dest['postal_code'] = $res['location']['data']['postal_code'];

        //$dest['area_type'] = $res['location']['data']['area_type'];
        //$dest['area_type_full'] = $res['location']['data']['area_type_full'];
        //$dest['area'] = $res['location']['data']['area'];

        //$dest['city_type'] = $res['location']['data']['city_type'];
        //$dest['city_type_full'] = $res['location']['data']['city_type_full'];
        //$dest['city'] = $res['location']['data']['city'];



    }


}