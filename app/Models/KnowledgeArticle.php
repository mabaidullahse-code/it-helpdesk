<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'summary',
        'content',
        'keywords',
        'views_count',
        'is_published',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title) . '-' . Str::random(5);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
