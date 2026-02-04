<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ottiene una preferenza specifica per un utente
     */
    public static function getValue($userId, $category, $key, $default = null)
    {
        $preference = self::where('user_id', $userId)
            ->where('category', $category)
            ->where('key', $key)
            ->first();

        return $preference ? $preference->value : $default;
    }

    /**
     * Imposta una preferenza specifica per un utente
     */
    public static function setValue($userId, $category, $key, $value)
    {
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'category' => $category,
                'key' => $key,
            ],
            ['value' => $value]
        );
    }

    /**
     * Ottiene tutte le preferenze di una categoria per un utente
     */
    public static function getCategoryPreferences($userId, $category)
    {
        return self::where('user_id', $userId)
            ->where('category', $category)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Imposta multiple preferenze per una categoria
     */
    public static function setCategoryPreferences($userId, $category, array $preferences)
    {
        foreach ($preferences as $key => $value) {
            self::setValue($userId, $category, $key, $value);
        }
    }
}