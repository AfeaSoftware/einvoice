<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera;

class Nilvera
{
    public static function make(string $token): Client
    {
        return Client::make($token);
    }

    public static function create(?string $token = null): Client
    {
        return Client::create($token);
    }

    public static function voucher(?string $token = null): Voucher\VoucherQuery
    {
        return self::create($token)->voucher();
    }

    public static function vouchers(?string $token = null): Voucher\VoucherQuery
    {
        return self::voucher($token);
    }

    public static function general(?string $token = null): General\GeneralClient
    {
        return self::create($token)->general();
    }
}
