
# Laravel sms package

## Install: 
`composer require amani64/sms`

## Add Provider to app.php: 
`Amani64\SMS\SMSServiceProvider::class`

## publish
`php artisan vendor:publish`

## migrate
`php artisan migrate`

## Set driver
in config/sms.php
<br >
`'driver' => 'isms'`
<br >
and for saving sent sms into database set `log_response` to `true`
<br >
`'log_response' => true`

## Usage: 
`SMS::send(mobiles,message);`

## supported SMS service

- Kavenegar
- ISMS
- Candoo
