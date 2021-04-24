<?php
declare(strict_types=1);


namespace BladL\SMSFly\Exceptions;


use BladL\SMSFly\Message;
use Exception;

final class StateIsNotOk extends Exception
{
    protected Message $smsMsg;

    public function __construct(Message $message)
    {
        $this->smsMsg = $message;
        parent::__construct('Message state is not ok');
    }


    public function getSMSMessage(): Message
    {
        return $this->message;
    }
}
