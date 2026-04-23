<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get a legal page by slug
     */
    public static function getBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Update content by slug
     */
    public static function updateBySlug($slug, $title, $content)
    {
        $page = static::where('slug', $slug)->first();
        if ($page) {
            $page->update([
                'title' => $title,
                'content' => $content
            ]);
            return $page;
        }
        return null;
    }
}