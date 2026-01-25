<?php

namespace App\Enums;

enum ScrapingStatus: int
{
    case PENDING = 0;
    case QUEUED = 1;
    case FETCHING = 2;
    case SUCCESS = 3;
    case FAILED = 4;
    case TIMEOUT = 5;
    case BLOCKED = 6;
}