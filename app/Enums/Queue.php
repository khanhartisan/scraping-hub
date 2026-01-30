<?php

namespace App\Enums;

enum Queue: string
{
    case DEFAULT = 'default';
    case SCRAPING = 'scraping';

    /**
     * Dedicated queue for the scrape scheduler job. Run exactly one worker
     * for this queue so only one instance of ScheduleScrapeDueJob runs at a time.
     */
    case SCHEDULER = 'scheduler';
}
