<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assignee_id',
        'title',
        'description',
        'status',
        'tags',
        'date',
        'completed_at'
    ];
    protected $primaryKey = 'id';
    protected $table = 'todos';

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'assignee_id', 'id');
    }
}
