<?php

namespace MichaelKaefer\AdminUiBundle\Media;

interface MediaInterface
{
    public function getFilename(): ?string;
    public function setFilename(string $filename): static;
    public function setOriginalFilename(string $originalFilename): static;
    public function setMimeType(string $mimeType): static;
    public function setSize(int $size): static;
}
