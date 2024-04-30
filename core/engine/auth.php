<?php

namespace core\engine;

use app\std_models\user;
use http\Cookie;
use function Sodium\add;

class AUTH{

    protected static $_instance = null;

    protected static $session_data = [];

    protected static $_auth = false;

    protected $session;

    public ?user $user = null;

    public static function init(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct(){
        $this->JWT_session_start();
        return $this;
    }





    public static function admin_with_user($user_id){
        return self::create_admin_jwt($user_id);
    }

    public static function auth_admin_with_user($ses_id){

        $session = \ORM::for_table('sessions')->find_one($ses_id);

        if($session){
            self::$_instance->unset_all_cookies();
            setcookie('jwt', $session->jwt, time()+300, '/');
            setcookie('udata', $session->data, time()+300, '/');
        }



    }





    public function destroy_all_user_session($user_id){
        $sessions = \ORM::for_table('sessions')->where('uid', $user_id)->find_many();

        if($sessions){
            foreach ($sessions as $session){

                $session->delete();

            }
        }
    }


    public function get_id_by_jwt($jwt){
        $session = \ORM::for_table('sessions')->where('jwt', $jwt)->find_one();

        if($session){
            return $session->uid;
        }

        return false;
    }


    public function get_session_ip(){

        if($this->session){
            return $this->session->ip;
        }

        return null;
    }


    public function create_jwt($id , $sub_data = []){

        $udata = [
            'id' => $id,
            'salt' => generate_pass(16),
        ];

        foreach($sub_data as $key => $val){
            $udata[$key] = $val;
        }

        $jwt = hash('sha256', hash('sha256', json_encode($udata, JSON_UNESCAPED_UNICODE).JWT_PRIVATE));

        $this->save_session($jwt, $udata);

        return [
            'jwt' => $jwt,
            'udata' => $udata,
        ];

    }


    public static function create_admin_jwt($id){

        $udata = [
            'id' => $id,
            'salt' => generate_pass(16),
        ];
        $jwt = hash('sha256', hash('sha256', json_encode($udata, JSON_UNESCAPED_UNICODE).JWT_PRIVATE));

        $ses_id = self::$_instance->save_session($jwt, $udata, true);

        return $ses_id;

    }


    protected function unset_all_cookies(){
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
    }



    protected function save_session($jwt, $udata, $is_admin = false){
        $session = \ORM::for_table('sessions')->create();
        $session->inited = (new \DateTime())->format('Y-m-d H:i:s');
        $session->uid = $udata['id'];
        $session->jwt = $jwt;
        $session->data = json_encode($udata, JSON_UNESCAPED_UNICODE);
        $session->ip = getIp();

        if($is_admin){
            $session->is_admin = 1;
        }

        $session->save();

        return $session->id;
    }

    protected function is_correct_jwt(){

        if($this->check_signature()){
            if($this->check_session()){
                return true;
            }
        }

        return false;
    }

    protected function check_session(){

        $udata = json_decode_to_obj($_COOKIE['udata']);

        $session = \ORM::for_table('sessions')->where([
            'uid' => $udata->get('id'),
            'jwt' => $_COOKIE['jwt'],
        ])->find_one();

        if($session){

            $this->session = $session;

            if($session->is_admin == 1){
                if((new \DateTime($session->inited))->add(new \DateInterval('PT5M')) <= new \DateTime()){
                    return false;
                }
            }

            return true;
        }
        return false;
    }

    protected function check_signature(){
        if(hash('sha256', hash('sha256', $_COOKIE['udata'].JWT_PRIVATE)) == $_COOKIE['jwt']){
            return true;
        }
        return false;
    }

    protected function remap_data($udata){
        foreach ($udata as $key => $value){
            self::set_session_data($key, $value);
        }
    }

    public function destroy_session(){
        self::$_auth = false;
        setcookie('jwt', null, -1, '/');
        setcookie('udata', null, -1, '/');
    }

    protected function JWT_session_start(){

        if(isset($_COOKIE['jwt']) && isset($_COOKIE['udata'])){

            if($this->is_correct_jwt()){

                $udata = json_decode($_COOKIE['udata'], true);


                /* @var $user user*/
                $user = new user($udata['id']);
                $user->update_last_visit();

                $this->user = $user;

                if(!$this->user){
                    $this->destroy_session();
                }

                self::$_auth = true;

                //if($this->user->id == 1){
                //    setcookie('no_err', 1, -1, '/');
                //}

                DATA::set('USER', $this->user);
                $this->remap_data($udata);


            }else{
                $this->destroy_session();
            }

        }

    }

    public function set_session_data($key, $value){

        if(!isset(self::$session_data[$key])){
            self::$session_data[$key] = $value;
        }

    }

    public function get_all_keys(){
        return array_keys(self::$session_data);
    }

    public function get_session_data($key){

        if(!isset(self::$session_data['id'])){
            if(isset($_COOKIE['udata'])){

                $data = json_decode($_COOKIE['udata'], true);

                if(isset($data[$key])){
                    return $data[$key];
                }else{
                    return false;
                }

            }else{
                return false;
            }
        }



        if(isset(self::$session_data[$key])){
            return self::$session_data[$key];
        }
        return false;
    }




    public function is_auth(){
        return self::$_auth;
    }


}