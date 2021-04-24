<?php
declare(strict_types=1);


namespace BladL\SMSFly\Data;

use BladL\SMSFly\SMSFlyAPI;
use BladL\SMSFly\Types\Campaign\StateCode;
use BladL\Time\Moment;
use Exception;

final class MessagesResult extends Container
{
    public function getStateCode(): StateCode
    {
        return StateCode::fromString((string)$this->getData()->state->attributes()->code);
    }
    public function getCampaignId():int {
        return (int)$this->getData()->state->attributes()->campaignID;
    }
    public function getStateDesc():string {
        return (string)$this->getData()->state;
    }

    /**
     * @throws Exception
     */
    public function getDate():Moment{
        return new Moment(SMSFlyAPI::getTimeZone(),(string)$this->getData()->date);
    }

    /**
     * @return MessageTo
     */
    public function getTo(): MessageTo
    {
        return new MessageTo($this->getData()->to);
    }
}
