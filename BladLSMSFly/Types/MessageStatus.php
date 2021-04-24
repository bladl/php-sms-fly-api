<?php
declare(strict_types=1);


namespace BladL\SMSFly\Types;


use InvalidArgumentException;
use function in_array;

final class MessageStatus extends StringType
{
    public const ACCEPTED = 'ACCEPTED';
    public const ERROR = 'ERROR';

    public static function fromString(string $value): self
    {
        if (in_array($value,[self::ACCEPTED,self::ERROR],true) ) {
            return new self($value);
        }
        throw new InvalidArgumentException("Message status '$value' is invalid");
    }
    public function isAccepted():bool {
        return $this->isOneOf(self::ACCEPTED);
    }
}
