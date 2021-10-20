<?php

namespace Andrijaj\DemoProject\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * @package Andrijaj\DemoProject\Model
 * @property int Id
 * @property int CategoryId
 * @property string SKU
 * @property string Title
 * @property string Brand
 * @property string Price
 * @property string ShortDescription
 * @property string Description
 * @property string Image
 * @property bool Enabled
 * @property bool Featured
 * @property int ViewCount
 * @property Category category
 */
class Product extends Model
{
    public $timestamps = false;
    protected $table = 'Product';
    protected $primaryKey = 'Id';

    public function category()
    {
        return $this->belongsTo(Category::class, 'CategoryId', 'Id');
    }
}