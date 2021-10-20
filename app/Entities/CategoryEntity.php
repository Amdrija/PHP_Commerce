<?php

namespace Andrijaj\DemoProject\Entities;

class CategoryEntity
{
    public int $id;
    public string $title;
    public string $code;
    public string $description;
    public string $parentTitle;
    public int $parentId;
    public array $subcategories;

    public function __construct(
        int $id,
        string $title,
        string $code,
        string $description,
        string $parentTitle,
        int $parentId,
        array $subcategories
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->code = $code;
        $this->description = $description;
        $this->parentTitle = $parentTitle;
        $this->parentId = $parentId;
        $this->subcategories = $subcategories;
    }
}