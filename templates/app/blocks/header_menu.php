<?php

use core\engine\AUTH;
use core\engine\DATA;

$AUTH = \core\engine\AUTH::init();

$user_name = '';
$user_avatar = '';
$role_name = '';

if ($AUTH->is_auth()) {
    $user_name = $AUTH->user->name;

    $user_avatar = file_exists(WEB.'uploads/avatars/'.$AUTH->user->id.'.png')
        ? URL.'uploads/avatars/'.$AUTH->user->id.'.png'
        : URL.'assets/img/icons/header-user.svg';

    $role_name = $AUTH->user->is_master() ? 'Мастер' : 'Заказчик';
}

?>

<header class="main_header_nav">
    <div class="wrap">
        <div class="header_nav-mobile">
            <input id="header-menu__toggle" type="radio" />
            <a href="<?= URL?>" class="header_nav-mobile-logo">
                <img src="<?= URL?>assets/img/logo-footer.svg"/>
            </a>
            <label class="header-menu__btn" for="header-menu__toggle">
                <div>
                    <span></span>
                </div>
            </label>
            <div class="header_nav-mobile-menu">
                <div class="header_nav-mobile-menu-head">
                    <a href="<?= URL?>" class="header_nav-mobile-menu-logo">
                        <img src="<?= URL?>assets/img/logo-footer.svg"/>
                    </a>
                    <label class="header-menu__btn" for="header-menu__toggle">
                        <div>
                            <span></span>
                        </div>
                    </label>
                </div>
                <a class="button" href="<?= URL?>">Поиск мастера</a>
                <div class="mobile-menu-droplist">
                   <h3>О сервисе</h3>
                   <ul>
                    <li><a href="#">Вход</a></li>
                    <li><a href="#">Регистрация специалиста</a></li>
                    <li><a href="#">Все специальности</a></li>
                </ul>
                </div>
                <div class="mobile-menu-droplist">
                    <h3>Связь с нами</h3>
                <ul>
                    <li class="menu__mail"><a href="#">prorabmasters@gmail.com</a></li>
                </ul>
                </div>
                <div class="mobile-menu-droplist">
                     <h3>Поможем быстро</h3>
                <ul>
                    <li><a href="#">Позвонить</a></li>
                    <li class="menu__whatsapp"><a href="#">Написать WhatsApp</a></li>
                </ul>
                </div>
            </div>

            <?php if ($AUTH->is_auth()): ?>
                <div class="profile">
                    <div class="lk_header_dropdown_menu">
                        <div class="dropdown_title">
                            <button class="dropbtn">
                                <span class="avatar" style="background-image: url('<?= $user_avatar ?>')"></span>
                                <span class="info">
                                        <span class="name"><?= $user_name ?></span>
                                        <span class="role"><?= $role_name ?></span>
                                    </span>
                            </button>
                        </div>
                        <div class="dropdown_content">
                            <a href="<?= URL?>dashboard">Личный кабинет</a>
                            <a href="<?= URL?>auth/switch">Войти как <?= $AUTH->user->is_master() ? 'Заказчик' : 'Мастер' ?></a>
                            <a href="<?= URL?>auth/logoutall">Выйти везде</a>
                            <a href="<?= URL?>auth/logout">Выход</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a class="login get_modal" modal="modal_login" data-role-as="customer">
                    Войти
                </a>
                <div class="opacity-mobile"></div>
            <?php endif; ?>
        </div>
        <div class="header_nav">
             <a href="<?= URL?>">
                <img src="<?= URL?>assets/img/logo-footer.svg"/>
            </a>
            <div class="main_panel">
                <?php if ($AUTH->is_auth()): ?>
                    <div class="profile">
                        <div class="lk_header_dropdown_menu">
                            <div class="dropdown_title">
                                <button class="dropbtn">
                                    <span class="avatar" style="background-image: url('<?= $user_avatar ?>')"></span>
                                    <span class="info">
                                        <span class="name"><?= $user_name ?></span>
                                        <span class="role"><?= $role_name ?></span>
                                    </span>
                                </button>
                            </div>
                            <div class="dropdown_content">
                                <a href="<?= URL?>dashboard">Личный кабинет</a>
                                <a href="<?= URL?>auth/switch">Войти как <?= $AUTH->user->is_master() ? 'Заказчик' : 'Мастер' ?></a>
                                <a href="<?= URL?>auth/logoutall">Выйти везде</a>
                                <a href="<?= URL?>auth/logout">Выход</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="registration_form registration_form_btns">
                        <a href="#" class="registration-master get_modal" modal="modal_reg_two" data-role-as="master">
                            Регистрация специалиста
                        </a>
                        <a class="login get_modal" modal="modal_login" data-role-as="customer">
                            <span>Войти</span>
                        </a>
                        <a class="registration button get_modal" modal="modal_reg_two" data-role-as="customer">Зарегистрироваться</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
