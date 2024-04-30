<?
namespace app\dashboard;

use app\std_models\catalog_model;
use app\std_models\dialog;
use app\std_models\dialogs_list;
use app\std_models\message;
use app\std_models\review;
use app\std_models\sort_rating_prices;
use app\std_models\user;
use app\std_models\work;
use core\engine\API;
use core\engine\AUTH;
use core\engine\city;
use core\engine\DATA;
use core\engine\rebuild_rating;
use core\engine\std_module;
use core\engine\view;

class dashboard extends std_module {

    public $layout = 'dashboard';

    protected user $user;
    public $forauth = true;

    protected $routes = [
        '/dashboard' => [
            '/' => 'main',
            '/delete/' => 'delete_account',
        ],
        '/messages' => [
            '/' => 'messages',
            '/{id:\d+}/' => 'messages',
        ],
        '/reviews' => [
            '/' => 'reviews',
            '/{id:\d+}/' => 'api_get_single_review',
            '/add_answer/' => 'review_add_answer',
            '/add_complaint/' => 'review_add_complaint',
            '/add_review/' => 'add_review',
        ],
        '/favorites' => [
            '/' => 'favorites',
        ],
        '/history' => [
            '/' => 'history',
        ],
        '/settings' => [
            '/' => 'settings',
        ],
        '/contacts' => [
            '/' => 'contacts',
        ],
        '/prices' => [
            '/' => 'prices',
        ],
        '/finished' => [
            '/' => 'finished',
            '/delete/' => 'delete_finished',
        ],
    ];

    protected function before_init(){

        /* @var user $this->user */
        $this->user = DATA::get('USER');
    }

    //Основной лк
    public function main(){

        if(is_ajax()){

            $rules = [
                'name' => ['req', 'str'],
                'mail' => ['req', 'email'],

                'city' => ['int'],

                'pass' => ['str', 'confirmed', ['min', 8], ['max', 20]],
                'pass_confirm' => ['str'],

                'email_subscribe' => ['checked'],

            ];

            if($this->user->is_master()){

                $rules['birthday'] = ['req', ['date','Y-m-d']];
                $rules['experience'] = ['int'];
                $rules['spec'] = ['arr'];
            }

            $fields = \validator::ALL_POST($rules);

            //не вказан город
            if(empty($fields->city)){
                $fields->delete_field('city');
            }
            //пароли не указаны (оставляем без изменения)
            if(empty($fields->pass)){
                $fields->delete_field('pass');
                $fields->delete_field('pass_confirm');
            }

            API::auto_validate($fields, function ($fields){

                if($fields->email_subscribe == 'on'){
                    $fields->email_subscribe = 1;
                }else{
                    $fields->email_subscribe = 0;
                }

                $city = !empty($fields->city) ? $fields->city : null;

                /* @var user $this->user */
                if($this->user->city != $city){
                    $sort_ratings = \ORM::for_table('sort_rating')->where('user_id', $this->user->id)->find_many();
                    if($sort_ratings){
                        foreach ($sort_ratings as $sort_rating){
                            $sort_rating->city_id = $city;
                            $sort_rating->save();
                        }
                    }
                }


                $this->user->update_profile($fields);
                API::response();
            });

        }

        set_meta_page('/dashboard');
    }

    //Сообщения
    public function messages($data = []){

        if(isset($data['id'])){
            $user_id = $data['id'];
            $dialog = new dialog();

            $dialog_id = $dialog->isset_dialog($this->auth->user->id, $user_id);

            if(!$dialog_id){
                $dialog->user_id_1 = $this->auth->user->id;
                $dialog->user_id_2 = $user_id;
                $dialog->last_update = (new \DateTime())->format('Y-m-d H:i:s');
                $dialog->create();
                $dialog_id = $dialog->id;
            }

            DATA::set('selected_dialog', $dialog_id);

        }

        $this->layout = 'dashboard_transperent_for_messanger';

        if(is_ajax()){
            if($_POST['action'] == 'get_messages'){ //update_readed
                $fields = \validator::ALL_POST([
                    'dialog_id' => ['req', 'int']
                ]);
                API::auto_validate($fields, function (\validator $fields){
                    $messages = (new message())->get_messages_by_dialog($fields->dialog_id);

                    $messages_html = '';

                    ob_start();
                    if($messages){
                        /* @var $message message*/
                       foreach ($messages as $message){

                           $mes_type = 'sended';
                           $avatar_id = $this->user->id;

                           if($this->user->id !== $message->sender){
                               $mes_type = 'incoming';
                               $avatar_id = $message->sender;
                           }
                           ?>
                           <div class="message <?= $mes_type?>">

                               <img class="avatar" src="<?= get_avatar($avatar_id)?>">
                               <p class="body">
                                   <?= $message->body ?>
                                   <? if($this->user->id == $message->sender){?>
                                       <span class="message_status <?= ($message->readed == 1)? ' readed' : ''; ?>"><i class="icon icon-check"></i><i class="icon icon-check"></i></span>
                                   <?}?>
                               </p>
                               <p class="time"><?= (new \DateTime($message->time))->format('d.m.Y H:i:s') ?></p>
                           </div>

                           <?php
                            $status = "";
                            if($message->body == "Отказ от работы"){
                                $status = "Вы отказались от работы";
                            } else if($message->body == "Хочу с вами работать!"){
                                $status = "Ждем подтверждение мастера";
                            } else if($message->body == "Берусь работать!"){
                                $status = "Вы начали работу";
                            } else if($message->body == "Завершил работу"){
                                $status = "Вы завершили работу";
                            }


                            if( $status != ""){
                           ?>



                           <div style="display:inline-block; border-bottom: 1px solid #E8E8E8; width:100%; width: 100%; display: flex; margin-top: -20px; margin-bottom: 20px;">
                            <div style="font-weight: 700; font-size: 13px; line-height: 140%; font-feature-settings: 'tnum' on, 'lnum' on; color: #356AFB; display: initial; margin: auto; width: auto; margin-bottom: -30px; background-color: #fff; padding: 20px;"><?= $status; ?></div>
                        </div>
                       <?
                            }

                    }



/*

            return $this->_join_if_not_empty(" ", array(
                $this->_build_select_start(),
                $this->_build_join(),
                $this->_build_where(),
                $this->_build_group_by(),
                $this->_build_having(),
                $this->_build_order_by(),
                $this->_build_limit(),
                $this->_build_offset(),

                */

                        $user = DATA::get('USER');
                        $orders = $user->customer_data->get('orders');

                        ?>

                        <div class="msg_btn_wrap">
                            <div class="msg_btn_cell">
                            <?php

                            $t = \ORM::for_table('dialogs')->where('id', $fields->dialog_id)->find_many();
                            $user_id_1 = $t[0]->_data["user_id_1"];
                            $user_id_2 = $t[0]->_data["user_id_2"];

                        //print_r($user->id);
                            if($user_id_1 == $user->id){
                                $to_whom_user = $user_id_2;
                                $this_user_id = $user_id_1;
                            } else {
                                $to_whom_user = $user_id_1;
                                $this_user_id = $user_id_2;
                            }



                            $master = 0;
                            if($user->role == "master"){
                                $order_master_select = \ORM::for_table('customer_data')->where('user_id', $to_whom_user)->find_many();
                                $order_master = json_decode($order_master_select[0]->_data["orders"], true);



                                $user_select = \ORM::for_table('users')->where('id', $to_whom_user)->find_many();

                                $key_main = 0;
                                $this_user_id = 0;
                                $to_whom_user_id = 0;
                                $spec_id = 0;
                                if(empty($order_master)){
                                    $order_master = [];
                                }
                                foreach($order_master AS $key => $val){
                                    if($order_master[$key]["this_user_id"] == $to_whom_user && $order_master[$key]["to_whom_user_id"] == $user->id){
                                        $key_main = $key;
                                        $this_user_id = $order_master[$key]["this_user_id"];
                                        $to_whom_user_id = $order_master[$key]["to_whom_user_id"];
                                        $spec_id = $order_master[$key]["spec_id"];
                                        $id = $order_master[$key]["id"];
                                    }
                                }
                                if(isset($order_master[$key_main]["this_user_id"])){
                                    if($order_master[$key_main]["this_user_id"] == $to_whom_user && $order_master[$key_main]["to_whom_user_id"] == $user->id){

                                    if($order_master[$key_main]["status"] == "2"){
                                        $master = 1;
                                        ?>


                                        <div class="work_with_master button btn_desc" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="3" role="master" spec_id="<?= $spec_id; ?>"><span>Принять</span></div>

                                        <div class="work_with_master button" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="1" role="master" spec_id="<?= $spec_id; ?>"><span>Отклонить</span></div>

                                        <div class="button" onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'">Позвонить</div>

                                        <?php
                                    } else if($order_master[$key_main]["status"] == "3"){

                                            ?>
                                            <div class="work_with_master button" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="4" role="master" spec_id="<?= $spec_id; ?>"><span>Завершить работу</span></div>
                                            <?php
                                    }
                                    }
                                }

                            }








                            $cu = 0;





                            if($user->role == "customer"){
                                $order_master_select = \ORM::for_table('customer_data')->where('user_id', $user->id)->find_many();
                                $order_master = json_decode($order_master_select[0]->_data["orders"], true);


                                $user_select = \ORM::for_table('users')->where('id', $to_whom_user)->find_many();
                                $key_main = 0;
                                $this_user_id = 0;
                                $to_whom_user_id = 0;
                                $spec_id = 0;

                                if(empty($order_master)){
                                    $order_master = [];
                                }
                                foreach($order_master AS $key => $val){
                                    //print_r($order_master[$key]["this_user_id"]."----".$user->id."----".$order_master[$key]["to_whom_user_id"]."----".$to_whom_user."<br><br>");
                                    if($order_master[$key]["this_user_id"] == $user->id && $order_master[$key]["to_whom_user_id"] == $to_whom_user){
                                        $key_main = $key;
                                        $this_user_id = $order_master[$key]["this_user_id"];
                                        $to_whom_user_id = $order_master[$key]["to_whom_user_id"];
                                        $spec_id = $order_master[$key]["spec_id"];
                                        $id = $order_master[$key]["id"];
                                    }
                                }
                                //print_r($order_master[$key_main]["this_user_id"]."----".$to_whom_user."----".$order_master[$key_main]["to_whom_user_id"] == $user->id);
                                //print_r($key_main);



                                if(isset($order_master[$key_main]["this_user_id"])){

                                    if($order_master[$key_main]["this_user_id"] == $user->id && $order_master[$key_main]["to_whom_user_id"] == $to_whom_user){

                                    if($order_master[$key_main]["status"] == "2"){
                                        ?>

                                        <div class="work_with_master button" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="1" spec_id="<?= $spec_id; ?>" role="customer"><span>Отменить</span></div>
                                        <div class="button" onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'">Позвонить</div>
                                        <?php
                                    } else if($order_master[$key_main]["status"] == "3"){
                                            ?>
                                            <div class="work_with_master button" onClick="self.location.href='https://samprorab.com/history'"><span>В заказы</span></div>



                                            <?php
                                            //print_r($order_master[0]["date"]);
                                            $ex = explode(".", $order_master[0]["date"]);
                                            $mk = mktime(0, 0, 0, $ex[1], $ex[0], $ex[2]);


                                            //if(time() < $mk+(60*60*24)){

                                            $time = (new \DateTime($order_master[0]["date"]))->add(new \DateInterval('P1D'));
                                            if($time <= new \DateTime()){

                                            $cu = 1;

                                            ?>
                                                <div class=" button" onClick="add_review_get_modal(<?= $this_user_id; ?>, <?= $to_whom_user_id; ?>, <?= $spec_id; ?>, <?= $fields->dialog_id; ?>)" user_id="<?= $to_whom_user_id; ?>" spec_id="<?= $spec_id; ?>" service_id="<?= $id; ?>">Оставить отзыв</div>

                                                <div class="btn_desc">
                                                <div class="work_with_master button" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="1" spec_id="<?= $spec_id; ?>" role="customer"><span>Отменить</span></div>
                                                <div class="button btn_mob" onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'">Позвонить</div>
                                                </div>



                                            <?php
                                            } else {

                                            $cu = 2;

                                            ?>
                                                <div class="work_with_master button" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="1" spec_id="<?= $spec_id; ?>" role="customer"><span>Отменить</span></div>
                                                <div class="button btn_desc" onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'"><span>Позвонить</span></div>

                                            <?php


                                            }
                                            ?>


                                            <?php
                                    } else if($order_master[$key_main]["status"] == "4"){
                                        ?>
                                        <div class="button" onClick="self.location.href='https://samprorab.com/history'">В заказы</div>

                                        <?php
                                        //print_r($order_master[0]["date"]);
                                        $ex = explode(".", $order_master[0]["date"]);
                                        $mk = mktime(0, 0, 0, $ex[1], $ex[0], $ex[2]);


                                        //if(time() < $mk+(60*60*24)){

                                        $time = (new \DateTime($order_master[0]["date"]))->add(new \DateInterval('P1D'));


                                        if($time <= new \DateTime()){


                                            $cu = 3;

                                            ?>


                                            <div class=" button btn_desc" onClick="add_review_get_modal(<?= $this_user_id; ?>, <?= $to_whom_user_id; ?>, <?= $spec_id; ?>, <?= $fields->dialog_id; ?>)" user_id="<?= $to_whom_user_id; ?>" spec_id="<?= $spec_id; ?>" service_id="<?= $id; ?>">Оставить отзыв</div>

                                        <?php
                                        }
                                        ?>
                                        <div class="button" onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'">Позвонить</div>
                                        <?php
                                }
                                    }
                                }

                            }

                            /*if(isset($orders[$fields->dialog_id])){
                                if(!isset($orders[$fields->dialog_id]["status"])){

                                    $orders[$fields->dialog_id]["status"] = 0;
                                    $orders[$fields->dialog_id]["this_user_id"] = 0;
                                }
                                if($orders[$fields->dialog_id]["status"] == "1"){
                                    ?>

                                        <?php
                                            if($user->role == "customer" && $orders[$fields->dialog_id]["this_user_id"] == $user->id){

                                                $this_user_id = $orders[$fields->dialog_id]["this_user_id"];
                                                $to_whom_user_id  = $orders[$fields->dialog_id]["to_whom_user_id"];
                                                ?>


                                            <div class="work_with_master button" service_id="<?= $orders[$fields->dialog_id]["id"]; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>">Отменить</div>
                                            <div class="work_with_master button" service_id="" this_user_id="" to_whom_user_id="">Позвонить</div>
                                            <?php
                                            }
                                        ?>
                                    <?php
                                } else if($orders[$fields->dialog_id]["status"] == "2"){
                                    if($user->role == "customer" && $orders[$fields->dialog_id]["this_user_id"] == $user->id){
                                        ?>
                                        <div class="work_with_master button" service_id="" this_user_id="" to_whom_user_id="">Перейти в заказы</div>
                                        <div class="work_with_master button" service_id="" this_user_id="" to_whom_user_id="">Оставить отзыв</div>
                                        <div class="work_with_master button" service_id="" this_user_id="" to_whom_user_id="">Позвонить</div>
                                        <?php
                                    }
                                }
                            }*/

                            ?>

                            </div>


                            <?php
                            if($master == 1){
                                ?>
                                        <div class="work_with_master button btn_mob" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="3" role="master" spec_id="<?= $spec_id; ?>"><div style="margin: auto;"><span>Принять</span></div></div>

                            <?php
                            }

                            if($cu ==  1){


                                ?>

                            <div class="msg_btn_cell btn_mob">
                                <div class="work_with_master button" service_id="<?= $id; ?>" this_user_id="<?= $this_user_id; ?>"  to_whom_user_id="<?= $to_whom_user_id; ?>" status="1" spec_id="<?= $spec_id; ?>" role="customer"><span>Отменить</span></div>
                                <div class="button" onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'">Позвонить</div>
                            </div>
                            <div class="msg_btn_cell btn_desc">
                            <div class="button " onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'" style="margin-top:20px;">Позвонить</div>
                            </div>
                          <?php
                            }

                            if($cu == 2){

                                ?>

                                                <div class="button btn_mob" onClick="self.location.href='tel:<?= $user_select[0]->_data["phone"]; ?>'"><div style="margin:auto;">Позвонить</div></div>

<?php
                            }
                          ?>


                            <?php
                            if($cu == 3){
                            //print_r($order_master[0]["date"]);
                            $ex = explode(".", $order_master[0]["date"]);
                            $mk = mktime(0, 0, 0, $ex[1], $ex[0], $ex[2]);


                            //if(time() < $mk+(60*60*24)){

                            $time = (new \DateTime($order_master[0]["date"]))->add(new \DateInterval('P1D'));


                            if($time <= new \DateTime()){
                                ?>
                                <div class=" button btn_mob" onClick="add_review_get_modal(<?= $this_user_id; ?>, <?= $to_whom_user_id; ?>, <?= $spec_id; ?>, <?= $fields->dialog_id; ?>)" user_id="<?= $to_whom_user_id; ?>" spec_id="<?= $spec_id; ?>" service_id="<?= $id; ?>"><div style="margin:auto;">Оставить отзыв</div></div>

                            <?php
                            }
                        }
                            ?>

                        </div>








                        <style>
                            .messanger .dialog_wrapper .chat_wrap .chat_box .messages_wrapper{
                                padding-top: 85px;
                            }
                        </style>
                        <?php


                    }

                    $messages_html.= ob_get_clean();

                    API::response(['messages_htmlcc' => $messages_html, 'dialog_id' => $fields->dialog_id]);
                });
            }

            if($_POST['action'] == 'get_chat_list'){

                $dialogs_list = (new dialogs_list())->get_all($this->user->id);

                $all_unread_count = 0;
                $chat_list_html = '';

                ob_start();
                /* @var $dialog \app\std_models\dialog */
                foreach ($dialogs_list as $dialog) {
                    $all_unread_count+= $dialog->unread_count;

                    ?>
                    <div class="dialog_item" dialog_id="<?= $dialog->id?>">
                        <? if(is_online_by_date($dialog->companion->last_visit, 600)){?>
                            <span class="online"></span>
                        <?}?>
                        <img class="avatar" src="<?= get_avatar($dialog->companion->id)?>">
                        <div class="name_box">
                            <span class="name" uid="<?= $dialog->companion->id ?>"><?= $dialog->companion->name ?></span>
                            <span class="profession">Профессия</span>
                        </div>
                        <span class="counter <?= ($dialog->unread_count < 1)? 'hide': '';?>"><?= $dialog->unread_count ?></span>
                    </div>
                <?}

                $chat_list_html.= ob_get_clean();

                $return_data = [
                    'chat_list_html' => $chat_list_html,
                    'all_unread_count' => $all_unread_count,
                ];

                API::response($return_data);


            }

        }

        $dialogs_list = (new dialogs_list())->get_all($this->user->id);
        DATA::set('dialogs_list', $dialogs_list);
        set_meta_page('/messages');

    }

    public function dialog_init(){

    }


    //Отзывы
    public function reviews(){
        set_meta_page('/reviews');
    }

    public function api_get_single_review($data){

        $this->is_api = true;

        $id = $data['id'];

        $review = new review($id);

        API::response($review);

    }


    public function review_add_answer(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'answer' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $review = new review($fields->id);
            if($review){
                $review->update($fields);
                API::response($review);
            }

            API::error(165, 'review id='.$fields->id.' not exist');

        });

    }

    public function review_add_complaint(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'complaint' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $review = new review($fields->id);
            if($review){
                $review->update($fields);
                API::response($review);
            }

            API::error(165, 'review id='.$fields->id.' not exist');

        });


    }

    public function add_review(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'spec_id' => ['req', 'int'],
            'content' => ['req', 'str'],
            'to_whom_user' => ['req', 'int'],
            'this_user_id' => ['req', 'int'],
            'star' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $review = new review();

            $review->spec_id = $fields->spec_id;
            $review->user_id = $this->auth->user->id;
            $review->master_id = $fields->id;
            $review->reviewer_name = $this->auth->user->name;
            $review->time = (new \DateTime())->format('Y-m-d H:i:s');
            $review->score = $fields->star;
            $review->content = $fields->content;
            $review->create();

            $master = new user($fields->id);

            $finished = $master->master_data->get('finished');
            $finished++;
            $master->master_data->set('finished', $finished);
            $master->master_data->save_data();

            (new rebuild_rating())->rebuild_user($fields->id);




            $order_master_select = \ORM::for_table('customer_data')->where('user_id', $fields->this_user_id)->find_many();
            $orders = json_decode($order_master_select[0]->_data["orders"], true);

            if(empty($orders)){
                $orders = [];
            }



            foreach($orders AS $key => $val){
                if($orders[$key]["to_whom_user_id"] == $fields->to_whom_user){
                    unset($orders[$key]);
                }
            }
            $orders = array_values($orders);



            $sql = "UPDATE customer_data SET orders='".json_encode($orders)."' WHERE user_id = '".$fields->this_user_id."'";
                \ORM::raw_execute($sql);





            API::response();




        });


    }

    public function favorites()
    {
        set_meta_page('/favorites');

        $this->layout = 'dashboard_transperent';

        $catalog_status = (new catalog_model())->get_favorites($this->user->customer_data->get('favorites'));

    }

    //История заказов для заказчика
    public function history()
    {
        set_meta_page('/history');

        $catalog_status = (new catalog_model())->get_orders($this->user->customer_data->get('orders'));
    }

    //Настройки для мастера
    public function settings(){


        if(is_ajax()){

            $rules = [
                'week_1' => ['bool'],
                'week_2' => ['bool'],
                'week_3' => ['bool'],
                'week_4' => ['bool'],
                'week_5' => ['bool'],
                'week_6' => ['bool'],
                'week_7' => ['bool'],
                'open_time' => ['time'],
                'close_time' => ['time'],
                'contract' => ['bool'],
                'guarantee' => ['bool'],
            ];

            $fields = \validator::ALL_POST($rules);

            //API::response($fields);


            API::auto_validate($fields, function ($fields){

                $this->user->master_data->update($fields);
                (new rebuild_rating())->rebuild_user($this->user->id);

                API::response();
            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }

        set_meta_page('/settings');
    }

    public function delete_account()
    {
        $auth = \core\engine\AUTH::init();
        $auth->destroy_all_user_session($this->user->id);
        $auth->destroy_session();

        $this->user->delete();

        $this->redirect();
    }

    public function contacts(){

        if(is_ajax()){

            $rules = [
                'phone'     => ['req', 'phone'],
                'telegram'  => ['str'],
                'whatsapp'  => ['str', 'phone'],
                'viber'     => ['str', 'phone'],
                'vk'        => ['str'],
                'fb'        => ['str'],
                'inst'      => ['str'],
            ];

            $fields = \validator::ALL_POST($rules);

            if(strlen($fields->telegram) > 1){
                $fields->telegram = trim($fields->telegram, '@');
            }else{
                $fields->telegram = '';
            }



            API::auto_validate($fields, function ($fields){

                $this->user->master_data->update($fields);
                (new rebuild_rating())->rebuild_user($this->user->id);
                API::response();
            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }

        set_meta_page('/contacts');
    }

    public function prices()
    {
        if(is_ajax()){

            $rules = [
                'spec_id'  => ['req', 'int'],
                'price'     => ['req', 'int'],
            ];

            $fields = \validator::ALL_POST($rules);

            API::auto_validate($fields, function (\validator $fields){

                $fields->add_field('user_id', $this->user->id);

                $updated = (new sort_rating_prices())->update_price($fields);

                if($updated){
                    API::response(['price' => $fields->price]);
                }else{
                    API::error('446','spec_id and user_id are not related');
                }

            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }

        set_meta_page('/prices');
    }

    public function finished(){

        if(is_ajax()){

            $rules = [
                'spec_id'  => ['req', 'int'],
                'name'     => ['req', 'str'],
                'content'  => ['req', 'str'],
                'price'     => ['req', 'int'],
                'medias' => ['arr'],
            ];

            $fields = \validator::ALL_POST($rules);

            API::auto_validate($fields, function (\validator $fields){

                $fields->add_field('user_id', $this->user->id);

                $work = (new work())->create($fields);
                (new rebuild_rating())->rebuild_user($this->user->id);

                API::response($work);

            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }

        set_meta_page('/finished');
    }

    public function delete_finished()
    {
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields) {
            if ($work = new work($fields->id)) {
                $work->delete();

                API::response();
            }

            API::error('179', 'there is no work with id = '.$fields->id);
        });
    }





}
