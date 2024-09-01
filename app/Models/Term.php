<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'term_id';

    protected $table = 'qwcheq_terms';

    protected $fillable = [
        "name",
        "slug",
        "term_group",
        "parent_slug",
    ];

}
