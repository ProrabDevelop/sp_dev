<div class="modal modal_register_success modal_auth modal_v2" data-role-as="customer">

    <div class="modal_header">
        <div class="modal_close_wrapper">
            <span class="modal_close">
                <img src="<?= URL.'assets/img/icons/close-gray.svg' ?>" alt="" width="24" height="24" />
            </span>
        </div>

        <div class="success-icon">
            <img src="<?= URL.'assets/img/icons/register-success.svg' ?>" alt="" width="64" height="64" />
        </div>

        <div class="title">
            <span>Успешная регистрация!</span>
        </div>
        <div class="subtitle" data-role-as="customer">
            Вы зарегистрировались под ролью “Заказчика”, ищите надежных исполнителей .....
        </div>
        <div class="subtitle" data-role-as="master">
            Вы зарегистрировались под ролью “Мастера”, ищите заказы .....
        </div>
    </div>

    <div data-role-as="customer">
        <div class="buttons-row">
            <a href="<?= URL ?>" class="button button-login">На главную</a>
        </div>
    </div>
    <div data-role-as="master">
        <div class="master-notice">
            <div class="icon">
                <img src="<?= URL.'assets/img/icons/danger.svg' ?>" alt="" width="32" heigth="32" />
            </div>
            <div class="info">
                <div class="heading">Сейчас ваш профиль скрыт...</div>
                <div class="description">
                    Заполните все обязательные разделы в личнок кабинете, для публикации профиля в каталоге мастеров<br>
                    - Специализация<br>
                    - Способы связи<br>
                    - О мастере<br>
                </div>
            </div>
        </div>
        <div class="buttons-row">
            <a href="<?= URL.'dashboard' ?>" class="button button-login">Перейти к заполнению</a>
            <a href="<?= URL ?>" class="button button-register">На главную</a>
        </div>
    </div>
</div>
