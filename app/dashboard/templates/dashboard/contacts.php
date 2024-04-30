
<?php

use core\engine\DATA;
use app\std_models\user;


/**
 * @var user $user
 */
$user = DATA::get('USER');

?>


<div class="std_lk_wrapper contacts_wrapper">

    <form class="update_custom_data" method="post">

        <h2>Выберите способы связи
                    <span class="info_tooltip">
                        <p class="tooltip">
                            <span>Укажите как можно больше способов связи что бы с вами было удобнее связаться</span>
                        </p>
                    </span>
        </h2>
        <p class="description">Укажите способы связи с вами для заказчиков. В соответствуюшие поля вставьте данные</p>


        <div class="row">
            <div class="c_6 xs_c_12">
                <div class="field_wrap">
                    <label for="phone">Телефон</label>
                    <input id="phone" name="phone" class="field field_full phonemask" type="text" value="<?= formatphone($user->master_data->get('phone'));?>">
                </div>
            </div>
        </div>


        <h4>Мессенжеры</h4>

        <div class="row socail_flx">
            <div class="c_4 xs_c_12">
                <div class="field_wrap">
                    <label for="telegram"><i class="icon icon-tg"></i>Telegram</label>
                    <input id="telegram" name="telegram" class="field field_full" type="text" value="@<?= $user->master_data->get('telegram');?>">
                </div>
            </div>
            <div class="c_4 xs_c_12">
                <div class="field_wrap">
                    <label for="whatsapp"><i class="icon icon-whatsapp"></i>WhatsApp</label>
                    <input id="whatsapp" name="whatsapp" class="field field_full phonemask" type="text" value="<?= formatphone($user->master_data->get('whatsapp'));?>">
                </div>
            </div>
            <div class="c_4 xs_c_12">
                <div class="field_wrap">
                    <label for="viber"><i class="icon icon-viber"></i>Viber</label>
                    <input id="viber" name="viber" class="field field_full phonemask" type="text" value="<?= formatphone($user->master_data->get('viber'));?>">
                </div>
            </div>
        </div>

        <h4>Социальные сети</h4>

        <div class="row socail_flx">
            <div class="c_4 xs_c_12">
                <div class="field_wrap">
                    <label for="vk"><i class="icon icon-vk"></i>Vkontakte</label>
                    <input id="vk" name="vk" class="field field_full" type="text" value="<?= $user->master_data->get('vk');?>">
                </div>
            </div>
            <div class="c_4 xs_c_12">
                <div class="field_wrap">
                    <label for="fb"><i class="icon icon-facebook_1"></i>Facebook</label>
                    <input id="fb" name="fb" class="field field_full" type="text" value="<?= $user->master_data->get('fb');?>">
                </div>
            </div>
            <div class="c_4 xs_c_12">
                <div class="field_wrap">
                    <label for="inst"><i class="icon icon-insta_1"></i>Instagram</label>
                    <input id="inst" name="inst" class="field field_full" type="text" value="<?= $user->master_data->get('inst');?>">
                </div>
            </div>
        </div>

        <input class="button save_profile" type="submit" value="Сохранить изменения">

    </form>

</div>



