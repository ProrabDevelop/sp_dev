<?


use core\engine\DATA;

function get_domain(){

    $tmp = explode('.', $_SERVER['HTTP_HOST']);

    if(count($tmp) == 2){
        return $_SERVER['HTTP_HOST'];
    }

    $offset = count($tmp) - 2;
    $tmp = array_slice($tmp, $offset, count($tmp));

    return implode('.', $tmp);

}



function get_sub_domain(){

    $tmp = explode('.', $_SERVER['HTTP_HOST']);
    $tmp = array_slice($tmp, 0, -2);

    $str = implode('.', $tmp);

    if($str == '.'){
        return false;
    }

    return mb_strtolower($str);
}

function generate_pass($number=8){
    $arr = array('a','b','c','d','e','f',
        'g','h','i','j','k','l',
        'm','n','o','p','r','s',
        't','u','v','x','y','z',
        'A','B','C','D','E','F',
        'G','H','I','J','K','L',
        'M','N','O','P','R','S',
        'T','U','V','X','Y','Z',
        '1','2','3','4','5','6',
        '7','8','9','0');

    $pass = "";
    for($i = 0; $i < $number; $i++)
    {
        $index = rand(0, count($arr) - 1);
        $pass .= $arr[$index];
    }
    return $pass;
}

function GUIDv4 ($trim = true){
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }

    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace.
        substr($charid,  0,  8).$hyphen.
        substr($charid,  8,  4).$hyphen.
        substr($charid, 12,  4).$hyphen.
        substr($charid, 16,  4).$hyphen.
        substr($charid, 20, 12).
        $rbrace;
    return $guidv4;
}

function getIp() {
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    ];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $srv = explode(',', $_SERVER[$key]);
            $ip = trim(end($srv));
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
}

function json_unformat($string) {

    if(!is_array($string) && !is_object($string)){
        if (is_array(json_decode($string, true))){
            return json_decode($string, true);
        }
    }
    return $string;
}

function get_content(){
    echo \core\engine\view::get_content();
}
function get_meta(){
    return \core\engine\view::get_meta();
}

function get_block($name, $module = null, $args = [])
{
    \core\engine\view::block($name, $module, $args);
}

function render_scripts(){
    \core\engine\view::render_scripts();
}


function temp($data){

    $temp = ORM::for_table('temp')->create();
    $temp->data = json_encode($data, JSON_UNESCAPED_UNICODE);
    $temp->save();
}

function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}


function sanitize_output($buffer) {


    if (!minify_html){
       return $buffer;
    }

    $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );

    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
}

function get_city_name($id){

    $city = ORM::for_table('cityes_for_search')->find_one($id);
    if($city){
        return $city->name;
    }

    return false;
}



function data(){
    return \core\engine\DATA::init();
}


class json_obj{

    public function set($key, $val){
        $this->$key = $val;
    }

    public function set_json($json){

        $temp_data = json_decode($json, true);
        if($temp_data){
            foreach ($temp_data as $key => $val){
                $this->$key = $val;
            }
        }

        return $this;
    }

    public function get($key, $ret_in_false = null){
        if(isset($this->$key)){
            return $this->$key;
        }
        return $ret_in_false;
    }

}

function json_decode_to_obj($json): json_obj{

    $json_class = new json_obj();
    return $json_class->set_json($json);

}

function r_date($date_time, $format = 'j M Y', $cut_year = true) {

    $time = strtotime($date_time);

    $end = ' года';
    if($cut_year && date('Y') == date('Y', $time)){
        $format = preg_replace('/o|y|Y/', '', $format);
        $end = '';
    }
    $month = abs(date('n', $time)-1);
    $rus = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    $rus2 = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
    $format = preg_replace(array("'M'", "'F'"), array($rus[$month], $rus2[$month]), $format);

    return date($format, $time).$end;

}

function declension($number, $words) {
    $number = abs($number);
    if ($number > 20) $number %= 10;
    if ($number == 1) return $words[0];
    if ($number >= 2 && $number <= 4) return $words[1];
    return $words[2];
}

function is_online_by_date($timestamp, $correction = 0){
    $current_time = time();
    $target_time = strtotime($timestamp);

    if($current_time >= $target_time) {
        $timestamp = abs($current_time - $target_time);
    }else{
        $timestamp = abs($target_time - $current_time);
    }

    if($timestamp <= $correction){
        return true;
    }



    return false;
}

function human_date_status($timestamp, $correction = 0) {
    $current_time = time();
    $target_time = strtotime($timestamp) + $correction;

    if(!$target_time){
        return '<span class="offline">Не в сети</span>';
    }

    $output_arr = [];
    $output = '';

    if($current_time >= $target_time) {
        $timestamp = abs($current_time - $target_time);
        $tense = 'past';
    } else {
        $timestamp = abs($target_time - $current_time);
        $tense = 'future';
    }
    $units = array('years' => 31536000,
        'weeks' => 604800,
        'days' => 86400,
        'hours' => 3600,
        'min' => 60,
        'sec' => 1);
    $titles = array('years' => array('год', 'года', 'лет'),
        'weeks' => array('неделя', 'недели', 'недель'),
        'days' => array('день', 'дня', 'дней'),
        'hours' => array('час', 'часа', 'часов'),
        'min' => array('минута', 'минуты', 'минут'),
        'sec' => array('секунда', 'секунды', 'секунд'));



    foreach ($units as $key => $value) {
        if(count($units)){
            if ($timestamp >= $value) {
                $number = floor($timestamp / $value);
                $output_arr[] = $number .' '. declension($number, $titles[$key]);
                $timestamp %= $value;
            }
        }
    }

    if(isset($output_arr[0]) && isset($output_arr[1]) && count($output_arr) != 6){
        $output .= $output_arr[0].' '.$output_arr[1];
    }elseif (isset($output_arr[0]) && isset($output_arr[1]) && count($output_arr) == 6){
        $output .= $output_arr[0];
    }elseif (isset($output_arr[0])){
        $output .= $output_arr[0];
    }

    if($tense == 'future') {
        return '<span class="online">В сети</span>';
    } else {
        $output .= ' назад';
    }

    if($output == ' назад'){
        return '<span class="online">В сети</span>';
    }

    $output = '<span class="offline">Был в сети: '.$output.'</span>';

    return $output;
}

function human_date_status_miminal($timestamp, $correction = 0){
    $current_time = time();
    $target_time = strtotime($timestamp) + $correction;

    if(!$target_time){
        return '<span class="offline">Сейчас не в сети</span>';
    }

    $output_arr = [];
    $output = '';

    if($current_time >= $target_time) {
        $timestamp = abs($current_time - $target_time);
        $tense = 'past';
    } else {
        $timestamp = abs($target_time - $current_time);
        $tense = 'future';
    }
    $units = array('years' => 31536000,
        'weeks' => 604800,
        'days' => 86400,
        'hours' => 3600,
        'min' => 60,
        'sec' => 1);
    $titles = array('years' => array('год', 'года', 'лет'),
        'weeks' => array('неделя', 'недели', 'недель'),
        'days' => array('день', 'дня', 'дней'),
        'hours' => array('час', 'часа', 'часов'),
        'min' => array('минута', 'минуты', 'минут'),
        'sec' => array('секунда', 'секунды', 'секунд'));



    foreach ($units as $key => $value) {
        if(count($units)){
            if ($timestamp >= $value) {
                $number = floor($timestamp / $value);
                $output_arr[] = $number .' '. declension($number, $titles[$key]);
                $timestamp %= $value;
            }
        }
    }

    $output .= $output_arr[0];

    if($tense == 'future') {
        return '<span class="online">Сейчас на сайте</span>';
    } else {
        $output .= ' назад';
    }

    if($output == ' назад'){
        return '<span class="online">Сейчас на сайте</span>';
    }

    $output = 'Был в сети: <span>'.$output.'</span>';

    return $output;
}

function human_date($timestamp, $correction = 0) {
    $current_time = time();
    $target_time = strtotime($timestamp) + $correction;

    if(!$target_time){
        return false;
    }

    $output_arr = [];
    $output = '';

    if($current_time >= $target_time) {
        $timestamp = abs($current_time - $target_time);
        $tense = 'past';
    } else {
        $timestamp = abs($target_time - $current_time);
        $tense = 'future';
    }
    $units = array('years' => 31536000,
        'weeks' => 604800,
        'days' => 86400,
        'hours' => 3600,
        'min' => 60,
        'sec' => 1);
    $titles = array('years' => array('год', 'года', 'лет'),
        'weeks' => array('неделя', 'недели', 'недель'),
        'days' => array('день', 'дня', 'дней'),
        'hours' => array('час', 'часа', 'часов'),
        'min' => array('минута', 'минуты', 'минут'),
        'sec' => array('секунда', 'секунды', 'секунд'));



    foreach ($units as $key => $value) {
        if(count($units)){
            if ($timestamp >= $value) {
                $number = floor($timestamp / $value);
                $output_arr[] = $number .' '. declension($number, $titles[$key]);
                $timestamp %= $value;
            }
        }
    }

    if(isset($output_arr[0]) && isset($output_arr[1]) && count($output_arr) != 6){
        $output .= $output_arr[0].' '.$output_arr[1];
    }elseif (isset($output_arr[0]) && isset($output_arr[1]) && count($output_arr) == 6){
        $output .= $output_arr[0];
    }elseif (isset($output_arr[0])){
        $output .= $output_arr[0];
    }

    if($tense == 'future') {
        return false;
    } else {
        $output .= ' назад';
    }

    return $output;
}


function get_age($birthday, $open = '', $close = ''){

    if(empty($birthday) || $birthday == '0000-00-00'){
        return 'не указан';
    }

    $birthday_DT = new DateTime($birthday);
    $now = new DateTime();
    $interval = $now->diff($birthday_DT);

    if($interval->y != 0){
        return $open.$interval->y.' '.declension($interval->y, ['год', 'года', 'лет']).$close;
    }

}


function num2word($n, $titles) {
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}



function cut_by_lenght($lenght, $content, $end = '') {
    $len = (mb_strlen($content) > $lenght)? mb_strripos(mb_substr($content, 0, $lenght), ' ') : $lenght;
    $cutStr = mb_substr($content, 0, $len);
    $temp = (mb_strlen($content) > $lenght)? $cutStr. $end : $cutStr;
    return $temp;
}

function money_beautiful($money){

    return number_format($money, 0, '', ' ' );
}

function get_avatar($user_id, $default = null)
{
    $avatar = (new \core\engine\media())->create_info($user_id, 'png', 'avatars');

    $file = $avatar->get_path();

    if (file_exists($file)) {
        return $avatar->get_url();
    }

    return $default ?? get_avatar('no_avatar');
}



function render_custom_stars($score){
    for ($i = 0; $i < 5; $i++){
        echo (round($score) <= $i) ? '<i class="icon icon-star gray"></i>' : '<i class="icon icon-star"></i>';
    }
}




function js_str($s){
    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}

function js_array($array){
    $temp = array_map('js_str', $array);
    return '[' . implode(',', $temp) . ']';
}

function js_assoc_array($array){

    $ret = '[';
    foreach ($array as $key => $val){
        $ret.= '"'.$key.'":'.js_str($val).',';
    }
    return $ret.']';
}






function crop_text($word_count, $text){
    $arr = explode( ' ', $text );
    $arr = array_slice( $arr, 0, $word_count );
    return implode( ' ', $arr );
}






function mapping_city_to_finder(){
    if(isset($_COOKIE['city_finder'])){

        if(!isset($_COOKIE['city_finder_for_search'])){
            $city = (new \core\engine\city($_COOKIE['city_finder']));
        }else{
            $city = ORM::for_table('cityes_for_search')->find_one($_COOKIE['city_finder']);
        }

        $cityes_for_search = ORM::for_table('cityes_for_search')->where('name', $city->name)->find_one();

        if($cityes_for_search){
            return $cityes_for_search->id;
        }

    }
    return false;
}

function set_meta_page(?string $route = null, array $replaces = [], array $custom = [])
{
    $meta = require CONFIG.'meta.php';

    $page = $route && !empty($meta['pages'][$route])
        ? $meta['pages'][$route]
        : $meta['pages']['/home'];

    foreach ($page as $key => &$value) {
        if (isset($custom[$key])) {
            $value = $custom[$key];
        }

        foreach ($replaces as $from => $to) {
            $value = str_ireplace('${'.$from.'}', $to, $value);
        }
    }

    $page['meta_title'] = implode($meta['title']['separator'], array_filter([
        $page['title'],
        $meta['title']['root'],
    ]));

    DATA::set('meta_page', $page);
}

function get_default_city()
{
    if (city_available) {
        $availableCities = city_available;

        return ORM::for_table('cityes_for_search')
            ->where('name', reset($availableCities))
            ->find_one();

    } else {
        return (new \core\engine\IP_to_city(getIp()))->get_city_data();
    }
}
