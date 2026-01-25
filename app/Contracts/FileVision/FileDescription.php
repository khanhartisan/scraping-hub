<?php

namespace App\Contracts\FileVision;

final class FileDescription
{
    protected string $filePath;

    protected string $extension;

    protected string $mimeType;

    protected string $description;

    /**
     * @var array<int, string>
     */
    protected array $labels;

    protected float $confidence;

    /**
     * @var array<string, mixed>
     */
    protected array $metadata;

    public function getFilePath(): ?string
    {
        return $this->filePath ?? null;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension ?? null;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType ?? null;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getLabels(): array
    {
        return $this->labels ?? [];
    }

    /**
     * @param array<int, string> $labels
     */
    public function setLabels(array $labels): static
    {
        $this->labels = $labels;
        return $this;
    }

    public function getConfidence(): ?float
    {
        return $this->confidence ?? null;
    }

    public function setConfidence(float $confidence): static
    {
        $this->confidence = $confidence;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata ?? [];
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }
}