<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'term_taxonomy_id';

    protected $table = 'qwcheq_term_taxonomy';

    protected $fillable = [
        "term_id",
        "taxonomy",
        "description",
        "parent",
        "count",
    ];

}
