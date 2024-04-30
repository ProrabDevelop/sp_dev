<?php

namespace core\engine;

class sms
{
    private static $smsc_login = 'FreeZaWeb';
    private static $smsc_pass = 'Nm001526';

    public const DELAY = 120;

    public static function init($phone, $action)
    {
        $existsSms = \ORM::for_table('sms_codes')
            ->where('phone', $phone)
            ->where('action', $action)
            ->where('status', 0)
            ->find_one();

        if ($existsSms) {
            return $existsSms;
        }

        $code = self::generateCode();

        $sms = \ORM::for_table('sms_codes')->create();
        $sms->created_at = time();
        $sms->phone = $phone;
        $sms->hash = uniqid('', false);
        $sms->action = $action;
        $sms->code = $code;
        $sms->status = 0;
        $sms->save();

        self::send($sms);

        return $sms;
    }

    public static function checkConfirmed($phone, $hash, $action)
    {
        return \ORM::for_table('sms_codes')
            ->where('phone', $phone)
            ->where('action', $action)
            ->where('hash', $hash)
            ->where('status', 1)
            ->find_one();
    }

    public static function getDelay($sms)
    {
        return self::DELAY - (time() - $sms->created_at);
    }

    public static function resend($phone, $hash)
    {
        $sms = \ORM::for_table('sms_codes')
            ->where('phone', $phone)
            ->where('hash', $hash)
            ->where('status', 0)
            ->find_one();

        if (!$sms || self::getDelay($sms) > 0) {
            return false;
        }

        $code = self::generateCode();

        $sms->code = $code;
        $sms->created_at = time();
        $sms->save();

        self::send($sms);

        return $sms;
    }

    public static function forgot($phone, $action)
    {
        return \ORM::for_table('sms_codes')
            ->where('phone', $phone)
            ->where('action', $action)
            ->delete_many();
    }

    public static function confirm($phone, $hash, $code, $action)
    {
        $sms = \ORM::for_table('sms_codes')
            ->where('phone', $phone)
            ->where('action', $action)
            ->where('hash', $hash)
            ->where('code', $code)
            ->where('status', 0)
            ->find_one();

        if ($sms) {
            $sms->status = 1;
            $sms->save();

            return $sms;
        }

        return false;
    }

    public static function send($sms)
    {
//        $message = 'код: '.$sms->code.' Никому не сообщайте его!';
//        self::sender($sms->phone, $message);
    }

    protected static function generateCode()
    {
        return str_pad(rand(0, 999999), 6, 0, STR_PAD_LEFT);
    }

    protected static function sender($phone, $message){

        $addr = 'https://smsc.ru/sys/send.php?login='.self::$smsc_login.'&psw='.self::$smsc_pass.'&phones='.$phone.'&mes='.urlencode($message);
    }
}
