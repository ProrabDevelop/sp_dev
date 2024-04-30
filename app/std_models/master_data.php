<?php

namespace app\std_models;

class master_data extends custom_user_data {
    public $data = [];
    public $data_arrmap = ['experience', 'finished', 'phone', 'telegram', 'whatsapp', 'viber', 'vk', 'fb', 'inst', 'week_1', 'week_2', 'week_3', 'week_4', 'week_5', 'week_6', 'week_7', 'open_time', 'close_time', 'contract', 'guarantee', 'docs_ip', 'docs_ooo', 'type_ip', 'type_ooo'];

    public $data_arrmap_names = [
        'experience' => 'Опыт работы',
        'finished' => 'Выполненных проектов',
        'phone' => 'Телефон',
        'telegram' => 'Telegram',
        'whatsapp' => 'WhatsApp',
        'viber' => 'Viber',
        'vk' => 'Vkontakte',
        'fb' => 'Facebook',
        'inst' => 'Instagram',
        'week_1' => 'Понедельник',
        'week_2' => 'Вторник',
        'week_3' => 'Среда',
        'week_4' => 'Четверг',
        'week_5' => 'Пятница',
        'week_6' => 'Суббота',
        'week_7' => 'Воскресенье',
        'open_time' => 'Время работы до',
        'close_time' => 'Время работы до',
        'contract' => 'Работа по договору',
        'guarantee' => 'Гарантия',
        'type_ip' => 'ИП',
        'type_ooo' => 'ООО',
        'docs_ip' => 'Документы ИП',
        'docs_ooo' => 'Документы ООО',
        ];

    protected $table = 'master_data';


}