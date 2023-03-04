<?php

namespace App\Form\Model\Tag;

class TagUpdateModel
{
    public function __construct(
        public ?string $name = null,
    ) {}
}
