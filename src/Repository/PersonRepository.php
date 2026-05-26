<?php
declare(strict_types=1);

namespace App\Repository;

use Isklad\MyorderCartWidgetMiddleware\Model\Person;

final class PersonRepository
{
    public static function getPerson(): Person
    {
        $person = new Person();
        $person->name = 'Ján';
        $person->surname = 'Novák';
        $person->email = 'jan.novak@isklad.eu';
        $person->phone = '+421908111222';
        $person->phoneRegion = 'SK';

        return $person;
    }
}
