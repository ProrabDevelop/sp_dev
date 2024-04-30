
<?php

use app\std_models\sort_rating_prices;
use core\engine\DATA;
use app\std_models\user;


/**
 * @var user $user
 */
$user = DATA::get('USER');
$specs = $user->get_specs();
$all_services = (new \app\std_models\services_list())->get_all_for_user($user);

$sort_rating_prices = (new sort_rating_prices($user))->get_prices();



?>


<div class="std_lk_wrapper prices_wrapper">

    <h2>Услуги и цены
                    <span class="info_tooltip">
                        <p class="tooltip">
                            <span>Больший спектр услуг, это возможность выделиться на фоне конкурентов</span>
                        </p>
                    </span>
    </h2>

    <div class="works_tabs_wrapper">

        <ul class="tabs_titles">
            <?
            /* @var $spec \app\std_models\speciality */
            $i = 0;
            foreach ($specs as $spec){

                if(!isset($all_services[$spec->id])){
                    $all_services[$spec->id] = [];
                }

                $active = ($i == 0)? 'active' : '';
                echo '<li class="select_tab '.$active.'" role="services_tabs" tab="spec_'.$spec->id.'">'.$spec->name.'</li>';
                $i++;
            }
            ?>
        </ul>
        <br>



        <div class="service_tabs_content_wrapper">

            <?
            /* @var $service \app\std_models\user_service */
            $i = 0;

            foreach ($all_services as $spec_id => $service_cat) {
                $active = ($i == 0)? 'active' : '';
                echo '<div class="tab_content '.$active.'" role="services_tabs" tab="spec_'.$spec_id.'">';
                ?>
                <form class="save_prices_info" method="post">
                    <input type="hidden" name="spec_id" value="<?= $spec_id?>">
                <div class="row">
                    <div class="field_wrap xs_pr_0 c_6 xs_c_12 price_flex">
                        <label for="price_<?= $spec_id?>">Укажите среднюю цену за ваши услуги</label>
                        <input name="spec_id" type="hidden" value="<?= $spec_id?>">
                        <input id="price_<?= $spec_id?>" name="price" type="text" class="field_full" value="<?= $sort_rating_prices[$spec_id]?>">
                    </div>
                </div>

                <p class="add_new_service get_modal set_spec" modal="modal_add_service" spec="<?= $spec_id?>"><i class="icon icon-add"></i>Добавить услугу</p>

                <? if(empty($service_cat)){
                    echo '<h4 class="service-empty-message">Нет ни одной услуги</h4>';
                }else{
                    ?>
                    <div class="price_table">
                        <?
                        /* @var $service \app\std_models\user_service*/
                        foreach ($service_cat as $service){?>
                            <div class="price_item" price_item="<?= $service->id ?>">
                                <div class="name"><?= $service->name ?></div>
                                <div class="price"><?= $service->get_correct_price() ?></div>
                                <div class="controls">
                                    <span class="edit_service edit_service_trigger text-button" service="<?= $service->id ?>"><i class="icon icon-edit"></i></span>
                                    <span class="edit_service delete_service delete_service_trigger text-button" service="<?= $service->id ?>"><i class="icon icon-cancel"></i></span>
                                </div>
                            </div>
                        <?}?>
                    </div>
                    <?
                    $i++;
                }?>
                <input class="button save_price_info save_settings" type="submit" value="Сохранить изменения">
                <?
                echo '</form></div>';

            }?>



        </div>

    </div>




</div>



