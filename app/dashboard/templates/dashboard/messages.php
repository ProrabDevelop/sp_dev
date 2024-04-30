<?

/* @var $user \app\std_models\user */
/* @var $dialog \app\std_models\dialog*/

use core\engine\DATA;

$user = \core\engine\DATA::get('USER');
$dialogs_list = DATA::get('dialogs_list');

$selected_dialog = DATA::get('selected_dialog');

if($selected_dialog){?>
    <script>
        window.selected_chat = <?= $selected_dialog?>;
    </script>
<?}?>




<div class="messanger">
    <div class="dialog_wrapper">

        <div class="chat_list_wrapper ">

            <div class="chat_list_title">
                <p>Чаты</p>
            </div>

            <div class="chat_list">

                <? /* @var $dialog \app\std_models\dialog */?>
                <? foreach ($dialogs_list as $dialog) {?>

                    <?php if (!$dialog->companion || $dialog->companion->deleted_at) continue; ?>

                    <div class="dialog_item" dialog_id="<?= $dialog->id?>">
                        <?if(is_online_by_date($dialog->companion->last_visit, 600)){?>
                            <span class="online"></span>
                        <?}?>
                        <img class="avatar" src="<?= get_avatar($dialog->companion->id)?>">
                        <div class="name_box">
                            <span class="name" uid="<?= $dialog->companion->id ?>"><?= $dialog->companion->name ?></span>
                            <!--<span class="profession">Профессия</span>-->
                        </div>
                        <span class="counter <?= ($dialog->unread_count < 1)? 'hide': '';?>"><?= $dialog->unread_count ?></span>
                    </div>
                <?}?>
            </div>

        </div>

        <div class="chat_wrap">

            <div class="selected_chat_info">
                <p class="messages_back"><i class="icon icon-left-open"></i>Назад</p>
                <p class="name">Выбирете диалог</p>
                <p class="time"></p>
            </div>

            <div class="chat_box disabled"></div>

            <div class="chat_input_wrap disabled">
                <input class="my_message" type="text" placeholder="Сообщение...">
                <button class="send_message"><i class="icon icon-tg"></i></button>
            </div>

        </div>

    </div>
</div>


<script src="<?=URL?>assets/js/massanger.js"></script>
