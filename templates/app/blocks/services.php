<?php

use core\engine\DATA;

/* @var $speciality \app\std_models\speciality */
/* @var $speciality \app\std_models\speciality_list */
$speciality = DATA::get('speciality');

$services = DATA::get('services');

$me = DATA::has('USER') ? DATA::get('USER') : null;
?>

<?php
foreach ($services as $service):
    $user = $service->user;
    $favorite = $me && in_array($service->id, $me->customer_data->get('favorites'), false);

    //TODO N+1 Problem
    $user_reviews = \ORM::for_table('reviews')->where('master_id', $user->id)->find_many();
    $countReviews = count($user_reviews);

    $all_score = 0;
    $temp_score = 0;
    $l = 0;

    if ($countReviews > 0) {
        for ($i = 0; $i < $countReviews; $i++) {
            if ($user_reviews[$i]["spec_id"] == $service->spec_id) {
                $temp_score += $user_reviews[$i]["score"];
                $l++;
            }
        }

        if ($temp_score) {
            $all_score = round($temp_score / $l, 1);
        }
    }
?>

<div class="service-item" data-link="<?= URL.'service/'.$service->id; ?>">
    <div class="column left">
        <div class="avatar">
            <a href="<?= URL.'service/'.$service->id; ?>">
                <?php if (file_exists(WEB.'uploads/avatars/'.$user->id.'.png')): ?>
                    <img src="<?= URL ?>uploads/avatars/<?= $user->id; ?>.png" alt="" width="100" height="100" />
                <?php else: ?>
                    <div class="no-photo"></div>
                <?php endif; ?>
            </a>
        </div>
        <div class="info">
            <div class="speciality">
                <?= is_array($speciality) ? $speciality[$service->spec_id]->name : $speciality->name ?>
            </div>
            <div class="name">
                <?= $user->name ?>
            </div>
            <div class="rating">
                <img src="<?= URL ?>assets/img/icons/star.svg" alt="" width="16" height="16" />
                <span class="value">
                    <?= $all_score ?: '0.0' ?>
                </span>
                <span class="count-reviews">
                    <?= $countReviews ?> <?= num2word($countReviews, ['отзыв', 'отзыва', 'отзывов']) ?>
                </span>
            </div>
            <div class="online-status">
                <?= human_date_status($user->last_visit, 600) ?>
            </div>
        </div>
    </div>
    <div class="column center">
        <div class="work">
            <div class="finished">
                <div class="label">Выполненных проектов</div>
                <div class="value">
                    <?= (int) $user->master_data->get('finished') ?>
                </div>
            </div>
            <div class="experience">
                <div class="label">Стаж</div>
                <div class="value">
                    <?= !empty(EXP_TEXTS[$user->master_data->get('experience')])
                        ? EXP_TEXTS[$user->master_data->get('experience')]
                        : 'Без опыта'
                    ?>
                </div>
            </div>
        </div>
        <div class="tags">
            <?php if ($user->master_data->get('contract')): ?>
                <span class="tag">
                <img src="<?= URL ?>assets/img/icons/contract.svg" alt="" width="16" height="16" />
                Работа по договору
            </span>
            <?php endif; ?>
            <?php if ($user->master_data->get('guarantee')): ?>
                <span class="tag">
                <img src="<?= URL ?>assets/img/icons/shield.svg" alt="" width="16" height="16" />
                Гарантия работ
            </span>
            <?php endif; ?>
            <?php if ($user->master_data->get('type_ooo') || $user->master_data->get('type_ip')): ?>
                <span class="tag green">
                    <img src="<?= URL ?>assets/img/icons/ok.svg" alt="" width="16" height="16" />
                    Проверен сервисом
                </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="column right">
        <div class="row-top">
            <div class="price">
                от <?= (int) $user->range_price ?> ₽
            </div>
            <?php if ($me): ?>
                <span class="favorite-button favorite <?= $favorite ? 'active' : '' ?>" service_id="<?= $service->id; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M16 4H8C6.89543 4 6 4.89543 6 6V21L10.608 16.5335C11.3837 15.7816 12.6163 15.7816 13.392 16.5335L18 21V6C18 4.89543 17.1046 4 16 4Z" stroke="#BFBFBF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            <?php else: ?>
                <a class="favorite-button login get_modal" modal="modal_login">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M16 4H8C6.89543 4 6 4.89543 6 6V21L10.608 16.5335C11.3837 15.7816 12.6163 15.7816 13.392 16.5335L18 21V6C18 4.89543 17.1046 4 16 4Z" stroke="#BFBFBF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
        <div class="row-bottom">
            <?php if ($phone = $user->master_data->get('phone')): ?>
                <a class="button phone-button see_phone" phone="<?= formatphone($phone) ?>" phone_raw="<?= $phone ?>">Позвонить</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
