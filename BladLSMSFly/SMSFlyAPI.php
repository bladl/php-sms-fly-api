<?php
declare(strict_types=1);


namespace BladL\SMSFly;


use BladL\SMSFly\Data\MessagesResult;
use BladL\SMSFly\Exceptions\BadXMLReturned;
use BladL\SMSFly\Exceptions\CurlFailed;
use BladL\SMSFly\Exceptions\ErrorReturned;
use BladL\SMSFly\Exceptions\StateIsNotOk;
use BladL\SMSFly\Types\Campaign\StateCode;
use BladL\Time\TimeZone;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleXMLElement;
use function is_string;

class SMSFlyAPI
{
    private string $password;
    private string $login;
    public static function getTimeZone():TimeZone {
        return new TimeZone('Europe/Kiev');
    }
    private LoggerInterface $logger;

    final public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
    public function __construct(string $login, string $password)
    {
        $this->logger = new NullLogger();
        $this->login = $login;
        $this->password = $password;
    }
    /**
     * @throws CurlFailed|ErrorReturned|BadXMLReturned|StateIsNotOk
     */
    public function sendSms(Message $message):MessagesResult {
        $result  = new MessagesResult($this->sendPost('SENDSMS',$message->getXmlBody()));
        if ($result->getStateCode()->isOneOf(StateCode::ACCEPT)) {
            return $result;
        }
        throw new StateIsNotOk($message);
    }

    /**
     * @throws CurlFailed|ErrorReturned|BadXMLReturned
     */
    protected function sendPost(string $operation,string $body): SimpleXMLElement {
        $post_fields =
            '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL
            ."<request><operation>$operation</operation>$body</request>";
        $this->logger->debug("POST '$post_fields'");
        $request = curl_init();
        curl_setopt($request, CURLOPT_USERPWD , $this->login.':'.$this->password);
        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($request, CURLOPT_POST, 1);
        /** @noinspection HttpUrlsUsage */
        curl_setopt($request, CURLOPT_URL, 'http://sms-fly.com/api/api.php');
        curl_setopt($request, CURLOPT_HTTPHEADER, ['Content-Type: text/xml', 'Accept: text/xml']);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS,  $post_fields);
        $response = curl_exec($request);
        $this->logger->debug("Response '$response'");
        curl_close($request);
        if (false === $response) {
            $error = curl_error($request);
            $this->logger->critical("Curl error '$error'");
            throw new CurlFailed($error,curl_errno($request));
        }
        $use_errors = libxml_use_internal_errors(true);
        $xml_resp = simplexml_load_string($response);
        if (false === $xml_resp || null === $xml_resp) {
            if (is_string($response)) {
                $this->logger->error("Error '$response'");
                throw new ErrorReturned($response);
            }
            $this->logger->critical("Bad xml '$response'");
            throw new BadXMLReturned($response);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($use_errors);
        if (is_string($xml_resp)) {
            $this->logger->error("Error $xml_resp");
            throw new ErrorReturned($xml_resp);
        }
        return $xml_resp;
    }
}