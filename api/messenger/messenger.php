<?php
namespace api\messenger;
use app\std_models\dialog;
use app\std_models\dialogs_list;
use app\std_models\message;
use core\engine\API;
use core\engine\std_module;

class messenger extends std_module {

    public $active = true;
    //public $forauth = true;

    public $forauth = false;

    protected $routes = [
        '/messenger' => [
            '/' => 'main',
            '/send/' => 'send',
            '/read/' => 'read_messages',
            '/get_chat_list/' => 'get_chat_list',
        ],
    ];

    public function main(){
        echo 'main messenger api';
    }
    public function send(){
        $fields = \validator::ALL_POST([
            'dialog_id' => ['req', 'int'],
            'sender' => ['req', 'int'],
            'type' => ['req', ['enum', ['text', 'media']]],
            'body' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){
            $time = (new \DateTime())->format('Y-m-d H:i:s');

            $fields->add_field('time', $time);
            $fields->add_field('readed', 0);
            $sender = $fields->sender;
            $dialog = (new dialog($fields->dialog_id));

            $dialog->last_update = $time;
            $dialog->update();



            //$dialog->update();
            if($sender != $dialog->user_id_1){
                $fields->add_field('reader', $dialog->user_id_1);
            } else {
                $fields->add_field('reader', $dialog->user_id_2);
            }
            $message = (new message())->create($fields);
            $this->logger($dialog);
            unset($dialog->unread_count);

            API::response([
                'message_id' => $message->id,
                'dialog' => $dialog,
            ]);

        });
    }

    function logger($message)
    {
        $logPath = __DIR__ . '/test';
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }

        $errorMsg = date('Y/m/d H:i:s') . ":".json_encode($message) . PHP_EOL;
        error_log($errorMsg, 3, $logPath . '/api_logs.log');
    }

    public function read_messages(){

        $fields = \validator::ALL_POST([
            'dialog_id' => ['req', 'int'],
            'reader' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $time = (new \DateTime())->format('Y-m-d H:i:s');

            $is_updated = (new message())->read_messages($fields->dialog_id, $fields->reader);
            if($is_updated){
                $dialog = (new dialog($fields->dialog_id));
                $dialog->last_update = $time;
                $dialog->update();
            }
            API::response();

        });



    }

    //in api!
    public function get_chat_list(){

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
                    <span class="name"><?= $dialog->companion->name ?></span>
                    <!--<span class="profession">Профессия</span>-->
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
