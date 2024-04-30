<?

namespace app\users;

use app\std_models\slim_user;


use app\std_models\dialog;
use app\std_models\dialogs_list;
use app\std_models\message;


use core\engine\API;
use core\engine\APP;
use core\engine\DATA;


use core\engine\std_module;




class user extends std_module{

    public $active = true;
    public $forauth = true;
    public $is_api = true;

    protected $routes = [
        '/users' => [
            '/' => 'main',
            '/favorite/' => 'favorite',
            '/workwith/' => 'work_with',
            '/preparation_work_with/' => 'preparation_work_with',
            '/getinfo/' => 'main',
            '/getinfo/{id:\d+}/' => 'getinfo',

            '/add_spec/' => 'add_spec',
            '/last_visit/' => 'main',
            '/last_visit/{id:\d+}/' => 'last_visit',

        ],
    ];

    public function main(){
        API::response(['message' => 'users main api']);
    }


    public function last_visit($data){

        $time = (new slim_user($data['id']))->last_visit;

        if($time){
            $last_visit_text = 'онлайн '.human_date($time, 600);
        }else{
            $last_visit_text = 'Давно не был в сети';
        }



        API::response([
            'last_visit' => $last_visit_text,
        ]);

    }

    public function favorite(){
        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            /* @var $user \app\std_models\user*/
            $user = DATA::get('USER');

            $favorites = $user->customer_data->get('favorites');

            if(empty($favorites)){
                $favorites = [];
            }

            $subscribe = false;
            if(false !== $key = array_search($fields->id, $favorites)){
                unset($favorites[$key]);
            }else{
                $subscribe = true;
                $favorites[] = $fields->id;
            }

            $user->customer_data->set('favorites', $favorites);
            $user->customer_data->save_data();

            API::response(['id' => $fields->id, 'subscribe' => $subscribe]);

        });

    }

    public function preparation_work_with(){

        $fields = \validator::ALL_POST([
            'dialog_id' => ['req', 'int'],
            'this_user_id' => ['req', 'int'],
            'whom_user_id' => ['req', 'int'],
            'sender' => ['req', 'int'],
            'type' => ['req', ['enum', ['text', 'media']]],
            'body' => ['req', 'str'],
            'status' => ['req', 'int'],
            'role' => ['req', 'str'],
            'spec_id' => ['req', 'int'],
        ]);



        //проверяем есть ли диалог
        $dialog_msg = new dialog();

        $dialog_id = $dialog_msg->isset_dialog($fields->this_user_id, $fields->whom_user_id);

        if(!$dialog_id){
            $dialog_msg->user_id_1 = $fields->this_user_id;
            $dialog_msg->user_id_2 = $fields->whom_user_id;
            $dialog_msg->last_update = (new \DateTime())->format('Y-m-d H:i:s');
            $dialog_msg->create();
            $dialog_id = $dialog_msg->id;
        }
            //DATA::set('selected_dialog', $dialog_id);
        $fields->dialog_id = $dialog_id;

//        if($fields->status == 1){
//            $fields->add_field('body', "Завершил работу");
//        } else if($fields->status == 2){
//            $fields->add_field('body', "Хочу с вами работать!");
//        } else if($fields->status == 3){
//            $fields->add_field('body', "Берусь работать!");
//        } else if($fields->status == 4){
//            $fields->add_field('body', "Завершил работу");
//        }

        API::auto_validate($fields, function (\validator $fields) use ($dialog_msg) {

            //$fields->dialog_id = $dialog_id;
//            $time = (new \DateTime())->format('Y-m-d H:i:s');
//
//            $fields->add_field('time', $time);
//            $fields->add_field('readed', 0);
//            if($fields->role == "master"){
//                $sender = $fields->whom_user_id;
//            } else {
//                $sender = $fields->this_user_id;
//            }
//            $fields->add_field('sender', $sender);
//            $fields->add_field('requested_status', $requested_status);

            //print_r($fields);
           // exit();
            //$dialog = (new dialog($fields->dialog_id));

            //$dialog->last_update = $time;
            //$dialog->update();
            //$dialog->update();
            //if($fields->role == "master"){
            //    $fields->add_field('reader', $fields->this_user_id);
            //} else {
            //    $fields->add_field('reader', $fields->whom_user_id);
            //}
            //$message = (new message())->create($fields);
            //unset($dialog->unread_count);

            API::response([
                'message_id' => 0,
                'dialog' => $dialog_msg,
            ]);





        });



    }


    public function work_with(){
        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'status' => ['req', 'int'],
            'this_user_id' => ['req', 'int'],
            'to_whom_user_id' => ['req', 'int'],
            'status' => ['req', 'int'],
            'spec_id' => ['req', 'int'],
            'role' => ['req', 'str'],
        ]);



        API::auto_validate($fields, function (\validator $fields){

            /* @var $user \app\std_models\user*/
            $user = DATA::get('USER');

            if($fields->status == 2){
                $orders = $user->customer_data->get('orders');
            } else if($fields->status == 1 || $fields->status == 3 || $fields->status == 4){
                $order_master_select = \ORM::for_table('customer_data')->where('user_id', $fields->this_user_id)->find_many();
                $orders = json_decode($order_master_select[0]->_data["orders"], true);
            }



            if(empty($orders)){
                $orders = [];
            }

            $ids = [];
            foreach ($orders as $key => $order){
                if(isset($order['id'])){
                    $ids[$key] = $order['id'];
                }
            }

            $worked = false;

            if($fields->status == 2){
                if(false !== $key = array_search($fields->id, $ids)){
                    unset($orders[$key]);
                }else{
                    $worked = true;
                    $orders[] = [
                        'id' => $fields->id,
                        'date' => (new \DateTime())->format('d.m.Y'),
                        'status' => $fields->status,
                        'this_user_id' => $fields->this_user_id,
                        'to_whom_user_id' => $fields->to_whom_user_id,
                        'spec_id' => $fields->spec_id,
                    ];
                }
            } else if($fields->status == 1){
                $worked = true;
                    foreach($orders AS $key => $val){
                        if($orders[$key]["to_whom_user_id"] == $fields->to_whom_user_id){
                            unset($orders[$key]);
                        }
                    }
            } else if($fields->status == 3  || $fields->status == 4){
                $worked = true;
                foreach($orders AS $key => $val){
                    if($orders[$key]["to_whom_user_id"] == $fields->to_whom_user_id){
                        $orders[$key]["status"] = $fields->status;
                    }
                }
            }

            if($fields->status == 1 || $fields->status == 3 || $fields->status == 4){
                $sql = "UPDATE customer_data SET orders='".json_encode($orders)."' WHERE user_id = '".$fields->this_user_id."'";
                \ORM::raw_execute($sql);
            } else if($fields->status == 2){
                $user->customer_data->set('orders', $orders);
                $user->customer_data->save_data();
            }

            API::response(['id' => $fields->id, 'worked' => $worked]);
        });

    }

    public function getinfo($data){

        $id = $data['id'];

        $user = new slim_user($id);
        if($user){

            $res = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => get_avatar($user->id)
            ];

            API::response($res);
        }

        API::response();


    }

    public function add_spec(){

        $fields = \validator::ALL_POST([
            'user_id' => ['req', 'int'],
            'spec_name' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $new_spec = \ORM::for_table('new_specs')->create();
            $new_spec->user_id = $fields->user_id;
            $new_spec->spec_name = $fields->spec_name;
            $new_spec->save();

            API::response();

        });






    }

}
