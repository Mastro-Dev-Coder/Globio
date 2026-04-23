<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserProfile;

class ChannelHelper
{
    /**
     * Ottiene l'username di un utente per i link ai canali
     */
    public static function getChannelUsername($user): ?string
    {
        if ($user instanceof User) {
            return $user->userProfile?->username;
        }
        
        if ($user instanceof UserProfile) {
            return $user->username;
        }
        
        if (is_string($user)) {
            return $user;
        }
        
        return null;
    }
    
    /**
     * Genera l'URL del canale per un utente
     */
    public static function getChannelUrl($user): ?string
    {
        $username = static::getChannelUsername($user);
        
        if ($username) {
            return route('channel.show', $username);
        }
        
        return null;
    }
    
    /**
     * Mappa dei codici paese con le rispettive bandiere emoji
     */
    private static $countryFlags = [
        'IT' => '🇮🇹', 'US' => '🇺🇸', 'FR' => '🇫🇷', 'DE' => '🇩🇪', 'ES' => '🇪🇸',
        'UK' => '🇬🇧', 'RU' => '🇷🇺', 'CN' => '🇨🇳', 'JP' => '🇯🇵', 'KR' => '🇰🇷',
        'CA' => '🇨🇦', 'AU' => '🇦🇺', 'BR' => '🇧🇷', 'IN' => '🇮🇳', 'MX' => '🇲🇽',
        'AR' => '🇦🇷', 'CL' => '🇨🇱', 'CO' => '🇨🇴', 'PE' => '🇵🇪', 'VE' => '🇻🇪',
        'NL' => '🇳🇱', 'BE' => '🇧🇪', 'CH' => '🇨🇭', 'AT' => '🇦🇹', 'SE' => '🇸🇪',
        'NO' => '🇳🇴', 'DK' => '🇩🇰', 'FI' => '🇫🇮', 'PL' => '🇵🇱', 'CZ' => '🇨🇿',
        'HU' => '🇭🇺', 'RO' => '🇷🇴', 'BG' => '🇧🇬', 'GR' => '🇬🇷', 'PT' => '🇵🇹',
        'IE' => '🇮🇪', 'LU' => '🇱🇺', 'LT' => '🇱🇹', 'LV' => '🇱🇻', 'EE' => '🇪🇪',
        'SI' => '🇸🇮', 'SK' => '🇸🇰', 'MT' => '🇲🇹', 'CY' => '🇨🇾', 'HR' => '🇭🇷'
    ];
    
    /**
     * Mappa dei codici paese con i rispettivi nomi in italiano
     */
    private static $countryNames = [
        'IT' => 'Italia', 'US' => 'Stati Uniti', 'FR' => 'Francia', 'DE' => 'Germania', 'ES' => 'Spagna',
        'UK' => 'Regno Unito', 'RU' => 'Russia', 'CN' => 'Cina', 'JP' => 'Giappone', 'KR' => 'Corea del Sud',
        'CA' => 'Canada', 'AU' => 'Australia', 'BR' => 'Brasile', 'IN' => 'India', 'MX' => 'Messico',
        'AR' => 'Argentina', 'CL' => 'Cile', 'CO' => 'Colombia', 'PE' => 'Perù', 'VE' => 'Venezuela',
        'NL' => 'Paesi Bassi', 'BE' => 'Belgio', 'CH' => 'Svizzera', 'AT' => 'Austria', 'SE' => 'Svezia',
        'NO' => 'Norvegia', 'DK' => 'Danimarca', 'FI' => 'Finlandia', 'PL' => 'Polonia', 'CZ' => 'Repubblica Ceca',
        'HU' => 'Ungheria', 'RO' => 'Romania', 'BG' => 'Bulgaria', 'GR' => 'Grecia', 'PT' => 'Portogallo',
        'IE' => 'Irlanda', 'LU' => 'Lussemburgo', 'LT' => 'Lituania', 'LV' => 'Lettonia', 'EE' => 'Estonia',
        'SI' => 'Slovenia', 'SK' => 'Slovacchia', 'MT' => 'Malta', 'CY' => 'Cipro', 'HR' => 'Croazia'
    ];

    /**
     * Ottiene la bandiera emoji per un codice paese
     */
    public static function countryFlag($countryCode): string
    {
        return self::$countryFlags[strtoupper($countryCode)] ?? '🏳️';
    }
    
    /**
     * Ottiene il nome del paese per un codice paese
     */
    public static function countryName($countryCode): string
    {
        return self::$countryNames[strtoupper($countryCode)] ?? 'Sconosciuto';
    }
}