<?php get_block('modals/auth/register-confirm-phone') ?>
<?php get_block('modals/auth/register-finishing') ?>
<?php get_block('modals/auth/register-success') ?>

<div class="modal modal_reg_two modal_auth modal_v2" data-role-as="customer">

    <div class="modal_header">
        <div class="modal_close_wrapper">
            <span class="modal_close">
                <img src="<?= URL.'assets/img/icons/close-gray.svg' ?>" alt="" width="24" height="24" />
            </span>
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
            <span>Регистрация</span>
            <span data-role-as="customer">заказчика</span>
            <span data-role-as="master">мастера</span>
        </div>
    </div>

    <form class="reg_form ajax_sender" action_fn="reg_form">
        <input type="hidden" name="remember_me" value="on">
        <input type="hidden" name="role" value="customer">

        <div class="field_wrap">
            <label>Регистрируюсь как</label>
            <?php get_block('components/role-toggler') ?>
        </div>

        <div class="field_wrap">
            <label for="reg_form_phone">Телефон</label>
            <input type="text"
                   name="phone"
                   id="reg_form_phone"
                   placeholder="+7(___)___-__-__"
                   class="field phonemask"
            />
        </div>

        <div class="buttons-row">
            <button type="submit" class="button button-login">Получить код</button>
            <div class="register-policy">
                Нажимая кнопку «Получить код», вы подтверждаете своё согласие на
                <a href="#">обработку персональных данных</a>
            </div>
            <div class="separator" data-role-as="customer">Есть аккаунт заказчика?</div>
            <div class="separator" data-role-as="master">Есть аккаунт мастера?</div>
            <button type="button" class="button button-register get_modal" modal="modal_login" data-role-as="customer">Войти</button>
        </div>

    </form>
</div>
