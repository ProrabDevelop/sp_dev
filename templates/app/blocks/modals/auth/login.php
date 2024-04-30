<div class="modal modal_login modal_auth modal_v2" data-role-as="customer">
    <div class="modal_header">
        <div class="modal_close_wrapper">
            <span class="modal_close">
                <img src="<?= URL.'assets/img/icons/close-gray.svg' ?>" alt="" width="24" height="24" />
            </span>
        </div>
        <div class="title">
            <span>Войти</span>
            <span data-role-as="customer">как заказчик</span>
            <span data-role-as="master">как мастер</span>
        </div>
    </div>
    <form class="login_form ajax_sender" action_fn="login_form">
        <input type="hidden" name="remember_me" value="on">
        <input type="hidden" name="role" value="customer">

        <div class="field_wrap">
            <label>Войти как</label>
            <?php get_block('components/role-toggler') ?>
        </div>

        <div class="field_wrap">
            <label for="login_form_phone">Телефон</label>
            <input type="text"
                   name="phone"
                   id="login_form_phone"
                   placeholder="+7(___)___-__-__"
                   class="field phonemask"
            />
        </div>

        <div class="field_wrap">
            <label for="login_form_pass">Пароль</label>
            <div class="input-show-pwd">
                <input type="password" name="pass" id="login_form_pass" placeholder="Введите пароль" class="field field_full">
                <span class="show-ele show-login-pass"></span>
            </div>
        </div>

        <span class="reset-link get_modal" modal="modal_forgot" data-role-as="customer">Восстановить пароль</span>

        <div class="buttons-row">
            <button type="submit" class="button button-login">Войти</button>
            <div class="login-error">
                <img src="<?= URL.'assets/img/icons/login-error.svg' ?>" alt="" width="16" height="16" />
                <span>Неправильный телефон или пароль</span>
            </div>
            <div class="separator" data-role-as="customer">Нет аккаунта заказчика?</div>
            <div class="separator" data-role-as="master">Нет аккаунта мастера?</div>
            <button type="button" class="button button-register get_modal" modal="modal_reg_two" data-role-as="customer">Зарегистрироваться</button>
        </div>
    </form>
</div>
