<?php

namespace Andrijaj\DemoProject\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * @package Andrijaj\DemoProject\Model
 * @property int Id
 * @property int|null ParentId
 * @property string Code
 * @property string Title
 * @property string Description
 */
class Category extends Model
{
    public $timestamps = false;
    protected $table = 'Category';
    protected $primaryKey = 'Id';
    protected $fillable = ['ParentId', 'Code', 'Title', 'Description'];

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'ParentId', 'Id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'ParentId', 'Id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'CategoryId', 'Id');
    }
}