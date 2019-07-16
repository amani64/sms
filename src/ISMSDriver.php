<?php

namespace Amani64\SMS;


use Amani64\SMS\Models\SMSLog;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

//use nusoap_client;

class ISMSDriver implements SMSInterface
{
    protected $type;

    protected $url;

    public function handle($data)
    {
        try {
            $data['username'] = config('isms.username');
            $data['password'] = config('isms.password');

            $this->url = config('isms.url');

            $client = new Client(); //GuzzleHttp\Client
            $response = $client->post($this->url, [
                'form_params' => $data
            ]);
            $status = null;
            $logData = [
                'driver' => 'isms',
                'message' => $data['body'],
                'mobiles' => json_encode($data['mobiles']),
            ];
            if ($response->getBody()) {
                $content = $response->getBody()->getContents();
                $logData['response'] = $content;

                if (!empty($content)) {
                    $responseData = (array)json_decode($content);
                    if (!empty($responseData['code'])) {
                        $logData['status'] = 'Failed';
                    }
                    if (!empty($responseData['ids'])) {
                        $logData['status'] = 'Send';

                    }
                }
            }
            if (config('sms.log_response')) {
                SMSLog::create($logData);
            }

        } catch (\Exception $e) {
            $message = !empty($responseData['message']) ? $responseData['message'] : $e->getMessage();
            throw new \Exception('SMS sending error: ' . $message);
        }
    }

    public function send(string $to, string $message, string $type, string $sender = null)
    {

        $to = is_array($to) ? $to : [$to];
        $data = [
            'mobiles' => $to,
            'body' => $message,
        ];

        $this->handle($data);
    }

    public function sendSMS()
    {

    }

    protected function get_path($method, $base = 'sms')
    {
        return sprintf(self::APIPATH, $this->apiKey, $base, $method);
    }

}
