<?
namespace admin\dashboard;

use app\std_models\user;
use app\std_models\users_list;
use core\engine\AUTH;
use core\engine\DATA;
use Core\Engine\pagination;
use core\engine\rebuild_rating;
use core\engine\std_module;
use core\engine\std_module_admin;
use core\engine\view;
use PDO;

class users extends std_module_admin {

    public $forauth = true;

    protected $routes = [
        '/users' => [
            '/' => 'all_users',
        ],
        '/user' => [
            '/{id:\d+}/' => 'auth_with_user',
            '/show/{id:\d+}/'=> 'show_profile',
            '/restore/{id:\d+}/' => 'restore',
            '/delete/{id:\d+}/' => 'delete',
        ],
    ];

    public function all_users(){


        $fields = \validator::ALL_POST([
            'user_id' => ['req', 'int'],
            'type_ip' => ['checked'],
            'type_ooo' => ['checked'],
        ]);

        if($fields->POST()){

            if(!$fields->errors){

                $user = new user($fields->user_id);

                if($fields->type_ip){
                    $fields->type_ip = 'true';
                }else{
                    $fields->type_ip = 'false';
                }
                if($fields->type_ooo){
                    $fields->type_ooo = 'true';
                }else{
                    $fields->type_ooo = 'false';
                }
                $user->master_data->update($fields);

                (new rebuild_rating())->rebuild_user($user->id);

                view::set_notification('success', [
                    'title' => 'Сохранения',
                    'content' => 'Профиль пользователя обновлен и перепросчитан'
                ]);

            }

        }




                //$user->master_data->get('type_ooo')
                //$user->master_data->get('type_ip')






        $this->get_users();
    }

    public function show_profile($data)
    {
        $user_id = $data['id'] ?? null;
        $user = \ORM::for_table('users')->find_one($user_id);

        if (!$user) {
            $this->redirect();
            exit;
        }

        DATA::set('profile', $user);
    }

    public function restore($data)
    {
        $user_id = $data['id'] ?? null;

        $user = new \app\std_models\user($user_id);

        if ($user->restore()) {
            view::set_notification('success', [
                'title' => 'Успешное сохранение',
                'content' => sprintf('Пользователь #%d восстановлен.', $user_id),
            ]);
        } else {
            view::set_notification('error', [
                'title' => 'Ошибка сохранения',
                'content' => sprintf('Пользователь #%d не восстановлен. Дубликат номера телефона.', $user_id),
            ]);
        }

        $this->redirect('users');
    }

    public function delete($data)
    {
        $user_id = $data['id'] ?? null;
        $user = new \app\std_models\user($user_id);

        if ($user->deleted_at) {
            $user->force_delete();
            $message = sprintf('Пользователь #%d полностью удален.', $user_id);
        } else {
            $user->delete();
            $message = sprintf('Пользователь #%d временно удален.', $user_id);
        }

        view::set_notification('success', [
            'title' => 'Успешное сохранение',
            'content' => $message,
        ]);

        $this->redirect('users');
    }

    public function auth_with_user($data)
    {
        if ($_SERVER['HTTP_REFERER'] == 'https://admin.samprorab.com/users/') {
            $user_id = $data['id'] ?? null;

            $user = \ORM::for_table('users')
                ->where_null('deleted_at')
                ->find_one($user_id);

            if ($user) {
                AUTH::init();

                $ses_id = AUTH::admin_with_user((int) $user_id);

                header('Location: '.BASE_URL.'login_with_user/'.$ses_id.'/');
                exit;
            }
        }

        header('Location: '.BASE_URL.'/auth/logout');
        exit;
    }


    protected function get_users(){
        $count = \ORM::for_table('users')->count();

        pagination::set_limit(50);
        pagination::set_total($count);
        $users_raw = \ORM::for_table('users')->limit(pagination::get_limit())->offset(pagination::get_offset())->find_many();

        $users = [];
        $users_ids = [];
        foreach ($users_raw as $user){
            $users_ids[] = $user->id;
        }
        $users = (new users_list())->get($users_ids);

        DATA::set('users', $users);
    }


}
