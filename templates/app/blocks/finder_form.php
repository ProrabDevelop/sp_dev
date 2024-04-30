<?php

use core\engine\DATA;

/* @var $speciality \app\std_models\speciality */
$speciality = DATA::has('speciality') && DATA::has('speciality_id') && DATA::get('speciality_id')
    ? DATA::get('speciality')
    : null;

if (isset($_COOKIE['city_finder'])) {
    if (!isset($_COOKIE['city_finder_for_search'])) {
        $city = (new \core\engine\city($_COOKIE['city_finder']));
    } else {
        $city = ORM::for_table('cityes_for_search')->find_one($_COOKIE['city_finder']);
    }

    if (city_available && !array_search($city->name, city_available, true)) {
        $city = get_default_city();

        setcookie('city_finder', $city->id, -1, '/');
    }
} else {
    $city = get_default_city();

    setcookie('city_finder', $city->id, -1, '/');
}
?>

<form class="search_form_form finder_form" method="post" action="<?= URL?>catalog">
    <div class="input_inner_wrapp">
        <div class="finder finder_select c_6">
            <div class="find_field_wrap search_form_input_row">
                <input class="find_by_id" type="hidden" name="find" value="<?= $speciality ? $speciality->id : '' ?>">
                <input
                        type="text"
                        name="speciality"
                        value="<?= $speciality ? $speciality->name : '' ?>"
                        class="find_field"
                        placeholder="Например: Плотник"
                        autocomplete="off"
                />
            </div>
            <ul class="find_field_dropdown"></ul>
        </div>
        <div class="search_form_input_row">
            <div class="main_panel">
                <div class="city_changer">
                    <select id="city_finder" name="city" class="select_city_finder" placeholder="Краснодар">
                        <option><?= $city->name; ?></option>
                        <?php if(!empty( $user->city ) && !city_available): ?>
                            <option value="<?= $user->city ?>"><?= get_city_name($user->city) ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="search_form_input_row btn">
        <button class="button find_but" type="submit">Найти</button>
    </div>
</form>
