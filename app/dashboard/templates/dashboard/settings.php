<?
/* @var $user \app\std_models\user  */
$user = \core\engine\DATA::get('USER');

$user->master_data->get('week_1')

?>

<form class="update_settings" method="post">

    <h2 class="page_title">О мастере
                    <span class="info_tooltip">
                        <p class="tooltip">
                            <span>Рекомедуем заполнять как можно больше информации о вас - это повышает доверие</span>
                        </p>
                    </span>
    </h2>

    <?/*
    <div class="row">
        <div class="c_6">

            <div class="field_wrap">
                <label for="rayon">Выберите куда выезжаете к клиентам</label>

                <div class="micro_form">
                    <input type="text" name="rayon" id="rayon" class="field" placeholder="Название района">
                    <button class="button"><i class="icon icon-add"></i></button>
                </div>

            </div>

        </div>
    </div>

    <div class="tags">

        <div class="tag_item">
            <span>Центральный округ</span>
            <i class="icon icon-cancel"></i>
        </div>

        <div class="tag_item">
            <span>Прикубанский район</span>
            <i class="icon icon-cancel"></i>
        </div>

        <div class="tag_item">
            <span>Фестивальный район</span>
            <i class="icon icon-cancel"></i>
        </div>

        <div class="tag_item">
            <span>Зиповский район</span>
            <i class="icon icon-cancel"></i>
        </div>

    </div>
    */?>

    <div class="field_wrap">
        <label>Дни работы</label>

        <div class="row weeks_xs">
            <div class="checkbox_input">
                <input type="checkbox" id="week_1" name="week_1" <?= ($user->master_data->get('week_1') == 1) ? 'checked' : '' ;?>>
                <label for="week_1">Понедельник</label>
            </div>

            <div class="checkbox_input">
                <input type="checkbox" id="week_2" name="week_2" <?= ($user->master_data->get('week_2') == 1) ? 'checked' : '' ;?>>
                <label for="week_2">Вторник</label>
            </div>

            <div class="checkbox_input">
                <input type="checkbox" id="week_3" name="week_3" <?= ($user->master_data->get('week_3') == 1) ? 'checked' : '' ;?>>
                <label for="week_3">Среда</label>
            </div>

            <div class="checkbox_input">
                <input type="checkbox" id="week_4" name="week_4" <?= ($user->master_data->get('week_4') == 1) ? 'checked' : '' ;?>>
                <label for="week_4">Четверг</label>
            </div>

            <div class="checkbox_input">
                <input type="checkbox" id="week_5" name="week_5" <?= ($user->master_data->get('week_5') == 1) ? 'checked' : '' ;?>>
                <label for="week_5">Пятница</label>
            </div>

            <div class="checkbox_input">
                <input type="checkbox" id="week_6" name="week_6" <?= ($user->master_data->get('week_6') == 1) ? 'checked' : '' ;?>>
                <label for="week_6">Суббота</label>
            </div>

            <div class="checkbox_input">
                <input type="checkbox" id="week_7" name="week_7" <?= ($user->master_data->get('week_7') == 1) ? 'checked' : '' ;?>>
                <label for="week_7">Воскресенье</label>
            </div>
        </div>

    </div>

    <div class="row time_flex">
        <div class="c_5 xs_c_12">

            <div class="field_wrap xs_pr_0">
                <label for="time">Время работы</label>
                <div class="time_form">
                    <input type="time" name="open_time" class="field field_full" value="<?= ($user->master_data->get('open_time') != '00:00:00') ? $user->master_data->get('open_time') : '';?>">
                    <input type="time" name="close_time" class="field field_full" value="<?= ($user->master_data->get('close_time') != '00:00:00') ? $user->master_data->get('close_time') : '';?>">
                </div>
            </div>

        </div>
    </div>

    <div class="row time_flex">

        <div class="c_5 xs_c_12">
            <div class="toggle_input">
                <input type="checkbox" name="contract" id="but_1" class="toggle_button" <?= ($user->master_data->get('contract') == 1) ? 'checked' : '' ;?>>
                <label for="but_1">Работа по договору</label>
            </div>
        </div>

        <div class="c_5	 xs_c_12">
            <div class="toggle_input">
                <input type="checkbox" name="guarantee" id="but_2" class="toggle_button" <?= ($user->master_data->get('guarantee') == 1) ? 'checked' : '' ;?>>
                <label for="but_2">Делаете гарантию</label>
            </div>
        </div>

    </div>

    <br>
        <div>
            Наличие юридического лица
            <span class="info_tooltip">
                <p class="tooltip">
                    <span>Загрузите подтверждающие документы, мы проверим их и подтвердим что мы вам ддоверяем. Добавьте документы подтверждающие ИП или ООО</span>
                </p>
            </span>
        </div>
    <br>

    <div class="row">
        <div class="c_12 xs_c_12">
            <span>Проверен сервисом:</span>
            <img src="<?= URL.'assets/img/icons/'.(($user->master_data->get('type_ooo') != 0 || $user->master_data->get('type_ip') != 0) ? 'true' : 'false').'.svg'; ?>">
        </div>
    </div>
    <br>

        <!--
        <div class="c_3 xs_c_6">
            <div class="toggle_input">
                <input type="hidden" name="egrlip_file">
                <input type="checkbox" name="toggle" id="type_ip" class="toggle_button" <?= ($user->master_data->get('type_ip') == 1) ? 'checked' : '' ;?> disabled>
                <label for="type_ip">ИП</label>
            </div>
            <span class="add_doc_but get_modal" modal="modal_add_company_doc" doc_type="ip" wrap_type="white_bg"><i class="icon icon-add"></i>Добавить ЕГРИП</span>
        </div>

        <div class="c_3 xs_c_6">
            <div class="toggle_input">
                <input type="hidden" name="egrl_file">
                <input type="checkbox" name="toggle" id="type_ooo" class="toggle_button" <?= ($user->master_data->get('type_ooo') == 1) ? 'checked' : '' ;?> disabled>
                <label for="type_ooo">ООО</label>
            </div>
            <span class="add_doc_but get_modal" modal="modal_add_company_doc" doc_type="ooo" wrap_type="white_bg"><i class="icon icon-add"></i>Добавить ЕГРЮЛ</span>
        </div>
        -->
    <div class="row add_doc_but_flex">
        <div class="c_3 xs_c_6 add_doc_but_flex_item">
            <span class="add_doc_but get_modal" modal="modal_add_company_doc" doc_type="ip" wrap_type="white_bg"><i class="icon icon-add"></i>Добавить ЕГРИП</span>
        </div>

        <div class="c_3 xs_c_6 add_doc_but_flex_item">
            <span class="add_doc_but get_modal" modal="modal_add_company_doc" doc_type="ooo" wrap_type="white_bg"><i class="icon icon-add"></i>Добавить ЕГРЮЛ</span>
        </div>

    </div>

    <div class="row">
        <div class="c_3 xs_c_12 setting_btn">

            <input class="button save_profile save_settings" type="submit" value="Сохранить изменения">

        </div>
    </div>

</form>
