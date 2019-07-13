<?php

namespace Amani64\SMS;


use GuzzleHttp\Client;

class KavenegarDriver implements SMSInterface
{
    protected $to;

    protected $message;

    protected $from;

    protected $type;

    protected $url;

    protected $method;

    protected $apiKey = '';

    protected $apiUrl;

    protected $base;

    public function handle($data)
    {
        $this->apiKey = config('kavenegar.api_key');
        $this->url = config('kavenegar.api_url');
        $this->apiUrl = sprintf($this->url, $this->apiKey, config('kavenegar.base'), $this->method);

        $client = new Client(); //GuzzleHttp\Client
        $response = $client->post($this->apiUrl, [
            'form_params' => $data
        ]);

        if ($response->getBody()) {
            $response = json_decode($response->getBody()->getContents());
            //تایید شد
            if ($response->return->status == 200 && $response->return->message == 'تایید شد') {
                return true;
            }
        }
        throw new \Exception('Problem in sending SMS.', 404);
    }

    public function send(string $to, string $message, string $type, string $sender=null)
    {
        $this->to = $to;
        $this->message = $message;

        $this->from = config('kavenegar.sender');
        $this->type = $type;
        $data = [];

        if ($type == 'notify') {
            $this->method = 'send';
            $this->base = 'sms';
            $data['receptor'] = $to;
            $data['sender'] = $this->from;
            $data['message'] = $message;
            $data['date'] = '';
            $data['type'] = '';
            $data['localid'] = '';
        }
        if ($type == 'code') {
            $this->method = 'lookup';
            $this->base = 'verify';
        }

        $this->handle($data);
    }


    protected function get_path($method, $base = 'sms')
    {
        return sprintf(self::APIPATH, $this->apiKey, $base, $method);
    }



    public function SendArray($sender, $receptor, $message, $date = null, $type = null, $localmessageid = null)
    {
        if (!is_array($receptor)) {
            $receptor = (array) $receptor;
        }
        if (!is_array($sender)) {
            $sender = (array) $sender;
        }
        if (!is_array($message)) {
            $message = (array) $message;
        }
        $repeat = count($receptor);
        if (!is_null($type) && !is_array($type)) {
            $type = array_fill(0, $repeat, $type);
        }
        if (!is_null($localmessageid) && !is_array($localmessageid)) {
            $localmessageid = array_fill(0, $repeat, $localmessageid);
        }
        $path   = $this->get_path("sendarray");
        $params = array(
            "receptor" => json_encode($receptor),
            "sender" => json_encode($sender),
            "message" => json_encode($message),
            "date" => $date,
            "type" => json_encode($type),
            "localmessageid" => json_encode($localmessageid)
        );
        return $this->execute($path, $params);
    }

    public function Status($messageid)
    {
        $path = $this->get_path("status");
        $params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        return $this->execute($path,$params);
    }

    public function StatusLocalMessageId($localid)
    {
        $path = $this->get_path("statuslocalmessageid");
        $params = array(
            "localid" => is_array($localid) ? implode(",", $localid) : $localid
        );
        return $this->execute($path, $params);
    }

    public function Select($messageid)
    {
        $params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        $path = $this->get_path("select");
        return $this->execute($path, $params);
    }

    public function SelectOutbox($startdate, $enddate, $sender)
    {
        $path   = $this->get_path("selectoutbox");
        $params = array(
            "startdate" => $startdate,
            "enddate" => $enddate,
            "sender" => $sender
        );
        return $this->execute($path, $params);
    }

    public function LatestOutbox($pagesize, $sender)
    {
        $path   = $this->get_path("latestoutbox");
        $params = array(
            "pagesize" => $pagesize,
            "sender" => $sender
        );
        return $this->execute($path, $params);
    }

    public function CountOutbox($startdate, $enddate, $status = 0)
    {
        $path   = $this->get_path("countoutbox");
        $params = array(
            "startdate" => $startdate,
            "enddate" => $enddate,
            "status" => $status
        );
        return $this->execute($path, $params);
    }

    public function Cancel($messageid)
    {
        $path = $this->get_path("cancel");
        $params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        return $this->execute($path,$params);

    }

    public function Receive($linenumber, $isread = 0)
    {
        $path   = $this->get_path("receive");
        $params = array(
            "linenumber" => $linenumber,
            "isread" => $isread
        );
        return $this->execute($path, $params);
    }

    public function CountInbox($startdate, $enddate, $linenumber, $isread = 0)
    {
        $path   = $this->get_path("countinbox");
        $params = array(
            "startdate" => $startdate,
            "enddate" => $enddate,
            "linenumber" => $linenumber,
            "isread" => $isread
        );
        return $this->execute($path, $params);
    }

    public function CountPostalcode($postalcode)
    {
        $path   = $this->get_path("countpostalcode");
        $params = array(
            "postalcode" => $postalcode
        );
        return $this->execute($path, $params);
    }

    public function SendbyPostalcode($sender,$postalcode,$message, $mcistartindex, $mcicount, $mtnstartindex, $mtncount, $date)
    {
        $path   = $this->get_path("sendbypostalcode");
        $params = array(
            "postalcode" => $postalcode,
            "sender" => $sender,
            "message" => $message,
            "mcistartindex" => $mcistartindex,
            "mcicount" => $mcicount,
            "mtnstartindex" => $mtnstartindex,
            "mtncount" => $mtncount,
            "date" => $date
        );
        return $this->execute($path, $params);
    }

    public function AccountInfo()
    {
        $path = $this->get_path("info", "account");
        return $this->execute($path);
    }

    public function AccountConfig($apilogs, $dailyreport, $debug, $defaultsender, $mincreditalarm, $resendfailed)
    {
        $path   = $this->get_path("config", "account");
        $params = array(
            "apilogs" => $apilogs,
            "dailyreport" => $dailyreport,
            "debug" => $debug,
            "defaultsender" => $defaultsender,
            "mincreditalarm" => $mincreditalarm,
            "resendfailed" => $resendfailed
        );
        return $this->execute($path, $params);
    }

    public function VerifyLookup($receptor, $token, $token2, $token3, $template, $type = null)
    {
        $path   = $this->get_path("lookup", "verify");
        $params = array(
            "receptor" => $receptor,
            "token" => $token,
            "token2" => $token2,
            "token3" => $token3,
            "template" => $template,
            "type" => $type
        );
        if(func_num_args()>5){
            $arg_list = func_get_args();
            if(isset($arg_list[6]))
                $params["token10"]=$arg_list[6];
            if(isset($arg_list[7]))
                $params["token20"]=$arg_list[7];
        }
        return $this->execute($path, $params);
    }

    public function CallMakeTTS($receptor, $message, $date = null, $localid = null)
    {
        $path   = $this->get_path("maketts", "call");
        $params = array(
            "receptor" => $receptor,
            "message" => $message,
            "date" => $date,
            "localid" => $localid
        );
        return $this->execute($path, $params);
    }
}