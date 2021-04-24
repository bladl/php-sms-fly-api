<?php
declare(strict_types=1);


namespace BladL\SMSFly\Exceptions;


use BladL\SMSFly\Message;
use BladL\SMSFly\Types\Campaign\StateCode;
use Exception;

final class StateIsNotOk extends Exception
{
    protected Message $smsMsg;
    private StateCode $state;

    public function __construct(Message $message,StateCode $state)
    {
        $this->smsMsg = $message;
        $this->state = $state;
        parent::__construct('Message state is not ok');
    }

    public function getMessageState():StateCode {
        return $this->state;
    }
    public function getSMSMessage(): Message
    {
        return $this->smsMsg;
    }
}
