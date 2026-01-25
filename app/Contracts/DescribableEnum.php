<?php

namespace App\Contracts;

interface DescribableEnum
{
    public static function describe(self $enum): string;
}