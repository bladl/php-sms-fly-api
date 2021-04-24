<?php
declare(strict_types=1);


namespace BladL\SMSFly\Exceptions;



final class BadXMLReturned extends RequestFailed
{
    protected string $string;
    public function __construct(string $string )
    {
        $this->string = $string;
        parent::__construct('Bad XML body');
    }
    public function getXMlString():string {
        return $this->string;
    }
}
