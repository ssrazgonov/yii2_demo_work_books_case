<?php

namespace app\dto;

readonly class AuthorDto
{
    public function __construct(
        public string $name,
    ) {}
}