<?php
declare(strict_types=1);


namespace BladL\SMSFly\Exceptions;


use BladL\SMSFly\Message;
use Exception;

final class StateIsNotOk extends Exception
{
    protected Message $smsMsg;
    private string $state;

    public function __construct(Message $message,string $state)
    {
        $this->smsMsg = $message;
        $this->state = $state;
        parent::__construct('Message state is not ok');
    }

    public function getMessageState():string {
        return $this->state;
    }
    public function getSMSMessage(): Message
    {
        return $this->smsMsg;
    }
}
