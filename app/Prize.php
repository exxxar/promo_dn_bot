<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Prize extends Model
{
    use Sortable;

    public $sortable = [
        'id',
        'title',
        'description',
        'summary_activation_count',
        'current_activation_count',
        'company_id',
    ];

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'company_id',

        'summary_activation_count',
        'current_activation_count',

        'is_active',
        'updated_at',
        'created_at',
    ];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }
}
