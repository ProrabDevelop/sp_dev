<section id="footer_menu_wrap" class="xs_only">

    <div class="footer_menu">

        <a href="<?= URL?>" class="logo"></a>

        <div class="footer_city_changer_wrapper">
            <p>Город:</p>
            <p class="city_changer_field">
                <i class="icon icon-marker"></i>
                <input type="text">
            </p>
        </div>

        <div class="finder_wrapper">
            <a class="button" href="<?=URL?>find/">Поиск мастера</a>
        </div>

        <div class="accordion_wrapper">
            <div class="accordion">
                <p class="title"><span>О сервисе</span> <i class="icon icon-down-open"></i></p>
                <ul class="accordion_body">
                    <li><a href="#">Что такое ПРОРАБ сервис</a></li>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Пользовательское соглашание</a></li>
                </ul>
            </div>

            <div class="accordion">
                <p class="title"><span>Застройщику</span> <i class="icon icon-down-open"></i></p>
                <ul class="accordion_body">
                    <li><a href="#">Что такое ПРОРАБ сервис</a></li>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Пользовательское соглашание</a></li>
                </ul>
            </div>

            <div class="accordion">
                <p class="title"><span>Подрядчику</span> <i class="icon icon-down-open"></i></p>
                <ul class="accordion_body">
                    <li><a href="#">Что такое ПРОРАБ сервис</a></li>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Пользовательское соглашание</a></li>
                </ul>
            </div>
        </div>

        <div class="end_data">


            <?
            $AUTH = \core\engine\AUTH::init();
            if($AUTH->is_auth()){

                $switch_text = 'Мастер';
                if($AUTH->user->is_master()){
                    $switch_text = 'Заказчик';
                }
                ?>
                <a href="<?= URL?>auth/switch">Войти как <?= $switch_text?></a>

            <?}?>


        </div>

    </div>

    <p class="close_footer_menu">
        <i class="icon icon-cancel"></i>
    </p>

</section>






