<?php

namespace App\Form\Model\News;

class NewsCreateModel
{
    public function __construct(
        public ?string $name = null,
        public ?string $content = null,
        public ?string $preview = null,
        public ?array $tags = null,
    ) {}
}
