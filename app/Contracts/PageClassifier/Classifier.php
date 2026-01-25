<?php

namespace App\Contracts\PageClassifier;

interface Classifier
{
    public function classify(string $html): ClassificationResult;
}