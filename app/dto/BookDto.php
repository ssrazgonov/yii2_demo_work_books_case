<?php

namespace app\dto;

readonly class BookDto
{
    public function __construct(
        public string $title,
        public int $year,
        public ?string $description,
        public ?string $isbn,
        public ?string $cover_image,
        public array $authorIds = [],
    ) {}
}