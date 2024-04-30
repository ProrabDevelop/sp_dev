<?php

use core\engine\DATA;
$AUTH = \core\engine\AUTH::init();
$header_step = DATA::get('header_step');
?>

<section class="header_nav_steps_wrapper">
    <div class="wrap">
        <div class="header_nav_steps xs_hide">

            <div>
                <div class="steps_wrapper">
                    <div class="steps_view">
                        <? for($i = 1; $i <= 3; $i++){
                            $selected = '';
                            if($header_step >= $i){
                                $selected = 'selected';
                            }
                            echo '<div class="step '.$selected.'"></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <p class="step_text">Шаг <?= $header_step; ?> из 3</p>

        </div>


        <div class="header_nav_steps_xs xs_only">

            <a href="javascript:history.go(-1)" class="step-back-link"></a>

            <div class="sigment">

               <span class="step_text">Шаг <?= $header_step; ?> из 3</span>

            </div>


            <div class="sigment">

                <div class="steps_wrapper">
                    <div class="steps_view">
                        <? for($i = 1; $i <= 3; $i++){
                            $selected = '';
                            if($header_step >= $i){
                                $selected = 'selected';
                            }
                            echo '<div class="step '.$selected.'"></div>';
                        }
                        ?>
                    </div>
                </div>

                <? /*if($AUTH->is_auth()){?>
                    <div class="profile">
                        <div class="lk_header_dropdown_menu">
                            <div class="dropdown_title">
                                <button class="dropbtn">
                                    <i class="icon-lk"></i>
                                </button>
                            </div>
                            <div class="dropdown_content">
                                <?
                                $switch_text = 'Мастер';
                                if($AUTH->user->is_master()){
                                    $switch_text = 'Заказчик';
                                }
                                ?>
                                <a href="<?= URL?>dashboard">Личный кабинет</a>
                                <a href="<?= URL?>auth/switch">Войти как <?= $switch_text?></a>
                                <a href="<?= URL?>auth/logoutall">Выйти везде</a>
                                <a href="<?= URL?>auth/logout">Выход</a>
                            </div>

                        </div>


                    </div>
                <?}else{?>
                <a class="login get_modal" modal="modal_login">
                    <i class="icon-auth"></i>
                </a>
                <?}*/?>

            </div>


        </div>


    </div>

</section>
