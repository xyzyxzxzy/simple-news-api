<?php

namespace App\Form\Model;

class NewsFilterModel
{
    public function __construct(
        public ?int $pg = null,
        public ?int $on = null,
        public ?string $dateFilter = null,
        public ?array $tagIds = [],
    ) {}
}