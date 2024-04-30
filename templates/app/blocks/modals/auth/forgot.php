<?php get_block('modals/auth/forgot-confirm-phone'); ?>
<?php get_block('modals/auth/forgot-change-password'); ?>
<?php get_block('modals/auth/forgot-success'); ?>

<div class="modal modal_forgot modal_auth modal_v2" data-role-as="customer">

    <div class="modal_header">
        <div class="modal-controls">
            <a href="#" class="get_modal back-link" modal="modal_login" data-role-as="customer">
                <img src="<?= URL.'assets/img/icons/modal-back-arrow.svg' ?>" alt="" width="24" height="24">
                <span>Назад к авторизации</span>
            </a>
            <div class="modal_close_wrapper">
                <span class="modal_close">
                    <img src="<?= URL.'assets/img/icons/close-gray.svg' ?>" alt="" width="24" height="24" />
                </span>
            </div>
        </div>
        <div class="step-row" data-step="1" data-step-max="3">
            <div class="progress-bar">
                <svg class="progress" x="0px" y="0px" viewBox="0 0 80 80">
                    <path class="track" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" />
                    <path class="fill" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" />
                    <text class="value" x="50%" y="58%">1/3</text>
                </svg>
            </div>
        </div>
        <div class="title">
            <span>Восстановить пароль</span>
        </div>
        <div class="subtitle">
            Введите телефон на который зарегистрирован профиль
        </div>
    </div>

    <form class="login_form ajax_sender" action_fn="forgot">
        <div class="field_wrap">
            <label for="forgot_form_phone">Телефон</label>
            <input type="text"
                   name="phone"
                   id="forgot_form_phone"
                   placeholder="+7(___)___-__-__"
                   class="field phonemask"
            />
        </div>

        <div class="buttons-row">
            <button type="submit" class="button button-login">Получить код</button>
        </div>
    </form>
</div>
