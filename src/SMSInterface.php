<?php

namespace Amani64\SMS;


interface SMSInterface
{
    public function send(string $to, string $message, string $type, string $sender=null);

}