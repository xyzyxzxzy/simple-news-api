<?php

namespace App\Form\Model\Tag;

class TagCreateModel
{
    public function __construct(
        public ?string $name = null,
    ) {}
}
