<?php

namespace App\Classes;

class Response
{

    const ACK_RESPONSE_KEY = 'ack';
    const DATA_RESPONSE_KEY = 'data';
    const SUCCESS_RESPONSE_VAL = "success";
    const ERROR_RESPONSE_KEY = "error";
    const CODE_RESPONSE_KEY = "code";
    const MSG_RESPONSE_KEY = "msg";
    const INSERT_ID_RESPONSE_KEY = "insert_id";

    public static function build($ack, $result) {

        return json_encode([
            self::ACK_RESPONSE_KEY => $ack,
            self::DATA_RESPONSE_KEY => $result,
        ]);

    }

}
