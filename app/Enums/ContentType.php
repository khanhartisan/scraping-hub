<?php

namespace App\Enums;

use App\Contracts\DescribableEnum;

enum ContentType: string implements DescribableEnum
{
    case ARTICLE = 'article';
    case PRODUCT = 'product';
    case JOB_POSTING = 'job_posting';
    case EVENT = 'event';
    case REVIEW = 'review';
    case MEDIA = 'media';
    case COURSE = 'course';
    case PROFILE = 'profile';
    case FAQ = 'faq';
    case DOCUMENT = 'document';
    case DISCUSSION = 'discussion';
    case RECIPE = 'recipe';
    case WEBINAR = 'webinar';

    case UNKNOWN = 'unknown';

    public static function describe(DescribableEnum $enum): string
    {
        return match ($enum) {
            self::ARTICLE => 'Article or editorial content.',
            self::PRODUCT => 'Product page or product catalog content.',
            self::JOB_POSTING => 'Job listing or career opportunity.',
            self::EVENT => 'Event listing or event details.',
            self::REVIEW => 'Review content (product, service, etc.).',
            self::MEDIA => 'Media content (video, audio, gallery, etc.).',
            self::COURSE => 'Course or educational content.',
            self::PROFILE => 'Person, company, or entity profile.',
            self::FAQ => 'Frequently asked questions content.',
            self::DOCUMENT => 'Document-style content (PDF, whitepaper, docs).',
            self::DISCUSSION => 'Discussion or forum thread content.',
            self::RECIPE => 'Recipe or cooking instructions content.',
            self::WEBINAR => 'Webinar content (live or recorded).',
            default => 'Unknown content type.'
        };
    }
}