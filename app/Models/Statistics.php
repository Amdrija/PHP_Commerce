<?php

namespace Andrijaj\DemoProject\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Statistics
 * @package Andrijaj\DemoProject\Model
 * @property int Id
 * @property int HomeViewCount
 */
class Statistics extends Model
{
    public $timestamps = false;
    protected $table = 'Statistics';
    protected $primaryKey = 'Id';
}