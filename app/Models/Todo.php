<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Todo extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_ON_HOLD = 'on_hold';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_CRITICAL = 'critical';

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'user_id',
        'creator_id',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'Новая',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_DONE => 'Выполнена',
            self::STATUS_ON_HOLD => 'В ожидании',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Низкий',
            self::PRIORITY_MEDIUM => 'Средний',
            self::PRIORITY_HIGH => 'Высокий',
            self::PRIORITY_CRITICAL => 'Критический',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TodoComment::class)->orderBy('created_at', 'asc');
    }
}