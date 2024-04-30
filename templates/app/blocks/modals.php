<?php

use core\engine\AUTH;
use core\engine\DATA;

$user = DATA::has('USER') ? DATA::get('USER') : null;

$AUTH = AUTH::init();
?>

<div class="modal_wrapper std"></div>
<div class="modal_wrapper white_bg"></div>
<div class="modal_wrapper black_bg"></div>

<form id="avatar_uploader">
    <input type="hidden" name="user_id" id="avatar_upload_user_id" value="<?= $user ? $user->id : 0; ?>">
    <input type="file" name="media" id="avatar_uploader_field">
</form>

<form id="media_uploader">
    <input type="hidden" name="path" id="media_upload_path" value="">
    <input type="hidden" name="sizes" id="media_upload_sizes" value="">
    <input type="hidden" id="media_upload_cb" value="">
    <input type="file" name="media" id="media_upload_field">
</form>

<form id="doc_uploader">
    <input type="hidden" name="path" id="doc_upload_path" value="">
    <input type="hidden" name="doc_type" id="doc_upload_type" value="">
    <input type="hidden" id="doc_upload_cb" value="">
    <input type="file" name="media" id="doc_upload_field">
</form>

<?php if (!$AUTH->is_auth()): ?>
    <?php get_block('modals/auth/login'); ?>
    <?php get_block('modals/auth/register'); ?>
    <?php get_block('modals/auth/forgot'); ?>
<?php endif; ?>

<div class="modal modal_error">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Ошибка</p>
    </div>
    <p class="sub_title modal_error_text"></p>

</div>

<!-------------------------------------->

<div class="modal modal_big modal_add_service modal_v2">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Добавить услугу</p>
    </div>
    <br>

    <form class="ajax_sender add_service" action_fn="add_service" style="padding: 0 10px;">

        <input id="new_service_spec" type="hidden" name="spec_id">

        <div class="field_wrap">
            <label for="service_name">Услуга</label>
            <input type="text" name="name" id="service_name" placeholder="Название услуги" class="field_full">
        </div>

        <div class="field_wrap">
            <label for="amount">Укажите цену</label>
            <input type="text" name="amount" id="amount" placeholder="например 1000" class="field_full">
        </div>

        <div class="field_wrap">
            <label>Стоимость</label>

            <div class="radio_input xs_one_line_radio">
                <div class="radio_item">
                    <input type="radio" id="pt1" name="payment_type" value="1" checked="checked">
                    <label for="pt1">от</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="pt2" name="payment_type" value="2">
                    <label for="pt2">фиксированная</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="pt3" name="payment_type" value="3">
                    <label for="pt3">по договоренности</label>
                </div>
            </div>

        </div>

        <div class="field_wrap">
            <label>Выберите один из пунктов</label>

            <div class="radio_input xs_two_line_radio">
                <div class="radio_item">
                    <input type="radio" id="at1" name="amount_type" value="1" checked="checked">
                    <label for="at1">За услугу</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="at2" name="amount_type" value="2">
                    <label for="at2">За час</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="at3" name="amount_type" value="3">
                    <label for="at3">За М²</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="at4" name="amount_type" value="4">
                    <label for="at4">За КГ</label>
                </div>
            </div>

        </div>


        <div class="row">
            <button class="button save_modal">Добавить</button>
        </div>

    </form>

</div>

<div class="modal modal_big modal_edit_service modal_v2">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Редактирование услуги</p>
    </div>
    <br>

    <form class="ajax_sender edit_service" action_fn="edit_service" style="padding: 0 10px;">

        <input type="hidden" id="edit_service_id" name="id" value="">

        <div class="field_wrap">
            <label for="edit_service_name">Услуга</label>
            <input type="text" name="name" id="edit_service_name" placeholder="Название услуги" class="field_full">
        </div>

        <div class="field_wrap">
            <label for="edit_amount">Укажите цену</label>
            <input type="text" name="amount" id="edit_amount" placeholder="например 1000" class="field_full">
        </div>


        <div class="field_wrap">
            <label>Стоимость</label>

            <div class="radio_input xs_one_line_radio">
                <div class="radio_item">
                    <input type="radio" id="edit_pt1" name="payment_type" role="edit" value="1" checked="checked">
                    <label for="edit_pt1">от</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="edit_pt2" name="payment_type" role="edit" value="2">
                    <label for="edit_pt2">фиксированная</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="edit_pt3" name="payment_type" role="edit" value="3">
                    <label for="edit_pt3">по договоренности</label>
                </div>
            </div>

        </div>

        <div class="field_wrap">
            <label>Выберите один из пунктов</label>

            <div class="radio_input xs_two_line_radio">
                <div class="radio_item">
                    <input type="radio" id="edit_at1" name="amount_type" role="edit" value="1" checked="checked">
                    <label for="edit_at1">За услугу</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="edit_at2" name="amount_type" role="edit" value="2">
                    <label for="edit_at2">За час</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="edit_at3" name="amount_type" role="edit" value="3">
                    <label for="edit_at3">За М²</label>
                </div>
                <div class="radio_item">
                    <input type="radio" id="edit_at4" name="amount_type" role="edit" value="4">
                    <label for="edit_at4">За КГ</label>
                </div>
            </div>
        </div>




        <div class="row">
            <button class="button save_modal">Сохранить</button>
        </div>

    </form>

</div>

<!-------------------------------------->

<div class="modal modal_big modal_add_work modal_v2">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Добавить выполненную работу</p>
    </div>
    <br>



    <form class="ajax_sender add_work" action_fn="add_work" style="padding: 0 10px;">

        <input id="new_work_spec" type="hidden" name="spec_id">

        <div class="field_wrap">
            <label for="work_name">Название</label>
            <input type="text" name="name" id="work_name" placeholder="Введите название проекта" class="field_full" required>
        </div>

        <div class="field_wrap">
            <label for="work_description">Описание</label>

            <textarea name="content" id="work_description" placeholder="Кратко в 2-3 предложения опишите суть проекта" class="field_full" required></textarea>
        </div>

        <div class="field_wrap">
            <label for="work_description">Фото проекта</label>

            <div class="gallery_upload_wrapper">

                <div class="add_work_image"><i class="icon icon-add"></i></div>

            </div>

        </div>

        <div class="field_wrap">
            <label for="work_price">Стоимость</label>
            <input type="text" name="price" id="work_price" placeholder="Введите стоимость проекта" class="field_full" required>
        </div>


        <br>
        <div class="row">
            <button class="button save_modal">Добавить</button>
        </div>

    </form>

</div>


<div class="modal modal_big modal_edit_work modal_v2" wrap_type="white_bg">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Редактировать выполненную работу</p>
    </div>
    <br>

    <form class="ajax_sender edit_work" action_fn="edit_work" style="padding: 0 10px;">

        <input id="edit_work_spec" type="hidden" name="spec_id">
        <input id="edit_work_id" type="hidden" name="id">

        <div class="field_wrap">
            <label for="work_name">Название</label>
            <input type="text" name="name" id="edit_work_name" placeholder="Введите название проекта" class="field_full" required>
        </div>

        <div class="field_wrap">
            <label for="work_description">Описание</label>

            <textarea name="content" id="edit_work_content" placeholder="Кратко в 2-3 предложения опишите суть проекта" class="field_full" required></textarea>
        </div>

        <div class="field_wrap">
            <label for="work_description">Фото проекта</label>

            <div class="gallery_upload_wrapper">

            </div>

        </div>

        <div class="field_wrap">
            <label for="work_price">Стоимость</label>
            <input type="text" name="price" id="edit_work_price" placeholder="Введите стоимость проекта" class="field_full" required>
        </div>


        <br>
        <div class="row">
            <button class="button save_modal">Сохранить</button>
        </div>

    </form>

</div>


<div class="modal modal_big modal_work_item" wrap_type="black_bg">
    <span class="modal_close">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
            <path d="M2 2.5L22 22.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
            <path d="M2 22.5L22 2.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </span>
    <div class="modal_header">
        <div class="title"></div>
        <div class="description"></div>
    </div>
    <div class="photos_wrapper"></div>
    <div class="content"></div>
</div>

<!-------------------------------------->

<div class="modal modal_big modal_add_company_doc" wrap_type="white_bg">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Добавить EГРИП</p>
    </div>
    <p class="description">Добавьте выписку из Единого государственного реестра индивидуальных предпринимателей</p>

    <br>

    <div class="add_doc" doc_type="ip">

        <div class="upload_company_doc">

            <a class="upload_doc_button"><i class="icon icon-screpka"></i>Загрузить выписку</a>
            <span>-загрузите файл</span>

        </div>

        <!--
        <div class="row">
            <button class="button save_modal">Добавить</button>
        </div>
        -->
    </div>

</div>

<!-------------------------------------->

<div class="modal modal_big modal_review modal_add_review">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Оцените мастера</p>
    </div>
    <div class="avatar">
        <img src="<?=URL?>uploads/avatars/no_avatar.png">
    </div>

    <p class="name">Иванов Иван</p>

    <form class="add_review">
        <input type="hidden" name="spec_id" value="">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="to_whom_user" value="">
        <input type="hidden" name="this_user_id" value="">
        <input type="hidden" name="user_id" value="">
        <input type="hidden" name="dialog_id" value="">


        <div class="field_wrap">
            <label for="add_review_content">Отзыв</label>
            <textarea class="field_full" id="add_review_content" name="content" placeholder="Кратко опишите результаты взаимодействия с мастером"></textarea>
        </div>

        <div class="rating_input">
            <input type="radio" name="star" class="star-1" id="star-1" value="1"/>
            <label class="star-1" for="star-1">1</label>
            <input type="radio" name="star" class="star-2" id="star-2" value="2"/>
            <label class="star-2" for="star-2">2</label>
            <input type="radio" name="star" class="star-3" id="star-3" value="3"/>
            <label class="star-3" for="star-3">3</label>
            <input type="radio" name="star" class="star-4" id="star-4" value="4"/>
            <label class="star-4" for="star-4">4</label>
            <input type="radio" name="star" class="star-5" id="star-5" value="5"/>
            <label class="star-5" for="star-5">5</label>
            <span></span>
        </div>

        <div class="row">
            <button class="button save_modal">Отправить</button>
        </div>

    </form>

</div>

<div class="modal modal_review modal_add_review_answer modal_v2">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Ответить на отзыв</p>
    </div>
    <div class="avatar">
        <img src="<?=URL?>uploads/avatars/no_avatar.png">
    </div>

    <p class="name"></p>

    <form class="add_review_answer">

        <input type="hidden" name="id" value="">

        <div class="field_wrap">
            <label for="add_review_content">Ответ</label>
            <textarea class="field_full" id="add_review_answer" name="answer" placeholder="Кратко опишите свой ответ на отзыв"></textarea>
        </div>

        <div class="row">
            <button class="button save_modal">Ответить</button>
        </div>

    </form>

</div>

<div class="modal modal_review modal_add_review_complaint modal_v2">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Пожаловаться на отзыв</p>
    </div>
    <div class="avatar">
        <img src="<?=URL?>uploads/avatars/no_avatar.png">
    </div>

    <p class="name">Иванов Иван</p>

    <form class="add_review_complaint">

        <input type="hidden" name="id" value="">

        <div class="field_wrap">
            <label for="add_review_content">Жалоба</label>
            <textarea class="field_full" id="add_review_complaint" name="complaint" placeholder="Кратко опишите суть жалобы, мы рассмотрим и примем соответственное решение"></textarea>
        </div>

        <div class="row">
            <button class="button save_modal">Пожаловаться</button>
        </div>

    </form>

</div>

<!-------------------------------------->


<div class="modal modal_add_spec">
    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Добавить специальность</p>
    </div>
    <br>
        <div class="field_wrap finder_modal finder_select">
            <label for="add_new_spec">Напишите название специальности</label>
            <div class="micro_form find_field_wrap">
                <input class="find_by_id" type="hidden" name="find">
                <input type="text" id="add_new_spec" class="find_field" placeholder="Например, Плотник" autocomplete="off">
            </div>
            <ul class="find_field_dropdown"></ul>
        </div>
        <div class="row">
            <button class="button save_modal add_to_my_spec">Добавить</button>
        </div>
</div>

<!-------------------------------------->

<div class="modal modal_delete_profile">
    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>
        <p class="title">Удаление профиля</p>
    </div>
    <br>
    <div class="text-center">
        Внимание, после продолжения Вы потеряете доступ к своему аккаунту.
    </div>
    <br>
    <div class="row button-row">
        <form action="/dashboard/delete" method="POST">
            <button type="submit" class="button save_modal">Удалить</button>
        </form>
        <button type="button" class="button save_modal modal_close">Отмена</button>
    </div>
</div>

<!-------------------------------------->

<div class="modal modal_big modal_confirm_work_with_master">

    <div class="modal_header">
        <div class="modal_close_wrapper">
        <span class="modal_close">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.21807 1.21807C1.50882 0.927311 1.98023 0.927311 2.27098 1.21807L7 5.94708L11.729 1.21807C12.0198 0.927311 12.4912 0.927311 12.7819 1.21807C13.0727 1.50882 13.0727 1.98023 12.7819 2.27098L8.05292 7L12.7819 11.729C13.0727 12.0198 13.0727 12.4912 12.7819 12.7819C12.4912 13.0727 12.0198 13.0727 11.729 12.7819L7 8.05292L2.27098 12.7819C1.98023 13.0727 1.50882 13.0727 1.21807 12.7819C0.927311 12.4912 0.927311 12.0198 1.21807 11.729L5.94708 7L1.21807 2.27098C0.927311 1.98023 0.927311 1.50882 1.21807 1.21807Z" fill="#252525" stroke="#252525" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        </div>

        <p class="title">Вы начали работать с мастером</p>
    </div>

    <p class="description" align="center">Вы создали новый заказ, через 24 часа вы сможете оставить отзыв <br>о работе с мастером</p>




    <div class="modal_confirm_work_with_master_controls">

        <a class="button save_modal" href="<?= URL?>history/">Перейти в заказы</a>
        <p class="unwork">Отменить заказ</p>

    </div>




</div>
