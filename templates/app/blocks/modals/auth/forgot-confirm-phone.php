<div class="modal modal_forgot_confirm_code modal_auth modal_v2">

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

        <div class="step-row" data-step="2" data-step-max="3">
            <div class="progress-bar">
                <svg class="progress" x="0px" y="0px" viewBox="0 0 80 80">
                    <path class="track" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" />
                    <path class="fill" d="M5,40a35,35 0 1,0 70,0a35,35 0 1,0 -70,0" />
                    <text class="value" x="50%" y="58%">2/3</text>
                </svg>
            </div>
        </div>

        <div class="title">
            <span>Восстановить пароль</span>
        </div>
        <div class="subtitle">
            Мы отправили код подтверждения на номер
            <span class="confirm_phone"></span>
            <a href="#" class="get_modal" modal="modal_forgot">Изменить</a>
        </div>
    </div>

    <form class="ajax_sender sms_confirm" action_fn="forgot_sms_confirm">
        <input id="forgot_sms_hash" type="hidden" name="sms_hash" />
        <input type="hidden" name="phone" value="" />

        <div class="field_wrap">
            <label for="forgot_sms_code">Введите код из SMS</label>
            <input type="text" name="code" id="forgot_sms_code" placeholder="Код" class="field" />
        </div>

        <div class="buttons-row">
            <button type="submit" class="button button-login">Продолжить</button>
            <?php get_block('components/sms-status'); ?>
        </div>
    </form>
</div>
