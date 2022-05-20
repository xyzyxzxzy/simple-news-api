<?php

namespace App\Model;

use DateTime;

class NewsModel extends SerializerModel
{
    private $id;
    private $name;
    private $slug;
    private $content;
    private $preview;
    private $dateCreation;
    private $datePublication;
    private $tags;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPreview()
    {
        return $this->preview;
    }

    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    public function getDatePublication()
    {
        return $this->datePublication;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setId(int $id)
    {
        return $this->id = $id;
    }

    public function setName(string $name)
    {
        return $this->name = $name;
    }

    public function setSlug(string $slug)
    {
        return $this->slug = $slug;
    }

    public function setContent(string $content)
    {
        return $this->content = $content;
    }

    public function setPreview(string $preview)
    {
        return $this->preview = $preview;
    }
    
    public function setDateCreation(DateTime $dateCreation)
    {
        return $this->dateCreation = $dateCreation->format("d-m-Y");
    }

    public function setDatePublication(DateTime $datePublication)
    {
        return $this->datePublication = $datePublication->format("d-m-Y");
    }

    public function setTags($tags)
    {
        return $this->tags = $tags;
    }

    public function toArray()
    {
        return parent::serialize($this);
    }
}