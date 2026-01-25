<?php

namespace App\Contracts;

interface Describable
{
    public function getDescription(): ?string;

    public function setDescription(?string $description): static;
}