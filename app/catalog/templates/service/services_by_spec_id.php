<?php

use core\engine\DATA;
use core\engine\media;
use core\engine\media_list;

/* @var $speciality \app\std_models\speciality */
$speciality = DATA::get('speciality');

/* @var $user \app\std_models\user */
$user = DATA::get('user');

$me = null;
$favorite = false;
$order_is_complete = null;
$spec_id = null;

if(DATA::has('USER')) {
    /* @var $me \app\std_models\user */
    $me = DATA::get('USER');

    if(in_array($user->service_id, $me->customer_data->get('favorites'), false)){
        $favorite = true;
    }

    $orders = $me->customer_data->get('orders');
    $orders_ids = [];
    if (!empty($orders)) {
        foreach ($orders as $order){
            $orders_ids[] = $order['id'];
        }
    }

    $order_is_complete = in_array($user->service_id, $orders_ids);

    $spec_id = array_key_first($user->get_servises_ids_by_spec());
}

$all_score = 0;
$countReviews = count($user->reviews);

if ($countReviews) {
    $temp_score = 0;

    foreach ($user->reviews as $review) {
        $temp_score += $review->score;
    }

    $all_score = round($temp_score / $countReviews, 1);
}

if ($user->master_data->get('type_ip')) {
    $company_type = 'ИП';
} elseif ($user->master_data->get('type_ooo')) {
    $company_type = 'ООО';
} else {
    $company_type = false;
}

$weekWorkDays = [];
$weekDayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

for ($i = 0; $i < 7; $i++) {
    $weekWorkDays[] = $user->master_data->get('week_'.($i + 1)) ? $weekDayNames[$i] : '—';
}

$weekWorkDays = implode(', ', $weekWorkDays);

$servises_ids_by_spec = $user->get_servises_ids_by_spec();

$socials = array_filter([
        'vk' => $user->master_data->get('vk') ?: null,
        'web' => null, //TODO need implementation
//      'inst' => $user->master_data->get('inst') ?: null,
//      'fb' => $user->master_data->get('fb') ?: null,
]);

$messengers = array_filter([
        'tg' => $user->master_data->get('telegram') ?: null,
        'whatsapp' => $whatsapp = $user->master_data->get('whatsapp') ?: null,
        'viber' => $user->master_data->get('viber') ?: null,
]);

$phone = $user->master_data->get('phone');

$countServices = $user->services ? count($user->services) : 0;
$countWorks = $user->works ? count($user->works) : 0;
$countReviews = $user->reviews ? count($user->reviews) : 0;

?>

<div class="service-full">
    <div class="top-panel">
        <a href="javascript:history.back()" class="link-back">
            <img src="<?= URL.'assets/img/icons/arrow-back.svg' ?>" alt="" width="24" height="24">
            <span>Назад</span>
        </a>
    </div>
    <div class="content-card card-info">
        <div class="column column-avatar">
            <div class="avatar" style="background-image: url('<?= get_avatar($user->id, URL.'assets/img/service-no-photo.svg') ?>')"></div>
            <div class="online-status">
                <?= human_date_status($user->last_visit, 600) ?>
            </div>
        </div>
        <div class="column column-info">
            <div class="info-row speciality"><?= $speciality->name ?></div>
            <div class="info-row name"><?= $user->name ?></div>
            <div class="info-row rating">
                <img src="<?= URL ?>assets/img/icons/star.svg" alt="" width="16" height="16" />
                <span class="value">
                    <?= $all_score ?: '0.0' ?>
                </span>
                <span class="count-reviews">
                    <?= $countReviews ?> <?= num2word($countReviews, ['отзыв', 'отзыва', 'отзывов']) ?>
                </span>
            </div>
            <div class="info-row work">
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
            <div class="info-row tags">
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
            <div class="info-row extend">
                <div class="line">
                    <div class="label">Возраст:</div>
                    <div class="underline"></div>
                    <div class="value"><?= get_age($user->birthday) ?></div>
                </div>
                <div class="line">
                    <div class="label">Адрес:</div>
                    <div class="underline"></div>
                    <div class="value"><?= get_city_name($user->city) ?></div>
                </div>
                <div class="line">
                    <div class="label">Время работы:</div>
                    <div class="underline"></div>
                    <div class="value">
                        <?= (new DateTime($user->master_data->get('open_time')))->format('H:i') ?>-<?= (new DateTime($user->master_data->get('close_time')))->format('H:i') ?>
                    </div>
                </div>
                <div class="line">
                    <div class="label">Дни работы:</div>
                    <div class="underline"></div>
                    <div class="value"><?= $weekWorkDays ?></div>
                </div>
                <?php if ($company_type): ?>
                    <div class="line">
                        <div class="label">Зарегистрирован в качестве юр. лица:</div>
                        <div class="underline"></div>
                        <div class="value">
                            <?= $company_type ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="info-row stack">
                <div class="headline">Работы выполняемые мастером</div>
                <div class="stack-tags">
                    <?php foreach ($user->get_specs() as $spec): ?>
                        <a class="stack-tag" href="<?= URL.'service/'.$servises_ids_by_spec[$spec->id] ?>">
                            <?= $spec->name_cat ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if ($socials): ?>
            <div class="info-row additional">
                <div class="headline">Дополнительная информация</div>
                <div class="socials">
                    <?php if (!empty($socials['vk'])): ?>
                        <a href="https://vk.com/<?= $socials['vk'] ?>" class="link" rel="nofollow noopener noreferrer" target="_blank">
                            <span class="icon vk">
                                <img src="<?= URL.'assets/img/icons/socials/vk.svg' ?>" alt="" width="20" height="20" />
                            </span>
                            <span>Вконтакте</span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($socials['web'])): ?>
                        <a href="<?= $socials['web'] ?>" class="link" rel="nofollow noopener noreferrer" target="_blank">
                            <span class="icon web">
                                <img src="<?= URL.'assets/img/icons/socials/web.svg' ?>" alt="" width="20" height="20" />
                            </span>
                            <span>Сайт</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="column column-contacts">
            <div class="contacts-card">
                <div class="info-row top-row">
                    <div class="price">
                        от <?= (int) $user->range_price ?> ₽
                    </div>
                    <div>
                        <?php if ($me): ?>
                            <span class="favorite-button favorite <?= $favorite ? 'active' : '' ?>" service_id="<?= $user->service_id; ?>">
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
                </div>
                <div class="info-row call-buttons">
                    <a class="btn phone see_phone" phone="<?= formatphone($phone) ?>" phone_raw="<?= $phone ?>">
                        <img src="<?= URL.'assets/img/icons/phone.svg' ?>" alt="" width="20" height="20" />
                        <span>Позвонить</span>
                    </a>
                    <?php if (isset($me) && $me->is_customer()): ?>
                        <?php if ($order_is_complete): ?>
                            <a href="<?= URL.'history/' ?>" class="btn chat">
                                <img src="<?= URL.'assets/img/icons/chat.svg' ?>" alt="" width="20" height="20" />
                                <span>Перейти в заказы</span>
                            </a>
                        <?php else: ?>
                            <button
                                service_id="<?= $user->service_id ?>"
                                this_user_id="<?= $me->id ?>"
                                to_whom_user_id="<?= $user->id ?>"
                                status="2"
                                role="customer"
                                spec_id="<?= $spec_id; ?>"
                                class="btn chat work_with_master"
                            >
                                <img src="<?= URL.'assets/img/icons/chat.svg' ?>" alt="" width="20" height="20" />
                                <span class="text">Работаю с мастером</span>
                            </button>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
                <?php if ($messengers): ?>
                    <div class="info-row socials">
                        <div class="headline">Мастер в мессенджерах</div>
                        <div class="social-links">
                            <?php if (!empty($messengers['tg'])): ?>
                                <a href="https://t.me/<?= $messengers['tg'] ?>" class="link" rel="nofollow noopener noreferrer" target="_blank">
                                    <span class="icon">
                                        <img src="<?= URL.'assets/img/icons/socials/tg.svg' ?>" alt="" width="20" height="20" />
                                    </span>
                                    <span>Telegram</span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($messengers['whatsapp'])): ?>
                                <a href="https://wa.me/<?= $messengers['whatsapp'] ?>" class="link" rel="nofollow noopener noreferrer" target="_blank">
                                    <span class="icon">
                                        <img src="<?= URL.'assets/img/icons/socials/wapp.svg' ?>" alt="" width="20" height="20" />
                                    </span>
                                    <span>WhatsApp</span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($messengers['viber'])): ?>
                                <a href="viber://chat?number=%2B<?= $messengers['viber'] ?>" class="link" rel="nofollow noopener noreferrer" target="_blank">
                                    <span class="icon">
                                        <img src="<?= URL.'assets/img/icons/socials/viber.svg' ?>" alt="" width="20" height="20" />
                                    </span>
                                    <span>Viber</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?endif; ?>
            </div>
            <div class="registration-date">
                На сайте с <?= r_date($user->reg_time)?> <?= get_age($user->reg_time, '(', ')')?>
            </div>
        </div>
    </div>

    <div class="content-card card-portfolio service_tabs_wrapper">
        <ul class="tabs_titles">
            <li class="select_tab active" role="global_tabs" tab="works">
                <span>Работы</span>
                <span class="count"><?= $countWorks ?></span>
            </li>
            <li class="select_tab" role="global_tabs" tab="prices">
                <span>Услуги</span>
                <span class="count"><?= $countServices ?></span>
            </li>
            <li class="select_tab" role="global_tabs" tab="reviews">
                <span>Отзывы</span>
                <span class="count"><?= $countReviews ?></span>
            </li>
        </ul>

        <div class="service_tabs_content_wrapper">
            <div class="tab_content content active" role="global_tabs" tab="works">
                <?php if ($countWorks): ?>

                    <div class="works">
                        <?php
                        /* @var $work \app\std_models\work */
                        foreach ($user->works as $work):
                            /* @var $media media */
                            $media = $work->get_first_media();
                            $countPhoto = count($work->get_medias());
                        ?>
                            <div class="work get_modal set_work_item"
                                 modal="modal_work_item"
                                 work_id="<?= $work->id ?>"
                                 wrap_type="black_bg"
                                 style="background-image: url('<?= $media->get_url('704x512') ?>')"
                            >
                                <div class="item-header">
                                    <div class="count"><?= $countPhoto ?> фото</div>
                                </div>
                                <div class="item-footer">
                                    <div class="title"><?= $work->name ?></div>
                                    <div class="price"><?= money_beautiful($work->price);?>₽</div>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>

                <?php else: ?>
                    <div class="not-found">
                        <div class="headline">Проектов нет</div>
                        <div class="description">У мастера пока нет выполненных проектов</div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="tab_content content" role="global_tabs" tab="prices">
                <?php if ($countServices): ?>

                    <div class="services">
                        <?php
                        /* @var $service \app\std_models\user_service */
                        foreach ($user->services as $service):
                            ?>
                            <div class="service">
                                <span class="name"><?= $service->name ?></span>
                                <span class="price"><?= $service->get_correct_price() ?></span>
                            </div>
                        <? endforeach; ?>
                    </div>

                <?php else: ?>
                    <div class="not-found">
                        <div class="headline">Услуг нет</div>
                        <div class="description">У мастера пока нет активных услуг</div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="tab_content content" role="global_tabs" tab="reviews">
                <?php if ($countReviews): ?>

                    <div class="reviews-wrapper">
                        <div class="total-rating">
                            <img src="<?= URL ?>assets/img/icons/star.svg" alt="" width="16" height="16" />
                            <span class="value">
                                <?= $all_score ?: '0.0' ?>
                            </span>
                            <div class="count-reviews">
                                <?= $countReviews ?> <?= num2word($countReviews, ['отзыв', 'отзыва', 'отзывов']) ?>
                            </div>
                        </div>
                        <div class="reviews">
                            <?php get_block('reviews', null, ['reviews' => $user->reviews]); ?>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="not-found">
                        <div class="headline">Отзывов нет</div>
                        <div class="description">У мастера пока нет отзывов</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
