<?php
/*
 *  Last Modified: 6/29/21, 12:06 AM
 *  Copyright (c) 2021
 *  -created by Ariful Islam
 *  -All Rights Preserved By
 *  -If you have any query then knock me at
 *  arif98741@gmail.com
 *  See my profile @ https://github.com/arif98741
 */

namespace Xenon\LaravelBDSms\Provider;

use Xenon\LaravelBDSms\Handler\ParameterException;
use Xenon\LaravelBDSms\Request;
use Xenon\LaravelBDSms\Sender;

class Tense extends AbstractProvider
{
    /**
     * Tense constructor.
     * @param Sender $sender
     * @since v1.0.25
     */
    public function __construct(Sender $sender)
    {
        $this->senderObject = $sender;
    }

    /**
     * Send Request To Api and Send Message
     * @since v1.0.25
     */
    public function sendRequest()
    {
        $number = $this->senderObject->getMobile();
        $text = $this->senderObject->getMessage();
        $config = $this->senderObject->getConfig();
        $queue = $this->senderObject->getQueue();
        $queueName = $this->senderObject->getQueueName();
        $tries=$this->senderObject->getTries();
        $backoff=$this->senderObject->getBackoff();
        $query = [
            'user' => $config['user'],
            'password' => $config['password'],
            'campaign' => $config['campaign'],
            'masking' => $config['masking'],
            'number' => $number,
            'text' => $text,
        ];

        $requestObject = new Request('http://sms.tense.com.bd/api-sendsms', $query, $queue, [], $queueName,$tries,$backoff);
        $response = $requestObject->get();
        if ($queue) {
            return true;
        }

        $body = $response->getBody();
        $smsResult = $body->getContents();

        $data['number'] = $number;
        $data['message'] = $text;
        return $this->generateReport($smsResult, $data)->getContent();
    }

    /**
     * @throws ParameterException
     * @since v1.0.25
     */
    public function errorException()
    {
        if (!array_key_exists('user', $this->senderObject->getConfig())) {
            throw new ParameterException('user is absent in configuration');
        }
        if (!array_key_exists('password', $this->senderObject->getConfig())) {
            throw new ParameterException('password key is absent in configuration');
        }
        if (!array_key_exists('campaign', $this->senderObject->getConfig())) {
            throw new ParameterException('campaign key is absent in configuration');
        }
        if (!array_key_exists('masking', $this->senderObject->getConfig())) {
            throw new ParameterException('masking key is absent in configuration');
        }
    }

}
