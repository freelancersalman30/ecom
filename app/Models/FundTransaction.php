<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundTransaction extends Model
{
    protected $fillable = [
        'direction', 'source', 'source_id', 'amount', 'note', 'created_by',
    ];
}
