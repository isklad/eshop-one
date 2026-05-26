<?php
declare(strict_types=1);

namespace App\Repository;

use Isklad\MyorderCartWidgetMiddleware\Model\Address;

final class AddressRepository
{
    public static function getAddress(): Address
    {
        $address = new Address();
        $address->name = 'iSklad s.r.o.';
        $address->company = 'iSklad s.r.o.';
        $address->businessId = '12345678';
        $address->taxId = '2012345678';
        $address->vatId = 'SK2012345678';
        $address->street = 'Diaľničná cesta';
        $address->streetNumber = '5';
        $address->city = 'Senec';
        $address->postalCode = '90301';

        return $address;
    }
}