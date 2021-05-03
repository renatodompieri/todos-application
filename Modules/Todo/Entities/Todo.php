<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'date',
        'completed_at'
    ];
    protected $primaryKey = 'id';
    protected $table = 'todos';

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeFilterByTitleOrDescription($q, $keyword = null)
    {
        if (!$keyword) {
            return $q;
        }

        return $q->where('title', 'like', '%' . $keyword . '%')->orWhere('description', 'like', '%' . $keyword . '%');
    }

    public function scopeFilterCompletedTodo($q, $status = null)
    {
        if (!$status) {
            return $q;
        }

        return $q->whereStatus(1);
    }

    public function scopeFilterByUserId($q, $user_id = null)
    {
        if (!$user_id) {
            return $q;
        }

        return $q->whereUserId($user_id);
    }

    public function scopeDateBetween($q, $dates)
    {
        if ((!$dates['start_date'] || !$dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('date', '>=', getStartOfDate($dates['start_date']))->where('date', '<=', getEndOfDate($dates['end_date']));
    }
}
