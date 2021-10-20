<?php
/**
 * Admin model for communicating with the Admin table in the database.
 */

namespace Andrijaj\DemoProject\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string Password
 * @property int Id
 * @property string Token
 */
class Admin extends Model
{
    public $timestamps = false;
    protected $table = 'Admin';
    protected $primaryKey = 'Id';
}