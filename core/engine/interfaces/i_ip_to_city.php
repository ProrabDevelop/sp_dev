<?

namespace core\engine\interfaces;

use core\engine\city;

interface i_ip_to_city{

    function get_city($ip) : city;

}