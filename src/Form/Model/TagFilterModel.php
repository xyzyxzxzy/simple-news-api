<?php

namespace App\Form\Model;

class TagFilterModel
{
    public function __construct(
        public ?int $pg = null,
        public ?int $on = null,
    ) {}
}