<?
$user = \core\engine\DATA::get('USER');
$all_unreaded_count = (new \app\std_models\message())->get_all_unread_count($user->id);
?>

<ul class="lk_menu">

    <li>
        <a href="<?= URL?>dashboard">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.0026 1.875C7.81648 1.875 6.04427 3.64721 6.04427 5.83333C6.04427 8.01946 7.81648 9.79167 10.0026 9.79167C12.1887 9.79167 13.9609 8.01946 13.9609 5.83333C13.9609 3.64721 12.1887 1.875 10.0026 1.875Z" />
                <path d="M10.0026 11.875C8.10655 11.875 6.36215 12.1621 5.06911 12.647C4.42489 12.8886 3.86014 13.1913 3.44521 13.5592C3.03101 13.9265 2.71094 14.4119 2.71094 15C2.71094 15.5881 3.03101 16.0735 3.44521 16.4408C3.86014 16.8086 4.42489 17.1114 5.06911 17.353C6.36215 17.8379 8.10655 18.125 10.0026 18.125C11.8987 18.125 13.6431 17.8379 14.9361 17.353C15.5803 17.1114 16.1451 16.8086 16.56 16.4408C16.9742 16.0735 17.2943 15.5881 17.2943 15C17.2943 14.4119 16.9742 13.9265 16.56 13.5592C16.1451 13.1913 15.5803 12.8886 14.9361 12.647C13.6431 12.1621 11.8987 11.875 10.0026 11.875Z" />
            </svg>
            <span><span class="xs_hide">Личный</span> Кабинет</span>
        </a>
    </li>

    <li>
        <a href="<?= URL?>messages">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.07921 17.4144L1.0827 17.4074L1.09448 17.3836C1.10496 17.3624 1.12052 17.3308 1.14046 17.29C1.18034 17.2084 1.23763 17.09 1.3065 16.9446C1.4445 16.6532 1.62785 16.2559 1.81057 15.8296C1.99414 15.4012 2.1727 14.9537 2.30404 14.5597C2.44457 14.1381 2.49993 13.8662 2.49993 13.75C2.49993 13.3379 2.39329 12.7607 2.26382 12.0974L2.24366 11.9943C2.12979 11.4127 1.99993 10.7495 1.99993 10.25C1.99993 5.69365 5.69358 2 10.2499 2C14.8063 2 18.4999 5.69365 18.4999 10.25C18.4999 14.8063 14.8063 18.5 10.2499 18.5C9.41843 18.5 8.54839 18.2001 7.86275 17.9568L7.69448 17.8969L7.69443 17.8969L7.69437 17.8969C7.39931 17.7917 7.1368 17.6981 6.89131 17.6252C6.59591 17.5373 6.38962 17.5 6.24993 17.5C6.25361 17.5 6.25155 17.5002 6.24264 17.5013C6.22109 17.5039 6.15944 17.5114 6.04152 17.5319C5.89679 17.557 5.70923 17.5941 5.49038 17.6402C5.05366 17.7321 4.51346 17.8555 3.98421 17.9801C3.4559 18.1044 2.94263 18.2288 2.56114 18.3222C2.37049 18.3689 2.21296 18.4078 2.10317 18.435L1.93248 18.4774C1.64832 18.5485 1.34817 18.4478 1.1649 18.2193C0.981635 17.9908 0.948245 17.6764 1.07921 17.4144ZM6 11C6.55228 11 7 10.5523 7 10C7 9.44772 6.55228 9 6 9C5.44772 9 5 9.44772 5 10C5 10.5523 5.44772 11 6 11ZM11 10C11 10.5523 10.5523 11 10 11C9.44771 11 9 10.5523 9 10C9 9.44772 9.44771 9 10 9C10.5523 9 11 9.44772 11 10ZM14 11C14.5523 11 15 10.5523 15 10C15 9.44772 14.5523 9 14 9C13.4477 9 13 9.44772 13 10C13 10.5523 13.4477 11 14 11Z" />
            </svg>
            <span>Сообщения</span><span class="counter <?= ($all_unreaded_count < 1)? 'hide': '';?> all_unread_messages_counter"><?= $all_unreaded_count;?></span>
        </a>
    </li>

    <li>
        <a href="<?= URL?>reviews">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.835938 17.5007H4.16927V7.50065H0.835938V17.5007ZM19.1693 8.33398C19.1693 7.41732 18.4193 6.66732 17.5026 6.66732H12.2443L13.0359 2.85898L13.0609 2.59232C13.0609 2.25065 12.9193 1.93398 12.6943 1.70898L11.8109 0.833984L6.3276 6.32565C6.01927 6.62565 5.83594 7.04232 5.83594 7.50065V15.834C5.83594 16.7507 6.58594 17.5007 7.5026 17.5007H15.0026C15.6943 17.5007 16.2859 17.084 16.5359 16.484L19.0526 10.609C19.1276 10.4173 19.1693 10.2173 19.1693 10.0007V8.33398Z" />
            </svg>
            <span>Отзывы</span>
        </a>
    </li>

    <li>
        <a class="has_child">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path id="Vector" d="M15.9504 10.7833C15.9838 10.5333 16.0004 10.275 16.0004 10C16.0004 9.73333 15.9838 9.46667 15.9421 9.21667L17.6338 7.9C17.7838 7.78333 17.8254 7.55833 17.7338 7.39167L16.1338 4.625C16.0338 4.44167 15.8254 4.38333 15.6421 4.44167L13.6504 5.24167C13.2337 4.925 12.7921 4.65833 12.3004 4.45833L12.0004 2.34167C11.9671 2.14167 11.8004 2 11.6004 2H8.40042C8.20042 2 8.04208 2.14167 8.00875 2.34167L7.70875 4.45833C7.21708 4.65833 6.76708 4.93333 6.35875 5.24167L4.36708 4.44167C4.18375 4.375 3.97542 4.44167 3.87542 4.625L2.28375 7.39167C2.18375 7.56667 2.21708 7.78333 2.38375 7.9L4.07542 9.21667C4.03375 9.46667 4.00042 9.74167 4.00042 10C4.00042 10.2583 4.01708 10.5333 4.05875 10.7833L2.36708 12.1C2.21708 12.2167 2.17542 12.4417 2.26708 12.6083L3.86708 15.375C3.96708 15.5583 4.17542 15.6167 4.35875 15.5583L6.35042 14.7583C6.76708 15.075 7.20875 15.3417 7.70042 15.5417L8.00042 17.6583C8.04208 17.8583 8.20042 18 8.40042 18H11.6004C11.8004 18 11.9671 17.8583 11.9921 17.6583L12.2921 15.5417C12.7837 15.3417 13.2338 15.075 13.6421 14.7583L15.6338 15.5583C15.8171 15.625 16.0254 15.5583 16.1254 15.375L17.7254 12.6083C17.8254 12.425 17.7838 12.2167 17.6254 12.1L15.9504 10.7833ZM10.0004 13C8.35042 13 7.00042 11.65 7.00042 10C7.00042 8.35 8.35042 7 10.0004 7C11.6504 7 13.0004 8.35 13.0004 10C13.0004 11.65 11.6504 13 10.0004 13Z" />
            </svg>
            <span>Настройки</span>
        </a>

        <ul><li>

        <div class="back_step_tabs" style="position:absolute; top:20px; left:20px; cursor:pointer;">
            <p class="close_lk_menu"><i class="icon icon-left-open"></i></p>
        </div>

        </li>
            <li><a class="child" href="<?= URL?>settings"><span>О мастере</span><i class="icon icon-right-open"></i></a></li>
            <li><a class="child" href="<?= URL?>contacts"><span>Способы связи</span><i class="icon icon-right-open"></i></a></li>
            <li><a class="child" href="<?= URL?>prices"><span>Услуги и цены</span><i class="icon icon-right-open"></i></a></li>
            <li><a class="child" href="<?= URL?>finished"><span>Выполненные работы</span><i class="icon icon-right-open"></i></a></li>
        </ul>

    </li>

</ul>

<div class="xs_menu_header">
    <p class="menu_back"><i class="icon icon-left-open"></i>Назад</p>
    <p class="name">Настройки</p>
</div>
