<?php

use app\std_models\services_list;
use app\std_models\works_list;
use core\engine\DATA;
use app\std_models\user;


/**
 * @var user $user
 */
$user = DATA::get('USER');
$specs = $user->get_specs();

//$all_services = (new \app\std_models\services_list())->get_all_for_user($user);
//$user->services = (new services_list())->get_by_spec_for_user($user->id, $speciality->id);
$all_works = (new works_list())->get_all_for_user($user);

?>

<div class="std_lk_wrapper prices_wrapper">

    <h2>Выполненные работы
                    <span class="info_tooltip">
                        <p class="tooltip">
                            <span>Добавьте фото и описание работ которые вы уже сделалали, рекомендуем указывать - время выполнения, короткое описание, и загружать качественные фото. Со временем - дополняйте новыми кейсами</span>
                        </p>
                    </span>
    </h2>
    <p class="description">Добавьте реализованные проекты. Это повысит доверие к вам и ускорит связь с вами  </p>

    <span class="add_new_work" modal="modal_add_work"><i class="icon icon-add"></i>Добавить выполненный обьект</span>

    <div class="works_tabs_wrapper">

        <ul class="tabs_titles">
            <?
            /* @var $spec \app\std_models\speciality */
            $i = 0;
            foreach ($specs as $spec){
                $active = ($i == 0)? 'active' : '';
                (!empty($active))? $spec_to_modal = $spec->id : false;
                echo '<li class="select_tab set_spec '.$active.'" role="work_tabs" tab="spec_'.$spec->id.'" spec="'.$spec->id.'">'.$spec->name_cat.'</li>';
                $i++;
            }
            ?>
        </ul>
        <script>
            $(function (){
                $('#new_work_spec').attr('value', '<?= $spec_to_modal?>');
            });
        </script>

        <div class="service_tabs_content_wrapper">

            <?
            $i = 0;

            foreach ($all_works as $spec_id => $works_cat) {
                $active = ($i == 0)? 'active' : '';
                echo '<div class="tab_content '.$active.'" role="work_tabs" tab="spec_'.$spec_id.'">';

                if(empty($works_cat)){
                    echo '<h4 class="work-empty-message">Работы не добавлены</h4>';
                }else{
                    ?>

                    <div class="work_items">
                        <?
                        /* @var $work \app\std_models\work */



                        foreach ($works_cat as $work){?>
                            <div class="work_item set_edit_work_item" work_id="<?= $work->id?>">

                                <div class="img">
                                    <? $media = $work->get_first_media();
                                    /* @var $media \core\engine\media */?>
                                    <img src="<?= $media->get_url('160x130'); ?>">
                                </div>

                                <div class="information">
                                    <div class="title_wrap">
                                        <span class="title"><?= $work->name ?></span>
                                        <span class="price"><?= money_beautiful($work->price);?> ₽</span>
                                    </div>

                                    <div class="content">
                                        <?= $work->content ?>
                                    </div>

                                </div>

                                <span class="delete_work delete_work_trigger text-button" work="<?= $work->id ?>">
                                    <i class="icon icon-delete"></i> <span>Удалить</span>
                                </span>
                            </div>
                        <?}?>
                    </div>

                    <?

                    $i++;
                }
                echo '</div>';

            }?>

        </div>


    </div>



</div>


