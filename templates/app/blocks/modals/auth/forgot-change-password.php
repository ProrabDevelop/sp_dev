<div class="modal modal_forgot_change_password modal_auth modal_v2">
    <div class="modal_header">
        <div class="modal-controls">
            <a href="#" class="get_modal back-link" modal="modal_forgot">
                <img src="<?= URL.'assets/img/icons/modal-back-arrow.svg' ?>" alt="" width="24" height="24" />
            </a>
            <div class="modal_close_wrapper">
                <span class="modal_close">
                    <img src="<?= URL.'assets/img/icons/close-gray.svg' ?>" alt="" width="24" height="24" />
                </span>
            </div>
        </div>

        <div class="step-row" data-step="3" data-step-max="3">
            <div class="progress-bar">
                <svg class="progress" x="0px" y="0px" viewBox="0 0 80 80">
                    <path class="track" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" />
                    <path class="fill" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" />
                    <text class="value" x="50%" y="58%">3/3</text>
                </svg>
            </div>
        </div>

        <div class="title">
            <span>Восстановить пароль</span>
        </div>
        <div class="subtitle">
            Создайте новый пароль
        </div>
    </div>

    <form class="ajax_sender sms_confirm" action_fn="forgot_change_password">
        <input type="hidden" name="sms_hash" value="" />
        <input type="hidden" name="phone" value="" />
        <div class="field_wrap">
            <label for="forgot_pass">Новый пароль</label>
            <div class="input-show-pwd">
                <input type="password" name="pass" id="forgot_pass" placeholder="Введите пароль" class="field field_full">
                <span class="show-ele show-forgot-pass"></span>
            </div>
        </div>
        <div class="field_wrap">
            <label for="forgot_pass_confirm">Повторите новый пароль</label>
            <div class="input-show-pwd">
                <input type="password" name="pass_confirm" id="forgot_pass_confirm" placeholder="Введите пароль" class="field field_full">
                <span class="show-ele show-forgot-pass-confirm"></span>
            </div>
        </div>
        <div class="buttons-row">
            <button class="button button-login">Отправить</button>
        </div>
    </form>
</div>
