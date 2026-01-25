<?php

namespace App\Contracts\PageParser;

interface Parser
{
    public function parse(string $html): PageData;
}