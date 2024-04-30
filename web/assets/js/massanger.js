var active_dialog = 0;
var need_update_read = false;
var wsssend = null;

$(function () {

    var sender = false;
var socket = "";
    var connect = function(){

        var udata = JSON.parse($.cookie('udata'));

		    console.log(udata);
		    console.log(udata.id);
		    console.log(udata.salt);
	socket = new WebSocket("wss://samprorab.com:8080", $.cookie('jwt')+'~'+udata.id+'~'+udata.salt);

        //ONOPEN
        socket.onopen = function() {
            console.log("WS: Соединение с сервером установлено.");
        };

        //ONCLOSE
        socket.onclose = function(event) {
            console.log('WS: Разрыв соединения "код": ' + event.code);
            setTimeout(function() {
                connect();
            }, 1000);
          	console.log(socket.readyState);
        };

        //ONERROR
        socket.onerror = function(event) {
            console.log('WS: Непредвиденная ошибка');
			console.log(event);
            socket.close();

        };

        //ONMESSAGE
        socket.onmessage = function(message_item) {

            var mes = JSON.parse(message_item.data);

            console.log(mes);

            if(mes.action == 'auth'){
                if(mes.status == 'inited'){
                    sender = true;
                    if(window.selected_chat !== undefined){
                        $('.dialog_item[dialog_id='+window.selected_chat+']').trigger('click');
                    }

                }
            }

            if(mes.action == 'message'){
                print_message(mes);
            }

            if(mes.action == 'messages_readed'){
                update_tab_name_and_time(mes.dialog_id);
                user_readed_message(mes.dialog_id);
            }

            if(mes.action == 'read_updated'){
                update_dialog_list();
            }

        };

    };

    /////////////////////////////////////////////////
    /////////////////////////////////////////////////
    connect();
  var udata = JSON.parse($.cookie('udata'));
  console.log(udata);
    /////////////////////////////////////////////////
    /////////////////////////////////////////////////

    function wssend(data) {
        console.log(data);
        if(sender){
            if(data.action == 'send'){
                update_tab_name_and_time(data.dialog_id);

                print_my_message({
                    my_id: UID,
                    dialog_id: data.dialog_id,
                    body: data.body,
                    class: data.class
                })
            }

            if(socket.readyState != 1){
                setTimeout(function (){
                    wssend(data);
                },100);
            }else{
               socket.send(JSON.stringify(data));
            }
        }
    };
    wsssend = wssend;

    /////////////////////////////////////////////////

    function read_message(dialog_id){
        wssend({
            action: 'update_readed',
            dialog_id: dialog_id,
            reader: UID,
        });
    };

    /////////////////////////////////////////////////

    //send message
    $('.send_message').click(function () {
        //console.log(dialog_id);

        var message = $('.my_message').val();

        if(message.trim().length > 0){

            var dialog_id = $('.messages_wrapper.current').attr('dialog_id');

            update_tab_name_and_time(dialog_id);

            wssend({
                action: 'send',
                dialog_id: dialog_id,
                type: 'text',
                body: message,
                //sender: UID,
                class: Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 7),
            });
        }
        $('.my_message').val('');
    });

    $('.my_message').keypress(function (e) {
        if (e.which == 13) {
            $('.send_message').trigger('click');
            return false;    //<---- Add this line
        }
    });

    /////////////////////////////////////////////////


    function update_tab_name_and_time(dialog_id){

        var dialog_item = $('.dialog_item[dialog_id='+dialog_id+']');

        var name = $(dialog_item).find('.name').text();
        var uid = $(dialog_item).find('.name').attr('uid');

        $('.selected_chat_info .name').text(name);

        ajax_send_base({}, 'users/last_visit/'+uid, function (res){

            if(res.status == 'ok'){
                $('.selected_chat_info .time').text(res.data.last_visit);
            }

        })

    }


    //select dialog
    $(document).on('click', '.dialog_item', function (){


        var dialog_id = $(this).attr('dialog_id');
        var dialog_wrap = $('.messages_wrapper[dialog_id='+dialog_id+']');
        active_dialog = dialog_id;
        send_change_status_message();
$('.dialog_item').removeClass('selected_chat');
        $('.dialog_wrapper').addClass('chat_selected');
      $(this).addClass('selected_chat');
        update_tab_name_and_time(dialog_id);
        if($(dialog_wrap).length == 0){

            var data = {};
            data.action = 'get_messages';
            data.dialog_id = dialog_id;

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
            })

        }else{
            read_message(dialog_id);

            $('.messages_wrapper').removeClass('current');
            $(dialog_wrap).addClass('current');
            $('.chat_box').removeClass('disabled');
            $('.chat_input_wrap').removeClass('disabled');

            $(dialog_wrap).animate({
                scrollTop: $(dialog_wrap).prop("scrollHeight")
            }, 0);

        }

    });

    /////////////////////////////////////////////////

    function user_readed_message(dialog_id){

        update_tab_name_and_time(dialog_id);

        var dialog_wrapper = $('.messages_wrapper[dialog_id='+dialog_id+']');

        if($(dialog_wrapper).length > 0){
            $(dialog_wrapper).find('.message_status').addClass('readed');
        }
    }

    /////////////////////////////////////////////////

    function update_dialog_list(){
            //ajax_send_base(data, 'messages', function (data){
            ajax_send_base({action: 'get_chat_list'}, 'messages', function (data){
        //ajax_send({action: 'get_chat_list'}, 'messages', function (data){
            $('.chat_list').html(data.data.chat_list_html).find('.dialog_item[dialog_id='+active_dialog+']').addClass('selected_chat');

            $('.all_unread_messages_counter').text(data.data.all_unread_count);

            if(parseInt(data.data.all_unread_count) == 0){
                $('.all_unread_messages_counter').addClass('hide');
            }
        });
    }

    /////////////////////////////////////////////////

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



    async function print_message(message_data){

        var ts = message_data.time;
        var time = $.format.date(ts, "dd.MM.yyyy HH:mm:ss");

        var dialog_wrap = $('.messages_wrapper[dialog_id='+message_data.dialog_id+']');
        var uid = $('.selected_chat').find('.name').attr('uid');

        if (message_data.body === 'Завершил работу') {
            self.window.location.href = '/messages/' + uid;
        }
        if (message_data.body === 'Хочу с вами работать!') {
            self.window.location.href = '/messages/' + uid;
        }
        if (message_data.body === 'Берусь работать!') {
            self.window.location.href = '/messages/' + uid;
        }

        $(dialog_wrap).append(
            '<div class="message incoming">'+
            '<img class="avatar" src="'+ await get_avatar(message_data.sender).then(result=>result)+'">'+
            '<p class="body">'+message_data.body+'</p>'+
            '<p class="time">'+time+'</p>'+
            '</div>'
        ).animate({
            scrollTop: $(dialog_wrap).prop("scrollHeight")
        }, 0);

        var all_count_unreaded = parseInt($('.all_unread_messages_counter').text());
        all_count_unreaded++;

        if(document.hidden){
            need_update_read = true;
            update_dialog_list();
            play_incoming_notify();
            $('.all_unread_messages_counter').text(all_count_unreaded).removeClass('hide');
            return;
        }

        if($(dialog_wrap).hasClass('current')){
            read_message(message_data.dialog_id);
            return;
        }else{

            //$('.all_unread_messages_counter').text(all_count_unreaded).removeClass('hide');

            update_dialog_list();
            play_incoming_notify();

            return;
        }




    }

    async function print_my_message(message_data){

        var dialog_wrap = $('.messages_wrapper[dialog_id='+message_data.dialog_id+']');

        let time = $.format.date(new Date(), "dd.MM.yyyy HH:mm:ss");

        $(dialog_wrap).append(//!!!!!!!!!!!!!!
            '<div class="message sended '+message_data.class+'">'+
            '<img class="avatar" src="'+ await get_avatar(UID).then(result=>result) +'">'+
            '<p class="body">'+message_data.body+'<span class="message_status"><i class="icon icon-check"></i><i class="icon icon-check"></i></span></p>'+
            '<p class="time">'+time+'</p>'+
            '</div>'
        ).animate({
            scrollTop: $(dialog_wrap).prop("scrollHeight")
        }, 0);

    }

    /////////////////////////////////////////////////

    function play_incoming_notify(){
        var audio = new Audio(window.URL+'assets/audio/notification.mp3');
        audio.play();
    }

    document.addEventListener("visibilitychange", function() {
        if (document.visibilityState == 'visible') {
            if(need_update_read){
                read_message(active_dialog);
            }
        }
    });


    $(document).on('click', '.messages_back', function (){
        localStorage.removeItem('status_for_' + active_dialog);
        active_dialog = 0;
        $('.dialog_wrapper').removeClass('chat_selected');
        $('.dialog_wrapper .chat_list .dialog_item').removeClass('chat_selected');
        $('.messages_wrapper').removeClass('current');
        $('.chat_box').addClass('disabled');
        $('.chat_input_wrap').addClass('disabled');
    });

    function send_change_status_message () {
        let status_requested_is = window.location.href.split(window.location.host)[1].split('?')[1];

        // отправляем сообщение о том что с нами хотят работать/не хотят работать
        if (status_requested_is) {
            let status_id = Number(status_requested_is.split('=')[1]);

            let body = '';
            if (status_id === 1) {
                body = 'Завершил работу';
            } else if (status_id === 2) {
                body = 'Хочу с вами работать!';
            } else if (status_id === 3) {
                body = 'Берусь работать!';
            } else if (status_id === 4) {
                body = 'Завершил работу';
            }

            if (body !== '') {
                wsssend({
                    action: 'send',
                    dialog_id: active_dialog,
                    type: 'text',
                    body: body,
                    sender: UID,
                    class: Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 7),
                });

                setTimeout(function () {
                    var uid = $('.selected_chat').find('.name').attr('uid');
                    self.window.location.href = '/messages/' + uid;
                }, 100);
            }
        }
    }
});
