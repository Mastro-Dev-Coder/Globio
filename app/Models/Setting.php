<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];



    public static function getValue(string $key, $default = null)
    {
        $value = self::where('key', $key)->value('value');

        return $value !== null ? trim($value) : $default;
    }

    /**
     * Ottiene valore setting come booleano
     * Converte '1', 'true', 'yes' in true, tutto il resto in false
     */
    public static function getBooleanValue(string $key, $default = false): bool
    {
        $value = self::getValue($key, $default ? '1' : '0');
        
        if (is_bool($value)) {
            return $value;
        }
        
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'yes', 'on']);
    }
    
    /**
     * Imposta valore setting
     */
    public static function setValue(string $key, $value, string $type = 'string')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    

}
