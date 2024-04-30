<?php

namespace core\engine;

class API{



    public static function auto_validate(\validator $fields, callable $fn){
        self::logger('ЛОГИРУЕМ AUTO VALIDATE');
        self::logger($_POST);
        if(empty($_POST)){
            $_POST = file_get_contents('php://input');
        }

        if($fields->POST()){

            if($fields->errors){
                API::error_validator($fields);
            }else{
                $fn($fields);
            }

        }else{
            API::no_post_data();
        }

    }



    public static function response($data = null){

        $res['status'] = 'ok';

        if($data != null){
            $res['data'] = $data;
        }

        self::logger('RESPONSE');
        self::logger($data);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function logger($message)
    {
        $logPath = __DIR__ . '/logs';
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }

        $errorMsg = date('Y/m/d H:i:s') . print_r($message,true) . PHP_EOL;
        error_log($errorMsg, 3, $logPath . '/errors.log');
    }

    public static function error( int $code, string $message, $data = null){

        $res = [
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'data' => null,
        ];

        if($data != null){
            $res['data'] = $data;
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function no_post_data($data = null){
        self::logger('POST данные не поступили'.$data);
        self::error(103, 'POST данные не поступили', $data);
    }

    public static function error_validator(\validator $fields){
        self::logger('Ошибка валидации данных '.json_encode($fields->get_fields(), JSON_UNESCAPED_UNICODE));
        self::error(135, 'Ошибка валидации данных', $fields->errors);
    }

}
