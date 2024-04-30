<?

namespace admin\std_models;
use core\engine\view;

class user{

    public $id, $login, $pass, $last_ip;

    protected $ormdata = null;

    public function __construct($id = null){

        if($id != null){
            $this->get_user($id);
        }
        return $this;
    }

    public function auth(\validator $data){

        $user = $this->get_user_by_login($data->login);
        if($user){
            if(password_verify($data->pass, $user->pass)){
                $this->remap_data($user);
                return $this;
            }else{
                view::set_notification('error',[
                    'title' => 'Ошибка авторизации',
                    'content' => 'Пароль не верен!'
                ]);
            }
        }else{
            view::set_notification('error',[
                'title' => 'Ошибка авторизации',
                'content' => 'Пользователь не найден'
            ]);
        }
    }

    protected function get_user_by_login($login){
        $user = \ORM::for_table('admins')->where('login', $login)->find_one();
        return $user;
    }

    protected function get_user($id){
        $user = \ORM::for_table('admins')->find_one($id);
        if($user){
            $this->remap_data($user);
        }
        return false;
    }

    protected function remap_data($user){

        $this->ormdata = $user;

        foreach ($user->_data as $key => $val){
            $this->$key = $val;
        }

    }

    public function update(){
        if (!$this->ormdata) {
            return;
        }

        foreach ($this->ormdata->_data as $key => $val){
            $this->ormdata->$key = $this->$key;
        }

        $this->ormdata->save();
    }

}
