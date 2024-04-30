<?php

use core\engine\DATA;
use app\std_models\user;
use core\engine\media;

/**
 * @var user $user
 */
$user = DATA::get('USER');





?>


<div class="lk_content">

    <div class="avatar_wrapper">
        <div class="avatar my_avatar">

            <?
            if(file_exists(WEB.'uploads/avatars/'.$user->id.'.png')){?>
                <img src="<?= URL?>uploads/avatars/<?= $user->id; ?>.png<?= '?v='.rand(100000,999999);?>">
            <?}else{?>
                <img src="<?= URL?>assets/img/no-photo.jpg">
            <?}?>

        </div>
        <div class="edit_avatar"><i class="icon icon-edit"></i></div>
    </div>

    <div class="lk_data">
        <p class="name"><?= $user->name ?></p>

        <?php if ($user->is_master() && ($profession = $user->master_data->get('profession'))): ?>
            <p class="profession"><?= $profession ?></p>
        <?php endif; ?>


        <h2>Личная информация</h2>

        <form class="update_profile" method="post">

            <div class="row">
                <div class="c_6 xs_c_12">
                    <div class="field_wrap">
                        <label for="name">Имя Фамилия</label>
                        <input type="text" id="name" name="name" class="field field_full" placeholder="Имя" value="<?= $user->name; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="c_6 xs_c_12">
                    <div class="field_wrap">
                        <label for="mail">Email</label>
                        <input type="text" id="mail" name="mail" class="field field_full" placeholder="Ваш E-mail" value="<?= $user->mail; ?>" autocomplete="off">
                    </div>
                </div>
                <div class="c_6 xs_c_12">
                    <div class="field_wrap">
                        <label for="city">Город</label>
                        <select id="city" name="city" class="select_full select_city">
                            <?
                            if(!empty( $user->city )){
                                echo '<option value="'.$user->city.'">'.get_city_name($user->city).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="c_6 xs_c_12">
                    <div class="field_wrap">
                        <label for="edit_pass">Новый пароль</label>
                        <div class="input-show-pwd">
                            <input type="password" type="text" id="edit_pass" name="pass" class="field field_full" placeholder="Пароль" autocomplete="off">
                            <span class="show-ele show-edit-pass"></span>
                        </div>
                    </div>
                </div>
                <div class="c_6 xs_c_12">
                    <div class="field_wrap">
                        <label for="edit_pass_confirm">Повторите пароль</label>
                        <div class="input-show-pwd">
                            <input type="password" id="edit_pass_confirm" name="pass_confirm" class="field field_full" placeholder="Пароль" autocomplete="off">
                            <span class="show-ele show-edit-pass"></span>
                        </div>
                    </div>
                </div>
            </div>

            <? if($user->is_master()){?>
                <div class="row xs_plus_20 birthday_flex">
                    <div class="c_6 xs_c_12">
                        <div class="field_wrap">
                            <label for="birthday">Дата рождения</label>
                            <input type="date" id="birthday" name="birthday" class="field field_full" value="<?= $user->birthday?>">
                        </div>
                    </div>
                    <div class="c_6 xs_c_12">
                        <div class="field_wrap">
                            <label for="experience">Опыт работы</label>

                            <? $experience = $user->master_data->get('experience');?>

                            <select id="experience" class="select_full select_experience" name="experience">
                                <option value="">Выберите</option>
                                <? foreach (EXP_TEXTS as $key => $text) {
                                    if($key == $experience){
                                        echo '<option value="'.$key.'" selected>'.$text.'</option>';
                                        continue;
                                    }
                                    echo '<option value="'.$key.'">'.$text.'</option>';
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
            <?}?>


            <div class="field_wrap">
                <div class="row">
                    <div class="checkbox_input">
                        <input type="checkbox" id="email_subscribe" name="email_subscribe" <?= ($user->email_subscribe == 1) ? 'checked' : '';?>>
                        <label for="email_subscribe">Получать рассылку новостей о сервисе на почту</label>
                    </div>
                </div>
            </div>

            <? if($user->is_master()){
                $specs = $user->get_specs();


                $json_specs = [];
                /* @var $spec \app\std_models\speciality */
                foreach ($specs as $spec){
                    $json_specs[$spec->id] = $spec->name;
                }


                ?>

                <script>window.all_my_spec = <? echo (!empty($json_specs) ? json_encode($json_specs, JSON_UNESCAPED_UNICODE) : '{}')?>;</script>

                <h2>Мои специализации
                    <span class="info_tooltip">
                        <p class="tooltip">
                            <span>Здесь вы можете указать специальности в которых вы мастер</span>
                        </p>
                    </span>
                </h2>

                <div class="specialization">

                    <div class="spec_selector_form">



                        <div class="tags tags_float tags_spec">
                            <div class="add_spec_but get_modal button dark small" modal="modal_add_spec">
                                <span>Добавить специализацию</span>
                            </div>
                            <?


                            foreach ($specs as $spec){?>
                                <div class="tag_item" spec_id="<?= $spec->id?>">
                                    <span><?= $spec->name?></span>
                                    <i class="icon icon-cancel delete_spec_item"></i>
                                </div>
                            <?}?>

                        </div>

                    </div>

                </div>
            <?}?>

            <div class="row">
                <div class="c_12 xs_center">
                    <input class="button button_save save_profile" type="submit" value="Сохранить изменения">
                    <span type="button" modal="modal_delete_profile" class="get_modal button_delete_profile delprofile">
                        <i class="icon icon-delete"></i>
                        Удалить профиль
                    </span>
                </div>
            </div>

        </form>

    </div>


</div>



