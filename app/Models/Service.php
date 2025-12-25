<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    // A service has many products (e.g. Surveillance sets)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // A service has many configurator criteria (e.g. types of cameras, room sizes)
    public function configuratorCriteria()
    {
        return $this->hasMany(ConfiguratorCriteria::class);
    }
}
