$(function (){
    //auth
    {
        $('.phonemask').inputmask('+7(999)999-99-99', {
            showMaskOnHover: false,
        })

        $('.get_modal').click(function (e) {
            e.preventDefault()

            handleOpenModal(
                $(this).attr('modal'),
                $(this).attr('wrap_type'),
                $(this).attr('data-role-as')
            );

            return false
        })

        $('.modal_wrapper').click(function () {
            if (!$(this).hasClass('white_bg')) {
                $('.modal').fadeOut()
                $('.modal_wrapper').fadeOut()
            }
        })

        $('.modal_close').click(function () {
            $(this).closest('.modal').fadeOut()
            $('.modal_wrapper').fadeOut()
        })

        $('.modal[data-role-as] .role-toggle .toggle-button').on('click', function (e) {
            var $button = $(e.currentTarget)

            toggleModalAuthRole($button.closest('.modal[data-role-as]'), $button.attr('data-role'))
        })
    }

    //profile
    {

        $('.select_city').select2({
            maximumSelectionSize: 1,
            ajax: {
                url: window.URL+'cityes/list/',
                method: 'POST',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        chars: params.term
                    }
                    console.log(window.URL+'cityes/list/');
                    console.log(query);
                    return query;
                },
            },
            "language": {
                "noResults": function(){
                    return "Начните вводить город";
                },
                searching: function() {
                    return 'Поиск...';
                }
            },

        });

        $('.select_city_finder').select2({
		placeholder: "Краснодар",
            maximumSelectionSize: 1,
            ajax: {
                url: window.URL+'cityes/list/',
                method: 'POST',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        chars: params.term
                    }
                    console.log(window.URL+'cityes/list/');
                    console.log(query);
                    return query;
                },
            },
            "language": {
                "noResults": function(){
                    return "Начните вводить город";
                },
                searching: function() {
                    return 'Поиск...';
                }
            },

        });

        $(document).on('change', '.select_city_finder', function (){
            $.cookie('city_finder', $(this).val(),{ expires: 30, path: '/' });
            $.cookie('city_finder_for_search', true,{ expires: 30, path: '/' });
            location.reload();
        });


        $('.select_experience').select2({
            minimumResultsForSearch: -1,
            dropdownCssClass : "select_experience_dropdown"
        });






        $(document).on('submit', '.update_profile', function (e){
            e.stopPropagation();
            e.preventDefault();

            var data = pack_data($(this));

            if(typeof all_my_spec !== 'undefined'){
                data['spec'] = all_my_spec;
            }

            console.log(data);

            ajax_send_base(data, 'dashboard', function (res){

                //all_my_spec = {};
                $('#edit_pass').val('');
                $('#edit_pass_confirm').val('');

                if(res.status === 'ok'){
                    $('.field.error').removeClass('error');
                    create_informer('Обновление профиля','Профиль успешно обнавлен', 'success', 3000);
                } else if(res.status === 'error'){
                    if(res.code === 135){
                        Object.keys(res.data).forEach(key => {
                            var id = key === 'pass' ? 'edit_pass' : key;

                            $('#' + id).addClass('error');

                            if (res.data[key].confirmed || id === 'edit_pass') {
                                $('#' + id + '_confirm').addClass('error');
                            }
                        });
                    }

                    create_informer('Ошибка обновления профиля', res.message, res.status, 3000);
                }

            })

            return false;
        });

        $(document).on('submit', '.update_custom_data', function (e){
            e.stopPropagation();
            e.preventDefault();

            var data = pack_data($(this));

            console.log(data);

            ajax_send_base(data, 'contacts', function (data){

                if(data.status === 'ok'){
                    create_informer('Обновление профиля','Профиль успешно обнавлен', 'success', 3000);
                }

                if(data.status === 'error'){
                    create_informer('Ошибка' ,data.message, data.status, 0);
                }

            })




            return false;
        })

        $(document).on('submit', '.update_settings', function (e){
            e.stopPropagation();
            e.preventDefault();

            var data = pack_data($(this));

            console.log(data);

            ajax_send_base(data, 'settings', function (data){
                console.log(data);
                if(data.status === 'ok'){
                    create_informer('Обновление профиля','Профиль успешно обнавлен', 'success', 3000);
                }

                if(data.status === 'error'){
                    create_informer('Ошибка' ,data.message, data.status, 0);
                }

            })

            return false;
        })

        $(document).on('submit', '.save_prices_info', function (e){
            e.stopPropagation();
            e.preventDefault();

            var form = $(this);

            var data = pack_data($(this));

            console.log(data);

            ajax_send_base(data, 'prices', function (data){

                if(data.status === 'ok'){
                    create_informer('Обновление данных','Средняя цена успешно обновлена', 'success', 3000);
                }

                if(data.status === 'error'){

                    if(data.code === 135){
                        $(form).find('input[name=price]').addClass('error');
                        create_informer('Ошибка поля ввода' ,'Средняя цена должна быть числом', data.status, 3000);
                    }

                    if(data.code === 446){
                        create_informer('Критическая ошибка' ,data.message, data.status, 3000);
                    }

                }

            })

            return false;
        })


    }


    $(function () {
        $(document).on('click', '.close_informer', function () {
            var parent = $(this).parent().parent();
            parent.animate({left:'-500px'},300);

            setTimeout(function (){

                parent.animate({height:'0px', padding:'0px', margin:'0px'},300);
                setTimeout(function (){
                    parent.remove();
                }, 300);

            }, 300);

        })
    });

});


function create_informer(title, content, type, time = 0, custom_class = ''){

    var random_class_name = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
    var random_class = '.'+random_class_name;

    var notify = '<div class="notification_item '+custom_class+' '+random_class_name+'">' +
                 '    <p class="title '+type+'">' +
                 '       '+title+
                 '        <i class="close_informer icon icon-cancel"></i>' +
                 '    </p>' +
                 '    <div class="content">' +
                 '       '+content+
                 '    </div>' +
                 '</div>';

    $('.notification_wrap').append(notify);

    if(time > 0){
        setTimeout(function (){
            $(random_class).animate({left:'-500px'},300);
            setTimeout(function (){
                $(random_class).animate({height:'0px', padding:'0px', margin:'0px'},300);
                setTimeout(function (){
                    $(random_class).remove();
                }, 300);
            }, 300);
        }, time);
    }

}


function pack_data(form){

    var formdata = $(form).serializeArray();
    var data = {};
    $(formdata).each(function(index, obj){
        data[obj.name] = obj.value;
    });

    return data;

}

$(document).on('submit', '.ajax_sender', function (e){
    e.stopPropagation();
    e.preventDefault();

    var action = $(this).attr('action_fn');
    var formdata = $(this).serializeArray();
    var data = {};
    $(formdata).each(function(index, obj){
        data[obj.name] = obj.value;
    });

    window[action](data);
    return false;
})

$('.finder_select .find_field').on('input',function (){

    var chars = $.trim($(this).val());

    var selector_wrap = $(this).parent().parent().find('.find_field_dropdown');

    if(chars.length > 0){

        var data = 'chars='+$(this).val();

        ajax_send(data, 'find', function (res){
            if(res.status === 'ok'){

                if(res.data !== undefined){

                    selector_wrap.html('');
                    $.each(res.data, function (index, value){
                        selector_wrap.append('<li class="find_item" find-id="'+value.id+'">'+value.name+'</li>')
                    });

                    selector_wrap.show();
                }else{
                    selector_wrap.html('');
                    selector_wrap.hide();
                }

                //window.location.href = window.URL+'dashboard';
            }else{
                selector_wrap.html('');
                selector_wrap.hide();
            }
        });

    }else{
        selector_wrap.html('');
        selector_wrap.hide();
    }

});

$(document).on('click', '.find_item', function (){

    $(this).parent().hide();

    var text = $(this).text();
    var id = $(this).attr('find-id')

    $('.find_by_id').attr('value', id);
    $('.find_field').val(text);
    $('.finder_form').trigger('submit');

    var form = $(this).closest('form.search_form_form.finder_form')

    if (form.length && form.attr('action')) {
        window.location.href = form.attr('action') + '/' + id + '/';
    }
});

function toggleModalAuthRole($modal, role) {
    $modal.attr('data-role-as', role)
    $modal.find('input[name="role"]').val(role)
    $modal.find('.get_modal[data-role-as]').attr('data-role-as', role)
}

var smsReSendTimer = null;

function initSmsDelay($container) {
    clearInterval(smsReSendTimer);

    var delay = +$container.attr('data-delay');
    var $delay = $container.find('.delay');
    var $resend = $container.find('.send-sms');
    var $seconds = $delay.find('.seconds');

    $resend.removeClass('active');
    $delay.removeClass('active');

    if (delay > 0) {
        $seconds.html(delay);
        $delay.addClass('active');
    } else {
        $resend.addClass('active');
    }

    smsReSendTimer = setInterval(function () {
        delay--;

        if (delay > 0) {
            $seconds.html(delay);
        } else {
            clearInterval(smsReSendTimer);
            $delay.removeClass('active');
            $resend.addClass('active');
        }
    }, 1000);
}

function handleOpenModal(target, wrap_type, role_as) {
    $('.modal').fadeOut()

    var $modal = $('.' + target)

    if (!$modal.length) {
        return
    }

    if (['modal_confirm_code', 'modal_forgot_confirm_code'].includes(target)) {
        initSmsDelay($modal.find('.sms-status'));
    }

    if ($modal.hasClass('modal_v2')) {
        wrap_type = 'black_bg'
    }

    if ($modal.hasClass('modal_auth') && role_as) {
        toggleModalAuthRole($modal, role_as)
    }

    $modal.fadeIn()

    if (wrap_type !== undefined) {
        $('.modal_wrapper.' + wrap_type).fadeIn()
    } else {
        $('.modal_wrapper.std').fadeIn()
    }
}

function open_modal(target_modal, role_as) {
    var wrap_type = $('.' + target_modal).attr('wrap_type')

    handleOpenModal(target_modal, wrap_type, role_as);
}

function close_all_modals(fast = false, callback = null){



    if(fast){
        $(".modal").hide();
    }else{
        $(".modal").fadeOut();
    }

    $(".modal_wrapper").fadeOut();

    if(callback !== null){
        callback();
    }

}

$(function($) {
    $('.sms-status .send-sms').on('click', function(e) {
        var $self = $(e.currentTarget);
        var $component = $self.closest('.sms-status');
        var $form = $component.closest('form');

        $self.removeClass('active');

        ajax_send({
            phone: $form.find('input[name="phone"]').val(),
            sms_hash: $form.find('input[name="sms_hash"]').val(),
        }, 'auth/sms/resend', function (res) {
            if (res.status === 'ok') {
                $component.attr('data-delay', res.data['sms_delay']);
                $form.find('input[name="code"]').val(res.data['sms_debug'] ? res.data['sms_debug'] : '');
                initSmsDelay($component);
            }
        });
    });
});

$(document).on('change', 'input.error', function (){
    $(this).removeClass('error');
});

function login_form(data) {

    var $loginErrorContainer = $('.modal_login .login-error')

    $loginErrorContainer.css('visibility', 'hidden')

    var exp = data.remember_me === 'on' ? 365 : 1

    ajax_send(data, 'auth/login', function (res) {
        if (res.status === 'ok') {
            $.cookie('jwt', res.data.jwt_data.jwt, {expires: exp, path: '/'})
            $.cookie('udata', JSON.stringify(res.data.jwt_data.udata), {expires: exp, path: '/'})

            window.location.href = window.URL + 'dashboard'
        } else {

            if (res.code === 170 || res.code === 135) {
                $('#login_form_phone').addClass('error')
                $('#login_form_pass').addClass('error')

                $loginErrorContainer.css('visibility', 'visible')

                return false
            }

            create_informer('Ошибка авторизации', res.message, res.status, 3000)
            clear_form_add_service()
            close_all_modals()
            return false
        }
    })

}

window.temp_exp = 1;

function reg_form(data) {

    if(data.remember_me === 'on'){
        window.temp_exp = 365;
    }

    ajax_send(data, 'auth/registration/check', function (res) {
        if (res.status === 'ok') {
            $('#sms_hash').val(res.data['sms_hash']);
            $('#sms_code').val(res.data['sms_debug'] ? res.data['sms_debug'] : '')

            $('.modal_confirm_code .confirm_phone').html(data.phone);
            $('.modal_confirm_code input[name="phone"]').val(data.phone);
            $('.modal_confirm_code .sms-status').attr('data-delay', res.data['sms_delay']);

            open_modal('modal_confirm_code', data.role);

            return;
        }

        if (res.code === 171) {
            $('#reg_form_phone').addClass('error');

            create_informer('Ошибка регистрации', res.message, res.status, 3000);
            return;
        }

        if (res.code === 135) {
            var keys = Object.keys(res.data);

            for (const key of keys) {
                $('#reg_form_' + key).addClass('error');
            }

            return;
        }

        create_informer('Ошибка регистрации', res.message, res.status, 3000);
    })
}

function clear_reg_form(){
    $('#reg_form_name').val('').removeClass('error');
    $('#reg_form_mail').val('').removeClass('error');
    $('#reg_form_phone').val('').removeClass('error');
    $('#reg_form_pass').val('').removeClass('error');
}

function reg_sms_confirm(data) {
    ajax_send(data, 'auth/registration/confirm', function (res) {
        if (res.status === 'ok') {
            $('.modal_register_finishing input[name="phone"]').val(data.phone);
            $('.modal_register_finishing input[name="sms_hash"]').val(data.sms_hash);

            open_modal('modal_register_finishing', data.role);

            return;

        }

        $('#sms_code').addClass('error')
    })
}

function reg_finish(data) {
    ajax_send(data, 'auth/registration', function (res) {
        if (res.status === 'ok') {
            $.cookie('jwt', res.data.jwt_data.jwt, {expires: window.temp_exp, path: '/'});
            $.cookie('udata', JSON.stringify(res.data.jwt_data.udata), {expires: window.temp_exp, path: '/'});

            open_modal('modal_register_success', data.role);

            return;
        }

        if (res.code === 135) {
            var keys = Object.keys(res.data);

            for (const key of keys) {
                $('#reg_form_' + key).addClass('error');
            }

            return;
        }
    })
}

function forgot(data) {
    ajax_send(data, 'auth/forgot', function (res) {
        if (res.status === 'ok') {
            $('#forgot_sms_hash').val(res.data['sms_hash']);
            $('#forgot_sms_code').val(res.data['sms_debug'] ? res.data['sms_debug'] : '');

            $('.modal_forgot_confirm_code .confirm_phone').html(data.phone);
            $('.modal_forgot_confirm_code input[name="phone"]').val(data.phone);
            $('.modal_forgot_confirm_code .sms-status').attr('data-delay', res.data['sms_delay']);

            open_modal('modal_forgot_confirm_code')

            return false
        }

        $('#forgot_form_phone').addClass('error');
    })
}

function forgot_sms_confirm(data) {
    ajax_send(data, 'auth/forgot/confirm', function (res) {
        if (res.status === 'ok') {
            $('.modal_forgot_change_password input[name="phone"]').val(data.phone);
            $('.modal_forgot_change_password input[name="sms_hash"]').val(data.sms_hash);

            open_modal('modal_forgot_change_password');

            return;
        }

        $('#forgot_sms_code').addClass('error')
    })
}

function forgot_change_password(data) {

    ajax_send(data, 'auth/forgot/change', function (res) {
        if (res.status === 'ok') {
            $.cookie('jwt', res.data.jwt_data.jwt, {expires: 7, path: '/'})
            $.cookie('udata', JSON.stringify(res.data.jwt_data.udata), {expires: 7, path: '/'})

            open_modal('modal_forgot_success')
            return;
        }

        if (res.code === 135) {
            var keys = Object.keys(res.data);

            for (const key of keys) {
                $('#forgot_' + key).addClass('error');
            }

            return;
        }

        if (res.code === 170) {
            $('#forgot_pass').addClass('error');
            $('#forgot_pass_confirm').addClass('error');

            return;
        }
    })
}

function ajax_send(data, path, success){

    $.ajax({
        type: 'POST',
        method : "POST",
        url: window.URL+path+'/',
        data: data,
        dataType: 'json',
        success: function (res){
            if (success && typeof success === 'function') {
                success(res);
            }
        },
        error: function (res){
            console.log(res.responseText)
        }

    });

}

async function ajax_send_base(data, path, success){
    $.ajax({
        type: 'POST',
        method : "POST",
        url: window.URL+path+'/',
        data: data,
        dataType: 'json',
        success: function (res){
            if(success){
                success(res);
            }
        },
        error: function (res){

            console.log(res.responseText)

            create_informer('Неизвестная Ошибка','Что-то пошло не так', 'error', 3000);

        }

    });

}

//tabs

$('.select_tab').click(function (){

    var tab_name = $(this).attr('tab');
    var role = $(this).attr('role');

    $(this).parent().find('.select_tab[role="'+role+'"]').removeClass('active');
    $(this).addClass('active');

    $(this).parent().parent().find('.tab_content[role="'+role+'"]').removeClass('active');
    $(this).parent().parent().find('.tab_content[tab="'+tab_name+'"]').addClass('active');

});


$(function (){
    $(document).on('click', '.close_tab', function (){
        var tab_name = $(this).parent().parent().attr('tab');
        var role = $(this).parent().parent().attr('role');

        $(this).parent().parent().removeClass('active');
        $(this).parent().parent().parent().parent().find('.select_tab[tab="'+tab_name+'"]').removeClass('active');

    });
    $(document).on('click', '.close_lk_menu', function (){
        $('.lk_menu .has_child').removeClass('active');
        $('.lk_menu .has_child').next().hide();
    });
});



//SPEC selector
$('.spec_select .spec_field').on('input',function (){

    var chars = $.trim($(this).val());

    var selector_wrap = $(this).parent().parent().find('.find_field_dropdown');

    if(chars.length > 0){

        var data = 'chars='+$(this).val();

        ajax_send(data, 'find', function (res){
            console.log(res);
            if(res.status === 'ok'){

                if(res.data !== undefined){

                    selector_wrap.html('');
                    $.each(res.data, function (index, value){
                        selector_wrap.append('<li class="spec_find_item" find-id="'+value.id+'">'+value.name+'</li>')
                    });

                    selector_wrap.show();
                }else{
                    selector_wrap.html('');
                    selector_wrap.hide();
                }

                //window.location.href = window.URL+'dashboard';
            }else{
                selector_wrap.html('');
                selector_wrap.hide();
            }
        });

    }else{
        selector_wrap.html('');
        selector_wrap.hide();
    }

});

$(document).on('click', '.spec_find_item', function (){

    $(this).parent().hide();

    var text = $(this).text();
    var id = $(this).attr('find-id');

    $('.add_by_id').attr('value', id);
    $('.find_field').val(text);

});

$('.add_to_my_spec').click(function (){

    var spec_id = $('.find_by_id').val();
    var spec_name = $('.find_field').val();

    var style = '';

    if(spec_id === ''){
        style = 'red';
        create_informer('Такой специализации нет', 'В течении 24 часов, мы добавим ее в список, и вы сможете ее выбрать', 'error', 12000);



        //send to create

        var data = {};
        data.user_id = window.UID;
        data.spec_name = spec_name;
        ajax_send_base(data, 'users/add_spec');

    }

    if(all_my_spec[spec_id] === undefined){
        all_my_spec[spec_id] = spec_name;
        $('.tags_spec').append('<div class="tag_item '+style+'" spec_id="'+spec_id+'">' +
            '    <span>'+spec_name+'</span>' +
            '    <i class="icon icon-cancel delete_spec_item"></i>' +
            '</div>');
    }

    close_all_modals();

});

//todo Maybe delete?
$('.add_spec').click(function (){
    var spec = $('#spec_temp').val();
    if(spec.length > 0){

        all_my_spec.push(spec);
        //$('#all_spec').attr('value', JSON.stringify(all_spec_field));
        $('#spec_temp').val('');

        $('.tags_spec').append('<div class="tag_item">' +
            '    <span>'+spec+'</span>' +
            '    <i class="icon icon-cancel delete_spec_item"></i>' +
            '</div>');

        console.log(all_my_spec);

    }
});

$(document).on('click', '.delete_spec_item', function (){

    var item = $(this).parent();

    var spec_id = $(item).attr('spec_id');
    var spec = $(this).parent().find('span').text();



    //all_my_spec = all_my_spec.filter(e => e !== spec)

    delete all_my_spec[spec_id];
    item.remove();
})

$('.view_all_review_cont').click(function (){

    var answer = $(this).parent().find('.answer_cont');
    $(this).toggleClass('view');

    if($(this).hasClass('view')){
        $(this).html('<i class="icon icon-review_all"></i>Скрыть ответ');
        answer.slideDown();
    }else{
        $(this).html('<i class="icon icon-review_all"></i>Посмотреть ответ');
        answer.slideUp();
    }

});

$(document).on('click', '.view_all_review_cont_in_lk', function (){
    var answer = $(this).parent().parent().find('.answer_cont');
    $(this).toggleClass('view');

    if($(this).hasClass('view')){
        $(this).html('<i class="icon icon-review_all"></i>Скрыть ответ');
        answer.slideDown();
    }else{
        $(this).html('<i class="icon icon-review_all"></i>Посмотреть ответ');
        answer.slideUp();
    }
})

$('.set_spec').click(function (){
    var spec_id = $(this).attr('spec')
    $('#new_service_spec').attr('value', spec_id);
    $('#new_work_spec').attr('value', spec_id);
});

$(document).on('click', '.edit_service_trigger', function (){

    var data = {};
    data.id = $(this).attr('service');

    ajax_send(data, 'service/single', function (res){
        console.log(res);

        if(res.status === 'ok'){
            //res.data
            console.log(res.data);

            $('#edit_service_id').val(res.data.id);
            $('#edit_service_name').val(res.data.name);
            $('#edit_amount').val(parseInt(res.data.amount));

            $('input:radio[name="payment_type"][role="edit"]').attr('checked', false).filter('[value='+res.data.payment_type+']').attr('checked', true);
            $('input:radio[name="amount_type"][role="edit"]').attr('checked', false).filter('[value='+res.data.amount_type+']').attr('checked', true);

            open_modal('modal_edit_service');

        }else{
            close_all_modals();
        }


    });

})

$(document).on('click', '.delete_service_trigger', function () {
    var $self = $(this);
    var $parent = $self.closest('.price_item');
    var $container = $parent.closest('.price_table')

    ajax_send({ id: $self.attr('service') }, 'service/delete', function (res) {
        if (res.status === 'ok') {
            $parent.remove();

            if (!$container.children().length) {
                $container.replaceWith('<h4 class="service-empty-message">Нет ни одной услуги</h4>');
            }
        }
    });
})

function add_service(data){

    console.log(data);

    ajax_send(data, 'service/add', function (res){
        if(res.status === 'ok'){

            console.log(res);

            create_informer('Добавление услуги','Услуга успешно добавлена','success',3000);
            clear_form_add_service();
            close_all_modals();

            if (!$('.price_table').length) {
                $('.service-empty-message').replaceWith('<div class="price_table"></div>')
            }

            $('.price_table').append(`<div class="price_item" price_item="${res.data.id}">
                                <span class="name">${res.data.name}</span>
                                <span class="price">${res.data.correct_price}</span>
                                <span class="edit_service edit_service_trigger" service="${res.data.id}"><i class="icon icon-edit"></i></span>
                                <span class="edit_service delete_service delete_service_trigger" service="${res.data.id}"><i class="icon icon-cancel"></i></span>
                            </div>`)


            return false;
        }



        if(res.status === 'error'){

            if(res.code === 135){
                //const iterator = res.data.keys();

                var keys = Object.keys(res.data)

                for (const key of keys) {
                    $('.modal_add_service [name='+key+']').addClass('error');
                }
                return false;
            }

            create_informer('Ошибка Добавление услуги',res.message,res.status,3000);
            clear_form_add_service();
            close_all_modals();


            //$()


            return false;
        }
    });

}

function clear_form_add_service(){
    $('#service_name').val('').removeClass('error');
    $('#amount').val('').removeClass('error');
    $('label[for=pt1]').trigger('click');
    $('label[for=at1]').trigger('click');
}

function clear_form_edit_service(){
    $('#edit_service_name').val('').removeClass('error');
    $('#edit_amount').val('').removeClass('error');
    $('label[for=edit_pt1]').trigger('click');
    $('label[for=edit_at1]').trigger('click');
}

function edit_service(data){

    console.log(data);

    ajax_send(data, 'service/edit', function (res){
        console.log(res);
        if(res.status === 'ok'){

            var price_item = $('[price_item='+data.id+']');

            price_item.find('.name').text(data.name);
            price_item.find('.price').text(res.data.amount_text);
            close_all_modals();
            clear_form_edit_service();
        }

        if(res.status === 'error'){

            if(res.code === 135){
                //const iterator = res.data.keys();

                var keys = Object.keys(res.data)

                for (const key of keys) {
                    $('.modal_edit_service [name='+key+']').addClass('error');
                }
                return false;
            }

            create_informer('Ошибка Добавление услуги',res.message,res.status,3000);
            clear_form_edit_service();
            close_all_modals();
            return false;
        }




    });

}

///////////////////////////////

var work_medias = [];

$(document).on('click', '.add_new_work', function (){
    work_medias = [];
    open_modal($(this).attr('modal'));
});


$(document).on('click', '.delete_work_trigger', function () {
    var $self = $(this);
    var $parent = $self.closest('.work_item');
    var $container = $parent.closest('.work_items')

    ajax_send({ id: $self.attr('work') }, 'finished/delete', function (res) {
        if (res.status === 'ok') {
            $parent.remove();

            if (!$container.children().length) {
                $container.replaceWith('<h4 class="work-empty-message">Нет ни одной услуги</h4>');
            }
        }
    });
})


function add_work(data) {

    data.medias = work_medias;

    if(data.medias.length === 0){
        data.medias = {};
    }

    ajax_send(data, 'finished', function (res){
        console.log(res);
        if(res.status === 'ok'){

            var image = window.URL+'uploads/std/no-photo-160x130.png';

            if(data.medias.length !== undefined){
                image = window.URL+'uploads/works/'+work_medias[0].name+'-160x130.png';
            }

            if (!$('.tab_content.active[role="work_tabs"] .work_items').length) {
                $('.service_tabs_content_wrapper').html('<div class="tab_content active" role="work_tabs" tab="spec_'+res.data.spec_id + '"><div class="work_items"></div></div>')
            }

            $('.tab_content.active[role="work_tabs"] .work_items').prepend('<div class="work_item set_edit_work_item" work_id="' + res.data.id + '" >\n' +
                '                                <div class="img">\n' +
                '                                    <img src="'+image+'">\n' +
                '                                </div>\n' +
                '                                <div class="information">\n' +
                '                                    <div class="title_wrap">\n' +
                '                                        <span class="title">'+res.data.name+'</span>\n' +
                '                                        <span class="price">'+res.data.price+' ₽</span>\n' +
                '                                    </div>\n' +
                '                                    <div class="content">'+res.data.content+'</div>\n' +
                '                                </div>' +
                '                                <span class="delete_work delete_work_trigger text-button" work="' + res.data.id + '">\n' +
                '                                    <i class="icon icon-delete"></i><span>Удалить</span>\n' +
                '                                </span>\n' +
                '                            </div>');

            close_all_modals(true, function (){
                work_medias = [];
                $('.gallery_upload_wrapper').find('.uploaded_item').remove()

                $('#work_name').val('');
                $('#work_description').val('');
                $('#work_price').val('');
            });



        }else{
            //$('.modal_error_text').text(res.message);
            //open_modal('modal_error');
        }
    });

}

function edit_work(data) {
    console.log(data);
    data.medias = work_medias;

    console.log('EDIT');
    console.log(work_medias);

    console.log(data.medias);

    if(data.medias.length === 0){
        data.medias = {};
    }


    ajax_send(data, 'works/'+data.id+'/save', function (res){
        console.log('------------------');
        console.log(res);

        if(res.status === 'ok'){

            var image = window.URL+'uploads/std/no-photo-160x130.png';

            if(data.medias.length !== undefined){
                image = window.URL+'uploads/works/'+work_medias[0].name+'-160x130.png';
            }


            $('.tab_content.active[role="work_tabs"] .work_items').find('.work_item[work_id='+data.id+']').html('<div class="img">' +
                '                                    <img src="'+image+'">' +
                '                                </div>' +
                '                                <div class="information">' +
                '                                    <div class="title_wrap">' +
                '                                        <span class="title">'+res.data.name+'</span>' +
                '                                        <span class="price">'+res.data.price+' ₽</span>' +
                '                                    </div>' +
                '                                    <div class="content">'+res.data.content+'</div>' +
                '                                </div>');


            close_all_modals(true, function (){
                work_medias = [];
                $('.gallery_upload_wrapper').find('.uploaded_item').remove()

                $('#work_name').val('');
                $('#work_description').val('');
                $('#work_price').val('');
            });



        }else{
            //$('.modal_error_text').text(res.message);
            //open_modal('modal_error');
        }
    });





}



$(document).on('click', '.add_work_image', function (){
    $('#media_upload_path').attr('value', 'works');
    $('#media_upload_sizes').attr('value', ['600x488', '160x130', '92x92']);
    $('#media_upload_cb').attr('value', 'work_uploaded_success');
    $('#media_upload_field').trigger('click');
});

function work_uploaded_success(data){
    console.log(data);
    //work_medias.push(data);

    var index = work_medias.push(data) - 1;


    //$('.tab_content.active[role="work_tabs"]')

    $('.add_work_image').before(
        '<div class="uploaded_item"><img src="'+window.URL+'uploads/'+data.path+data.name+'-92x92.'+data.type+'">' +
        '<span class="work_delete_img" index="'+index+'"><i class="icon icon-add"></i></span></div>'
    );

}


function add_old_uploaded_images(data){

    var index = work_medias.push(data) - 1;

    $('.modal_edit_work .add_work_image').before(
        '<div class="uploaded_item"><img src="'+window.URL+'uploads/'+medias.path+data.name+'-92x92.'+data.type+'">' +
        '<span class="work_delete_img" index="'+index+'"><i class="icon icon-add"></i></span></div>'
    );

}
$(document).on('click', '.work_delete_img', function (){
    var index = $(this).attr('index');
    work_medias.splice(index, 1);
    $(this).parent().remove();
});

$(document).on('click', '.add_doc', function (){

    $('#doc_upload_type').attr('value', $(this).attr('doc_type'));

    $('#doc_upload_path').attr('value', 'docs');
    $('#doc_upload_cb').attr('value', 'docs_uploaded_success');
    $('#doc_upload_field').trigger('click');
});

function docs_uploaded_success(data){
    console.log(data);
    $('#'+data.check_field).prop('checked', true);
}

$('.add_doc_but').click(function (){
    var doc_type = $(this).attr('doc_type');

    $('.modal_add_company_doc .add_doc').attr('doc_type', doc_type);

    if(doc_type === 'ip'){
        $('.modal_add_company_doc .title').text('Добавить EГРИП');
        $('.modal_add_company_doc .description').text('Добавьте выписку из Единого государственного реестра индивидуальных предпринимателей');
    }
    if(doc_type === 'ooo'){
        $('.modal_add_company_doc .title').text('Добавить EГРЮЛ');
        $('.modal_add_company_doc .description').text('Добавьте выписку из Единого государственного реестра юридических лиц');
    }

});

$('#media_upload_field').on("change", function(){
    upload_media(
        $(this).prop('files')[0],
        $('#media_upload_path').attr('value'),
        $('#media_upload_sizes').attr('value'),
        $('#media_upload_cb').attr('value'),
    );
});

$('#doc_upload_field').on("change", function(){
    upload_doc(
        $(this).prop('files')[0],
        $('#doc_upload_type').attr('value'),
        $('#doc_upload_path').attr('value'),
        $('#doc_upload_cb').attr('value'),
    );
});

function upload_doc(media, path, doc_type, fn_name){

    var formdata = new FormData();

    if (!!media.type.match(/application.*/)) {
        formdata.append('file', media);
        formdata.append('path', path);
        formdata.append('doc_type', doc_type);

        $.ajax({
            url: window.URL+'/doc_upload.php',
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function (res){
                data = JSON.parse(res)
                if(data.status === 'ok'){
                    window[fn_name](data.data);
                }

            },
            error: function (res){
                console.log(res.responseText)
            }

        });

    }


}

function upload_media(media, path, sizes, fn_name){

    var formdata = new FormData();

    if (!!media.type.match(/image.*/)) {
        formdata.append('file', media);
        formdata.append('path', path);
        formdata.append('sizes', sizes);

        $.ajax({
            url: window.URL+'/media_upload.php',
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function (res){
                data = JSON.parse(res)
                if(data.status === 'ok'){
                    window[fn_name](data.data);
                }

            },
            error: function (res){
                console.log(res.responseText)
            }

        });

    }


}

function upload_avatar(media, user_id){

    var formdata = new FormData();

    if (!!media.type.match(/image.*/)) {
        formdata.append('file', media);
        formdata.append('user_id', user_id);

        $.ajax({
            url: window.URL+'/avatar_upload.php',
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function (res){
                res = JSON.parse(res);
                if(res.status === 'ok'){
                    d = new Date();
                    $('.my_avatar').find('img').attr('src', res.data+'?timestamp=' + new Date().getTime());
                }
            },
            error: function (res){
                console.log(res.responseText)
            }

        });

    }


}

$('#avatar_uploader_field').on("change", function(){
    upload_avatar(
        $(this).prop('files')[0],
        $('#avatar_upload_user_id').attr('value'),
    );
});

$('.edit_avatar').click(function (){
    $('#avatar_uploader_field').trigger('click');
});



////////////////////////////////

$('.see_phone').click(function (){
    $(this).text($(this).attr('phone')).attr('href', 'tel:+'+$(this).attr('phone_raw')).removeClass('see_phone');
});

////////////////////////////////



$('.set_work_item').click(function (){

    var work_id = $(this).attr('work_id');

    console.log(work_id);

    var data = {};

    ajax_send(data, 'works/'+work_id, function (res){
        console.log(res);
        if(res.status === 'ok'){

            $('.modal_work_item .title').text(res.data.name);
            $('.modal_work_item .description').html('<b>'+res.data.price+'</b>')
            $('.modal_work_item .content').text(res.data.content)

            $('.modal_work_item .photos_wrapper').html('');




            if(res.data.medias.length > 0){

                res.data.medias.forEach(media => {
                    console.log(media);

                    $('.modal_work_item .photos_wrapper').append('<img data-lazy="'+media['600x488']+'" src="'+media['160x130']+'">')
                })
                //<img data-lazy="images/slaid_max.jpg" src="images/slaid_min.jpg">
            }

            $('.modal_work_item .photos_wrapper').slick('refresh');

            //$('.modal_work_item .photos_wrapper').text()

        }else{
            //$('.modal_error_text').text(res.message);
            //open_modal('modal_error');
        }
    });


});

$(document).on('click', '.set_edit_work_item', function (e){

    var $target = $(e.target)

    if ($target.hasClass('delete_work_trigger') || $target.closest('.delete_work_trigger').length) {
        return
    }

    var work_id = $(this).attr('work_id');

    console.log(work_id);

    var data = {};

    ajax_send(data, 'works/'+work_id, function (res){
        console.log(res);
        if(res.status === 'ok'){

            open_modal('modal_edit_work');

            work_medias = [];

            console.log(res.data);

            $('#edit_work_id').attr('value', res.data.id);
            $('#edit_work_spec').attr('value', res.data.spec_id);

            $('#edit_work_name').attr('value', res.data.name);
            $('#edit_work_content').text(res.data.content);
            $('#edit_work_price').attr('value', res.data.price.replace(/[^+\d]/g, ''));



            $('.modal_edit_work_item .photos_wrapper').html('');

            $('.modal_edit_work .gallery_upload_wrapper').html('');

            if(res.data.medias.length > 0){

                work_medias = res.data.std_medias;

                console.log(work_medias);

                res.data.medias.forEach(function (media, index){
                    $('.modal_edit_work .gallery_upload_wrapper')
                        .append('<div class="uploaded_item"><img src="'+media['92x92']+'"><span class="work_delete_img" index="'+index+'"><i class="icon icon-add"></i></span></div>')
                })

            }

            $('.modal_edit_work .gallery_upload_wrapper').append('<div class="add_work_image"><i class="icon icon-add"></i></div>')


            //$('.modal_work_item .photos_wrapper').text()

        }else{
            //$('.modal_error_text').text(res.message);
            //open_modal('modal_error');
        }
    });


});


//menu

$('.lk_menu .has_child').click(function (){
    $(this).toggleClass('active');

    if($(this).hasClass('active')){
        $(this).next().slideDown('fast');
    }else{
        $(this).next().slideUp('fast');
    }
});


$('.lk_menu a:not(.button_sidebar)').each(function() {
    var $this = $(this);
    var href = $this.attr('href');

    if (window.location.href.indexOf(href) !== -1) {
        $this.addClass('active');

        if($this.hasClass('child') && window.innerWidth > 999){
            $this.closest('.lk_menu').find('.has_child').addClass('active');
            $this.closest('ul').show();
        }
    }
});

//end menu



function get_synq_avatar(){
    var url = window.URL+'uploads/avatars/'+user_id+'.png'

    let response = fetch(url);
    if(response.status === 404){
        url = window.URL+'uploads/avatars/no_avatar.png';
    }
    return url;
}

async function get_avatar(user_id) {
    var url = window.URL+'uploads/avatars/'+user_id+'.png'

    let response = await fetch(url);
    if(response.status === 404){
        url = window.URL+'uploads/avatars/no_avatar.png';
    }
    return url;
}

///////////

$('.review-reply').click(function (){
    var review_id = $(this).attr('review_id');
    var data = {};


    ajax_send_base(data, 'reviews/'+review_id, async function (res){
        $('.modal_add_review_answer [name=id]').attr('value', res.data.id);
        $('.modal_add_review_answer .name').text(res.data.reviewer_name);
        $('.modal_add_review_answer .avatar img').attr('src', await get_avatar(res.data.user_id).then(result=>result));
    });

    open_modal('modal_add_review_answer')

});

$('.review-complain').click(function (){
    var review_id = $(this).attr('review_id');
    var data = {};


    ajax_send_base(data, 'reviews/'+review_id, async function (res){

        $('.modal_add_review_complaint [name=id]').attr('value', res.data.id);
        $('.modal_add_review_complaint .name').text(res.data.reviewer_name);
        $('.modal_add_review_complaint .avatar img').attr('src', await get_avatar(res.data.user_id).then(result=>result));
    });

    open_modal('modal_add_review_complaint');

});

window.selected_service_id_in_history = null;

$('.add_review_get_modal').click(function (){
    var user_id = $(this).attr('user_id');
    var spec_id = $(this).attr('spec_id');
    window.selected_service_id_in_history = $(this).attr('service_id');
    var data = {};

    ajax_send_base(data, 'users/getinfo/'+user_id, async function (res){

        console.log(res.data);

        $('.modal_add_review [name=id]').attr('value', res.data.id);
        $('.modal_add_review [name=spec_id]').attr('value', spec_id);
        $('.modal_add_review .name').text(res.data.name);
        $('.modal_add_review .avatar img').attr('src', res.data.avatar);
    });

    open_modal('modal_add_review');

});

function add_review_get_modal(this_user_id, to_whom_user, spec_id, dialog_id){
        window.selected_service_id_in_history = $(this).attr('service_id');
        var data = {};

        ajax_send_base(data, 'users/getinfo/'+to_whom_user, async function (res){

            console.log(res.data);

            $('.modal_add_review [name=id]').attr('value', res.data.id);
            $('.modal_add_review [name=spec_id]').attr('value', spec_id);
            $('.modal_add_review [name=user_id]').attr('value', to_whom_user);
            $('.modal_add_review [name=this_user_id]').attr('value', this_user_id);
            $('.modal_add_review [name=to_whom_user]').attr('value', to_whom_user);
            $('.modal_add_review [name=dialog_id]').attr('value', dialog_id);
            $('.modal_add_review .name').text(res.data.name);
            $('.modal_add_review .avatar img').attr('src', res.data.avatar);
        });

        open_modal('modal_add_review');

}

//service_id

$(document).on('submit', '.add_review', function (e){
    e.stopPropagation();
    e.preventDefault();

    var data = pack_data($(this));

    //console.log(data.dialog_id);
    //return;

    ajax_send_base(data, 'reviews/add_review', function (data){

        console.log(data);

        if(data.status === 'ok'){
            create_informer('Отзыв мастеру','Вы успрешно оценили мастера', 'success', 3000);
            close_all_modals();


            var send_data = {};
            send_data.id = window.selected_service_id_in_history;

            ajax_send_base(send_data, 'users/workwith', function (data){
                if(data.status === 'ok'){

                    $('tr[service_id='+window.selected_service_id_in_history+']').remove();
                    if($('.history_tbody tr').length === 0){
                        $('.history_wrapper').html('<div class="catalog-empty">' +
                            '<img src="/assets/img/icons/find.svg" alt="" width="64" height="64">' +
                            '<p class="catalog-empty-heading">Список заказов пуст</p>' +
                            '</div>');
                    }

                }
                self.location.href='https://samprorab.com/history';
            })




        }

    })

    return false;
})

$(document).on('submit', '.add_review_answer', function (e){
    e.stopPropagation();
    e.preventDefault();

    var form = $(this);
    var data = pack_data($(this));

    ajax_send_base(data, 'reviews/add_answer', function (data){

        if(data.status === 'ok'){

            var reviews_item = $('[review_item='+data.data.id+']');
            $(reviews_item).find('.review-reply').remove();
            $(reviews_item).find('.answer_cont').text(data.data.answer);

            create_informer('Обновление отзыва','Вы успрешно ответили на отзыв', 'success', 3000);
            close_all_modals();
        }

    })

    return false;
})

$(document).on('submit', '.add_review_complaint', function (e){
    e.stopPropagation();
    e.preventDefault();

    var form = $(this);
    var data = pack_data($(this));

    ajax_send_base(data, 'reviews/add_complaint', function (data){
        if(data.status === 'ok'){

            var reviews_item = $('[review_item='+data.data.id+']');
            $(reviews_item).find('.review_controls').html('<span class="complain_info"><i class="icon icon-warning"></i>Жалоба оставлена</span>');

            create_informer('Обновление отзыва','Вы успрешно оставили жалобу на отзыв', 'success', 3000);
            close_all_modals();
        }
    })

    return false;
})


//Find spec field
{

    $('.finder_form').submit(function (){
        remap_field_item();
    });

    $(document).on('keypress',function(e) {
        if(e.which === 13) {
            if($('.find_field').is(":focus")){
                remap_field_item();
            }
        }

    });

    function remap_field_item(){
        var find_list = $('.find_field_dropdown');
        var variant = $(find_list).find(">:first-child");

        $(find_list).hide();

        if($(variant).hasClass('find_item')){
            $('.find_field').val($(this).text());
            $('.find_by_id').attr('value', $(this).attr('find-id'));
        }
    }


}


//favorite
{

    $(document).on('click', '.favorite', function (){
        var service_id = $(this).attr('service_id');
        var data = {};
        data.id = service_id;

        ajax_send_base(data, 'users/favorite', function (data){
            if(data.status === 'ok'){

                console.log(data);

                var star = $('.favorite[service_id='+data.data.id+']');

                if(data.data.subscribe === true){
                    $(star).addClass('checked');
                    $(star).addClass('active');
                }else{

                    if($(star).hasClass('favorite_page')){

                        $('.service_item[service_id='+service_id+']').remove();

                        if($('.service_item').length === 0){
                            $('.service_items').append('<h2>Список избранного пуст</h2>');
                            $('.service_items_xs').append('<h2>Список избранного пуст</h2>');
                        }

                    }else{
                        $(star).removeClass('checked');
                        $(star).removeClass('active');
                    }
                }
            }
        })

        return false
    });






}

//work with master

{

    $(document).on('click', '.work_with_master', function (e){



        function render_empty_messages(dialog_wrap){
            $(dialog_wrap).append(
                '<div class="message empty">'+
                '<p class="body">Тут пока нет сообщений, Исправьте это =)</p>'+
                '</div>'
            )
        }

        function render_history_message(dialog_wrap, messages_html){
            $(dialog_wrap).html(messages_html).animate({
                scrollTop: $(dialog_wrap).prop("scrollHeight")
            }, 0);
        }

        var service_id = $(this).attr('service_id')

        var data = {};
        data.id = service_id;

        var service_id = $(this).attr('service_id');
        var this_user_id = $(this).attr('this_user_id');
        var whom_user_id = $(this).attr('to_whom_user_id');
        var status = $(this).attr('status');
        var role = $(this).attr('role');
        var spec_id = $(this).attr('spec_id');

        var data = {};
        data.id = service_id;
        data.this_user_id = this_user_id;
        data.to_whom_user_id = whom_user_id;
        data.status = status;
        data.role = role;
        data.spec_id = spec_id;
        var req = "";

        var body = "11";
        var whm = "";
        ajax_send_base(data, 'users/workwith', function (data2){
            if(data2.status === 'ok'){

                //if(data2.data.worked === true){

                    //open_modal('modal_confirm_work_with_master');

                    $('.unwork').attr('service_id', service_id);


                    //$('.work_with_master').text('Прекратить работу');
                    if(role == "master"){
                        whm = this_user_id;
                    } else {
                        whm = whom_user_id;
                    }
                    req = {id:data.id, dialog_id: 5, this_user_id: this_user_id, whom_user_id: whom_user_id, type: 'text', body: body, sender: this_user_id, status: status, role: role, spec_id: spec_id };
                    //sender: UID,
                    ajax_send_base(req, 'users/preparation_work_with', function (data3){
                        if(data3.status === 'ok'){
                            self.location.href="https://samprorab.com/messages/"+whm + '?status_req=' + status;
                            //pdate_tab_name_and_time(data3.data.dialog.id);
                            //data.action = 'get_messages';
                            //data.dialog_id = data3.dialog.id;

                            //ajax_send_base(data, 'messages')
                            /*req = {"action":'get_messages', "dialog_id": data3.data.dialog.id}


                            ajax_send_base(data, 'messages', function (data){
                                if(data.status == 'ok'){

                                    //console.log(data.data.messages_htmlcc.length);

                                    $('.chat_box').append('<div class="messages_wrapper" dialog_id="'+data.data.dialog_id+'"></div>');
                                    dialog_wrap = $('.messages_wrapper[dialog_id='+data.data.dialog_id+']');

                                    $('.messages_wrapper').removeClass('current');
                                    $(dialog_wrap).addClass('current');

                                    //var messages_count = Object.keys(data.data.messages);

                                    if(data.data.messages_htmlcc.length == 0){
                                        render_empty_messages(dialog_wrap);
                                        $('.chat_box').removeClass('disabled');
                                        $('.chat_input_wrap').removeClass('disabled');
                                    }else{
                                        //$.each( data.data.messages, function(i, obj) {
                                        $('.chat_box').removeClass('disabled');
                                        $('.chat_input_wrap').removeClass('disabled');

                                        render_history_message(dialog_wrap, data.data.messages_htmlcc);
                                        //});
                                    }

                                    read_message(dialog_id);

                                }
                            })*/


                        }
                    })
                //}else{
                    if(self.location.href.match('/service/')){
                        $('.work_with_master > span').text('Работаю с мастером');
                    }
                //}

            }
        })

    });


    $(document).on('click', '.unwork', function (){

        var data = {};
        data.id = $(this).attr('service_id');

        ajax_send_base(data, 'users/workwith', function (data){
            if(data.status === 'ok'){

                if(data.data.worked === true){
                    $('.work_with_master > span').text('Прекратить работу');
                }else{
                    close_all_modals();
                    $('.unwork').attr('service_id', '');
                    $('.work_with_master > span').text('Работаю с мастером');
                }

            }
        })
    });


    $(document).on('click', '.delete_history', function (){


        var service_id = $(this).attr('service_id');

        var data = {};



        data.id = service_id;


        var service_id = $(this).attr('service_id');
        var this_user_id = $(this).attr('this_user_id');
        var whom_user_id = $(this).attr('to_whom_user_id');
        var status = $(this).attr('status');
        var role = $(this).attr('role');
        var spec_id = $(this).attr('spec_id');

        var data = {};
        data.id = service_id;
        data.this_user_id = this_user_id;
        data.to_whom_user_id = whom_user_id;
        data.status = status;
        data.role = role;
        data.spec_id = spec_id;
        var req = "";

        var body = "";
        var whm = "";



        ajax_send_base(data, 'users/workwith', function (data){
            if(data.status === 'ok'){

                $('.history_item[service_id='+service_id+']').remove();



                if($('.history_tbody tr').length === 0){
                    $('.history_wrapper').html('<div class="catalog-empty">' +
                        '<img src="/assets/img/icons/find.svg" alt="" width="64" height="64">' +
                        '<p class="catalog-empty-heading">Список заказов пуст</p>' +
                        '</div>');
                }

            }
        })

    });


}


//////////////


{

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };


    $(function (){

        var tab = getUrlParameter('tab');
        if(tab === 'reviews'){
            $('.tabs_titles .select_tab[tab=reviews]').trigger('click');
        }

        $(document).on('click', '.review_data_cont', function (){
            //$(this).toggleClass('get_cuted');
        });

        $(document).on('click', '.review-text .expand-link', function () {
            var $this = $(this)
            var $parent = $this.parent()

            $parent.find('.review-text-croped').toggle()
            $parent.find('.review-text-full').toggle()
            $this.toggleClass('up')
        });

        $(document).on('click', '.open_review', function (){

            var text = $(this).text()

            if(text === 'Развернуть'){
                $(this).text('Свернуть')
            }else{
                $(this).text('Развернуть')
            }

            $(this).parent().prev().toggleClass('get_cuted');

            //$(this).toggleClass('get_cuted');
        });

        $('.lk_menu .has_child').click(function (){
            if($(this).hasClass('active')){
                $('.xs_menu_header').addClass('active');
            }else{
                $('.xs_menu_header').removeClass('active');
            }
        });

        $('.menu_back').click(function (){
            $('.lk_menu .has_child').trigger('click');
        })



        $('.modal_work_item .photos_wrapper').slick({
            dots: false,
            arrows: true,
            infinite: true,
            speed: 500,
            cssEase: 'linear',
            lazyLoad: 'progressive'
        });




    })

}

//$(document).on( "swiperight", function( event ) {
    // $('#footer_menu_wrap').css({'left':0+'%', 'opacity':1});
//});

//$('.footer_menu').on("swipeleft", function( event ) {
  //  $('#footer_menu_wrap').css({'left':-100+'%', 'opacity':0});
//});


$(document).on('click', '.close_footer_menu', function (){
    $('#footer_menu_wrap').css({'left':-100+'%', 'opacity':0});
});

$(document).on('click', '.accordion .title', function (){
    $(this).toggleClass('active');
});
$(document).on('click', '.header-menu__btn', function (){
    $(this).parent().toggleClass('header-menu__btn--active');
    $('body').toggleClass('header-menu__body--active');
    $('.header_nav').toggleClass('header_nav--active');
});

$(document).ready(function() {
    //ANTON
    $('#header-menu__toggle').on('click', function() {
        $(this).toggleClass('active');
        $('.city_changer').toggleClass('active');
    });
    $('.mobile-menu-droplist h3').on('click', function() {
        $(this).toggleClass('active');
    });

    $("input#edit_pass, input#edit_pass_confirm").togglePassword({ el: '.show-edit-pass' });
    $("input#reg_form_pass").togglePassword({ el: '.show-reg-pass' });
    $("input#login_form_pass").togglePassword({ el: '.show-login-pass' });
    $("input#forgot_pass").togglePassword({ el: '.show-forgot-pass' });
    $("input#forgot_pass_confirm").togglePassword({ el: '.show-forgot-pass-confirm' });

    $(document).on('click', '[data-link]', function (e) {
        var link = e.currentTarget.getAttribute('data-link')
        if (link) {
            window.location.href = link
        }
    });
});
