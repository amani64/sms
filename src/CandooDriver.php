<?php

namespace Amani64\SMS;


use GuzzleHttp\Client;
use nusoap_client;

class CandooDriver implements SMSInterface
{
    protected $to;

    protected $message;

    protected $from;

    protected $type;

    protected $url;

    protected $method;

    protected $flash = '';

    protected $username = '';

    protected $password = '';

    protected $apiUrl;

    protected $base;

    public function handle($data)
    {
        $this->username = config('candoo.username');
        $this->password = config('candoo.password');
        $this->url = config('candoo.api_url');
        $this->from = config('candoo.sender.1');
        $this->flash = config('candoo.flash');
        $mode = config('candoo.send_mode');
        if ($mode == 'url') {
            $this->sendUrlMode($data);
        } else if ($mode == 'wsdl') {
            $this->sendWSDL($data);
        }
    }

    public function send(string $to, string $message, string $type, string $sender = null)
    {
        $data = [];

        if ($type == 'notify') {
            $this->method = 'send';
            $this->base = 'sms';
            $data['destinations'] = $to;
            $data['message'] = $message;
        }

        $this->handle($data);
    }

    private function sendWSDL($data)
    {

        $client = new nusoap_client('http://my.candoosms.com/services/?wsdl', true);
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;

        $err = $client->getError();
        if ($err) {
            throw new \Exception('Problem in sending SMS. ERROR: ' . $err, 500);
        }
//        $desNos = array();
        $result = $client->call('Send', [
            'username' => (string) $this->username,
            'password' => (string) $this->password,
            'srcNumber' => (string) $this->from,
            'encoding' => 'UTF-8',
            'body' => $data['message'],
            'destNo' => (string) $data['destinations'],
            'flash' => (string) $this->flash
        ]);

        if ($client->fault) {
            throw new \Exception('Problem in sending SMS. FAULT: ' . $result, 500);
        } else {
            $err = $client->getError();
            if ($err) {
                throw new \Exception('Problem in sending SMS. ERROR: ' . $err, 500);
            } else {
                foreach ($result as $value) {
                    return true;
                }
            }
        }

        return true;
    }

    private function sendUrlMode($data)
    {
        $this->apiUrl = sprintf($this->url, $this->username, $this->password, config('candoo.sender.1'), $data['destinations'], $data['message'], config('candoo.flash'));

        $client = new Client(); //GuzzleHttp\Client
        $response = $client->post($this->apiUrl, []);
        if ($response->getBody()) {
            $response = $response->getBody()->getContents();
            if (!empty($response)) {
                $responseArray = explode('<br/>', $response);
                if (!empty($responseArray[1])) {
                    $parts = explode(' ', $responseArray[1]);
                    $id = (explode(':', $parts[0]))[0];
                    $mobile = (explode(':', $parts[1]))[0];
                    if ($id == 'ID' && $mobile == 'Mobile') {
                        return true;
                    }
                }
            }
        }
        throw new \Exception('Problem in sending SMS. ERROR: ' . $response, 500);
    }


    protected function get_path($method, $base = 'sms')
    {
        return sprintf(self::APIPATH, $this->apiKey, $base, $method);
    }

}
