<?php

namespace App\Models;

use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NickDeKruijk\Leap\Traits\HasDocumentMeta;
use NickDeKruijk\Leap\Traits\HasMedia;
use NickDeKruijk\Leap\Traits\HasSections;
use NickDeKruijk\Leap\Traits\HasSlug;
use Spatie\Translatable\HasTranslations;

/**
 * News: a dated content item, listed newest first, only shown once its
 * publication date has passed. A card in a row, a filterable overview of its own, and
 * a section-based detail page each.
 */
class News extends Model
{
    use HasDocumentMeta;
    use HasFactory;
    use HasMedia;
    use HasSections;
    use HasSlug;
    use HasTags;
    use HasTranslations;
    use SoftDeletes;

    protected $table = 'news';

    public $translatable = ['title', 'html_title', 'slug', 'intro', 'description', 'meta'];

    protected $casts = [
        'active' => 'boolean',
        'published_at' => 'datetime',
        'sections' => 'array',
        'meta' => 'array',
    ];

    /**
     * Only items whose publication moment has passed — the frontend never shows a
     * future-dated item, so an editor can stage one and it appears on its own.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now());
    }

    /**
     * The frontend set: active, published, newest first.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)->published()->orderByDesc('published_at')->orderByDesc('id');
    }
}
