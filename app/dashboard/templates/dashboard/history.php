<?
use \core\engine\DATA;

use app\std_models\dialog;

/* @var $user \app\std_models\user */
$users = DATA::get('users');

/* @var $me \app\std_models\user */
$me = DATA::get('USER');

?>


<div class="history_wrapper">

    <?php
    //print_r($users);
    if(empty($users)){
        $users = [];
    }
    $k = 0;
    foreach ($users as $user){?>
        <?php
        if($user->history_status == "2"){
            continue;
        }
        $k++;
    }
    ?>
    <?  //if(!empty($users)){

        if($k != 0){?>
    <?php

    ?>
    <!--

    <h2 class="xs_hide">Всего заказов <? if(!empty($users)){?><span class="counter"><?= count($users);?></span><?}?></h2>
    -->
    <h2 class="xs_hide">Всего заказов <? if($k != 0){?><span class="counter"><?= $k; ?></span><?}?></h2>

    <table class="history_table xs_hide">
        <thead>
            <tr class="history_head">
                <th>Аватар</th>
                <th class="sort_name">
                    ФИО
                    <span class="sort-icon"></span>
                </th>
                <th class="sort_spec">
                    Специализация
                    <span class="sort-icon"></span>
                </th>
                <th class="sort_date" data-sorter="shortDate" data-date-format="ddmmyyyy">
                    Дата
                    <span class="sort-icon"></span>
                </th>
                <th>
                    Опции
                    <span class="sort-icon"></span>
                </th>
                <th></th>
                <th></th>
            </tr>
        </thead>

        <tbody class="history_tbody">
        <? foreach ($users as $user){?>
            <?php
            if($user->history_status == "1"){
                continue;
            }
            ?>
            <tr class="history_item" service_id="<?= $user->service_id; ?>">
                <td class="avatar_td">
                    <img class="avatar" src="<?= get_avatar($user->id)?>">
                    <? if(is_online_by_date($user->last_visit, 600)){?>
                        <span class="online"></span>
                    <?}?>
                </td>

                <td class="name"><?= $user->name ?></td>
                <td><?= $user->speciality->name ?></td>
                <td class="date"><?= $user->history_date ?></td>
                <td class="history_controls_wrapper">
                    <div class="history_controls">
                        <?
                        $time = (new DateTime($user->history_date))->add(new DateInterval('P1D'));
                        if($time >= new DateTime()){

                            $dialog = new dialog();
                            $dialog_id = $dialog->isset_dialog($me->id, $user->id);

                            ?>
                            <a href="#" class="add_review_get_modal1" onClick="add_review_get_modal(<?= $me->id; ?>, <?= $user->id; ?>, <?= $user->speciality->id; ?>, <?= $dialog_id; ?>) " user_id="<?= $user->id; ?>" spec_id="<?= $user->speciality->id; ?>" service_id="<?= $user->service_id; ?>"><i class="icon icon-reviews"></i>Оставить отзыв</a>
                        <?} ?>

                       <a href="<?= URL?>messages/<?= $user->id?>" class="text-button">
                           <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                               <path fill-rule="evenodd" clip-rule="evenodd" d="M1.07921 17.4144L1.0827 17.4074L1.09448 17.3836C1.10496 17.3624 1.12052 17.3308 1.14046 17.29C1.18034 17.2084 1.23763 17.09 1.3065 16.9446C1.4445 16.6532 1.62785 16.2559 1.81057 15.8296C1.99414 15.4012 2.1727 14.9537 2.30404 14.5597C2.44457 14.1381 2.49993 13.8662 2.49993 13.75C2.49993 13.3379 2.39329 12.7607 2.26382 12.0974L2.24366 11.9943C2.12979 11.4127 1.99993 10.7495 1.99993 10.25C1.99993 5.69365 5.69358 2 10.2499 2C14.8063 2 18.4999 5.69365 18.4999 10.25C18.4999 14.8063 14.8063 18.5 10.2499 18.5C9.41843 18.5 8.54839 18.2001 7.86275 17.9568L7.69448 17.8969L7.69443 17.8969L7.69437 17.8969C7.39931 17.7917 7.1368 17.6981 6.89131 17.6252C6.59591 17.5373 6.38962 17.5 6.24993 17.5C6.25361 17.5 6.25155 17.5002 6.24264 17.5013C6.22109 17.5039 6.15944 17.5114 6.04152 17.5319C5.89679 17.557 5.70923 17.5941 5.49038 17.6402C5.05366 17.7321 4.51346 17.8555 3.98421 17.9801C3.4559 18.1044 2.94263 18.2288 2.56114 18.3222C2.37049 18.3689 2.21296 18.4078 2.10317 18.435L1.93248 18.4774C1.64832 18.5485 1.34817 18.4478 1.1649 18.2193C0.981635 17.9908 0.948245 17.6764 1.07921 17.4144ZM6 11C6.55228 11 7 10.5523 7 10C7 9.44772 6.55228 9 6 9C5.44772 9 5 9.44772 5 10C5 10.5523 5.44772 11 6 11ZM11 10C11 10.5523 10.5523 11 10 11C9.44771 11 9 10.5523 9 10C9 9.44772 9.44771 9 10 9C10.5523 9 11 9.44772 11 10ZM14 11C14.5523 11 15 10.5523 15 10C15 9.44772 14.5523 9 14 9C13.4477 9 13 9.44772 13 10C13 10.5523 13.4477 11 14 11Z" fill="#1F1F1F"/>
                           </svg>
                           <span>Посмотреть диалог</span>
                       </a>
                    </div>
                </td>

                <td class="button_wrap">
                    <a class="button small dark" href="<?= URL?>service/<?= $user->service_id; ?>">
                        Связаться с мастером
                    </a>
                </td>

                <td class="delete_wrap"><i class="icon icon-delete delete_history" service_id="<?= $user->service_id; ?>"  this_user_id="<?= $me->id; ?>"  to_whom_user_id="<?= $user->id; ?>" status="1" role="customer" spec_id="1"></i></td>
            </tr>
        <? } ?>
        </tbody>
    </table>

        <script>
            $(function() {
                $(".history_table").tablesorter({

                    theme : 'blue',

                    dateFormat : "mmddyyyy", // set the default date format

                    headers: {
                        0: { sorter: false },
                        1: { sorter: true},
                        2: { sorter: true},
                        3: { sorter: "shortDate" },
                        4: { sorter: false},
                        5: { sorter: false},
                        6: { sorter: false},
                    }
                });
            });

        </script>

        <div class="history_items_xs">
            <? foreach ($users as $user){?>
                <div class="history_item" service_id="<?= $user->service_id; ?>">

                    <div class="base_info">
                        <div class="avatar_wrapper">
                            <div class="avatar">
                                <img class="avatar" src="<?= get_avatar($user->id)?>">
                                <? if(is_online_by_date($user->last_visit, 600)){?>
                                    <span class="online"></span>
                                <?}?>
                            </div>
                        </div>

                        <div class="info">
                            <p class="name"><?= $user->name ?></p>
                            <p class="profession"><?= $user->speciality->name ?></p>
                            <p class="date">Дата: <span><?= $user->history_date ?></span></p>
                        </div>


                        <div class="delete_wrap">
                            <i class="icon icon-delete delete_history"  service_id="<?= $user->service_id; ?>"  this_user_id="<?= $me->id; ?>"  to_whom_user_id="<?= $user->id; ?>" status="1" role="customer" ></i>
                        </div>
                    </div>

                    <div class="history_controls_wrapper">
                        <div class="history_controls">
                            <?
                            $time = (new DateTime($user->history_date))->add(new DateInterval('P1D'));
                            if($time >= new DateTime()){


                                $dialog = new dialog();
                            $dialog_id = $dialog->isset_dialog($me->id, $user->id);


                                ?>
                                <a href="#" class="add_review_get_modal1" onClick="add_review_get_modal(<?= $me->id; ?>, <?= $user->id; ?>, <?= $user->speciality->id; ?>, <?= $dialog_id; ?>) " user_id="<?= $user->id; ?>" spec_id="<?= $user->speciality->id; ?>" service_id="<?= $user->service_id; ?>"><i class="icon icon-reviews"></i>Оставить отзыв</a>
                            <?} ?>

                            <a class="dialog_item" href="<?= URL?>messages/<?= $user->id?>">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.07921 17.4144L1.0827 17.4074L1.09448 17.3836C1.10496 17.3624 1.12052 17.3308 1.14046 17.29C1.18034 17.2084 1.23763 17.09 1.3065 16.9446C1.4445 16.6532 1.62785 16.2559 1.81057 15.8296C1.99414 15.4012 2.1727 14.9537 2.30404 14.5597C2.44457 14.1381 2.49993 13.8662 2.49993 13.75C2.49993 13.3379 2.39329 12.7607 2.26382 12.0974L2.24366 11.9943C2.12979 11.4127 1.99993 10.7495 1.99993 10.25C1.99993 5.69365 5.69358 2 10.2499 2C14.8063 2 18.4999 5.69365 18.4999 10.25C18.4999 14.8063 14.8063 18.5 10.2499 18.5C9.41843 18.5 8.54839 18.2001 7.86275 17.9568L7.69448 17.8969L7.69443 17.8969L7.69437 17.8969C7.39931 17.7917 7.1368 17.6981 6.89131 17.6252C6.59591 17.5373 6.38962 17.5 6.24993 17.5C6.25361 17.5 6.25155 17.5002 6.24264 17.5013C6.22109 17.5039 6.15944 17.5114 6.04152 17.5319C5.89679 17.557 5.70923 17.5941 5.49038 17.6402C5.05366 17.7321 4.51346 17.8555 3.98421 17.9801C3.4559 18.1044 2.94263 18.2288 2.56114 18.3222C2.37049 18.3689 2.21296 18.4078 2.10317 18.435L1.93248 18.4774C1.64832 18.5485 1.34817 18.4478 1.1649 18.2193C0.981635 17.9908 0.948245 17.6764 1.07921 17.4144ZM6 11C6.55228 11 7 10.5523 7 10C7 9.44772 6.55228 9 6 9C5.44772 9 5 9.44772 5 10C5 10.5523 5.44772 11 6 11ZM11 10C11 10.5523 10.5523 11 10 11C9.44771 11 9 10.5523 9 10C9 9.44772 9.44771 9 10 9C10.5523 9 11 9.44772 11 10ZM14 11C14.5523 11 15 10.5523 15 10C15 9.44772 14.5523 9 14 9C13.4477 9 13 9.44772 13 10C13 10.5523 13.4477 11 14 11Z" fill="#1F1F1F"/>
                                </svg>
                                Посмотреть диалог
                            </a>
                        </div>
                    </div>

                    <div class="button_wrap">
                        <a class="button small dark" href="<?= URL?>service/<?= $user->service_id; ?>">Связаться с мастером</a>
                    </div>

                </div>
            <? } ?>
        </div>

    <?}else{?>
            <div class="catalog-empty">
                <img src="<?= URL ?>assets/img/icons/find.svg" alt="" width="64" height="64">
                <p class="catalog-empty-heading">Список заказов пуст</p>
            </div>
    <? } ?>
</div>


