<?php

namespace App\Model;

class TagModel extends SerializerModel
{
    private $id;
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setId(int $id)
    {
        return $this->id = $id;
    }

    public function setName(string $name)
    {
        return $this->name = $name;
    }

    public function toArray()
    {
        return parent::serialize($this);
    }
}