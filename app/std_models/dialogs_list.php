<?php

namespace app\std_models;

class dialogs_list{

    protected $dialogs_list = [];

    public function get_all($user_id){

        $dialogs = \ORM::for_table('dialogs')
            ->where_raw('user_id_1 = ? OR user_id_2 = ?', array($user_id, $user_id))
            ->order_by_desc('last_update')
            ->find_many();

        if($dialogs){

            $user_ids = [$user_id];
            $messages_unread_count = [];

            foreach ($dialogs as $dialog){
                if($dialog->user_id_1 == $user_id){
                    $user_ids[] = $dialog->user_id_2;
                }else{
                    $user_ids[] = $dialog->user_id_1;
                }
            }

            $users = (new slim_user())->get_list_by_ids($user_ids);

            $me = $users[$user_id];

            foreach ($dialogs as $dialog){

                if (empty($users[$dialog->user_id_2]) || empty($users[$dialog->user_id_1])) {
                    continue;
                }

                $dialog->unread_count = (new message())->get_unread_count($dialog->id, $user_id);

                if($dialog->user_id_1 == $user_id){
                    $this->dialogs_list[$dialog->id] = (new dialog())->set_ormdata($dialog)
                        ->set_users($me, $users[$dialog->user_id_2])
                        ->set_companion($users[$dialog->user_id_2]);
                }else{
                    $this->dialogs_list[$dialog->id] = (new dialog())->set_ormdata($dialog)
                        ->set_users($users[$dialog->user_id_1], $me)
                        ->set_companion($users[$dialog->user_id_1]);
                }

            }
        }

        return $this->dialogs_list;
    }

}
