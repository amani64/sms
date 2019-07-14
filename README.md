
# Laravel sms package

## Install: 
`composer require amani64/sms`

## Add Provider to app.php: 
`Amani64\SMS\SMSServiceProvider::class`

## publish
`php artisan vendor:publish`

## migrate
`php artisan migrate`

## Usage: 
`SMS::send(mobiles,message);`

## supported SMS service

- Kavenegar
- ISMS
- Candoo
