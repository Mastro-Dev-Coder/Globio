<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class TranslationsController extends Controller
{
    public function getTranslations($locale)
    {
        if (!in_array($locale, ['it', 'en', 'es'])) {
            return response()->json(['error' => 'Invalid locale'], 400);
        }

        // Get UI translations
        $translationsPath = resource_path("lang/{$locale}/ui.php");
        if (File::exists($translationsPath)) {
            $translations = File::getRequire($translationsPath);
            return response()->json([
                'locale' => $locale,
                'translations' => $translations
            ]);
        }

        return response()->json(['error' => 'Translations not found'], 404);
    }
}
