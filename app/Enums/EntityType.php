<?php

namespace App\Enums;

enum EntityType: int
{
    case UNCLASSIFIED = 0;
    case PAGE = 1;
    case IMAGE = 2;
    case VIDEO = 3;
    case DOCUMENT = 4;
    case UNKNOWN = 5;
}