<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'colorName',
        'color', // এটি হবে HEX code field, যেমন #FF0000
        'status',
    ];

    /**
     * Relationship: A Color can be used in many products.
     */
    public function productColors()
    {
        return $this->hasMany(Productcolor::class, 'color_id', 'id');
    }
}
