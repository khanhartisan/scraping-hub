<?php

namespace App\Contracts\PageClassifier;

use App\Enums\ContentType;
use App\Enums\PageType;
use App\Enums\Temporal;

final class ClassificationResult
{
    protected ContentType $contentType;

    protected PageType $pageType;

    protected Temporal $temporal;

    public function getContentType(): ?ContentType
    {
        return $this->contentType ?? null;
    }

    public function setContentType(ContentType $contentType): static
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getPageType(): ?PageType
    {
        return $this->pageType ?? null;
    }

    public function setPageType(PageType $pageType): static
    {
        $this->pageType = $pageType;
        return $this;
    }

    public function getTemporal(): ?Temporal
    {
        return $this->temporal ?? null;
    }

    public function setTemporal(Temporal $temporal): static
    {
        $this->temporal = $temporal;
        return $this;
    }
}