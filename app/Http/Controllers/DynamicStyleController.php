<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DynamicStyleController extends Controller
{
    /**
     * Genera il CSS dinamico basato sui settings del database
     */
    public function generateDynamicCSS()
    {
        $colors = [
            'primary_color' => Setting::getValue('primary_color', '#dc2626'),
            'primary_color_light' => Setting::getValue('primary_color_light', '#ef4444'),
            'primary_color_dark' => Setting::getValue('primary_color_dark', '#b91c1c'),
            'accent_color' => Setting::getValue('accent_color', '#dc2626'),
            'accent_color_light' => Setting::getValue('accent_color_light', '#ef4444'),
            'accent_color_dark' => Setting::getValue('accent_color_dark', '#b91c1c'),
        ];

        $css = $this->generateCSSVariables($colors);

        return Response::make($css, 200, [
            'Content-Type' => 'text/css',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    /**
     * Genera le variabili CSS basate sui colori
     */
    private function generateCSSVariables($colors)
    {
        $primary = $colors['primary_color'];
        $primaryLight = $colors['primary_color_light'];
        $primaryDark = $colors['primary_color_dark'];
        $accent = $colors['accent_color'];
        $accentLight = $colors['accent_color_light'];
        $accentDark = $colors['accent_color_dark'];

        // Converti i colori in RGB
        $primaryRgb = $this->hexToRgb($primary);
        $primaryLightRgb = $this->hexToRgb($primaryLight);
        $primaryDarkRgb = $this->hexToRgb($primaryDark);
        $accentRgb = $this->hexToRgb($accent);
        $accentLightRgb = $this->hexToRgb($accentLight);
        $accentDarkRgb = $this->hexToRgb($accentDark);

        return <<<CSS
:root {
    /* Colori Primari */
    --primary-color: {$primary};
    --primary-color-light: {$primaryLight};
    --primary-color-dark: {$primaryDark};
    --primary-rgb: {$primaryRgb};
    --primary-light-rgb: {$primaryLightRgb};
    --primary-dark-rgb: {$primaryDarkRgb};
    
    /* Colori di Accento */
    --accent-color: {$accent};
    --accent-color-light: {$accentLight};
    --accent-color-dark: {$accentDark};
    --accent-rgb: {$accentRgb};
    --accent-light-rgb: {$accentLightRgb};
    --accent-dark-rgb: {$accentDarkRgb};
    
    /* Gradients */
    --primary-gradient: linear-gradient(to bottom right, {$primary}, {$primaryDark});
    --accent-gradient: linear-gradient(to bottom right, {$accent}, {$accentDark});
    
    /* Shadow colors con opacità */
    --primary-shadow: rgba({$primaryRgb}, 0.1);
    --primary-shadow-lg: rgba({$primaryRgb}, 0.25);
    --accent-shadow: rgba({$accentRgb}, 0.1);
    --accent-shadow-lg: rgba({$accentRgb}, 0.25);
    
    /* Focus ring colors */
    --primary-focus: rgba({$primaryLightRgb}, 0.5);
    --accent-focus: rgba({$accentLightRgb}, 0.5);
}

/* Classi di utilità per i colori dinamici */
.bg-primary { background-color: var(--primary-color); }
.bg-primary-light { background-color: var(--primary-color-light); }
.bg-primary-dark { background-color: var(--primary-color-dark); }
.bg-accent { background-color: var(--accent-color); }
.bg-accent-light { background-color: var(--accent-color-light); }
.bg-accent-dark { background-color: var(--accent-color-dark); }

.text-primary { color: var(--primary-color); }
.text-primary-light { color: var(--primary-color-light); }
.text-primary-dark { color: var(--primary-color-dark); }
.text-accent { color: var(--accent-color); }
.text-accent-light { color: var(--accent-color-light); }
.text-accent-dark { color: var(--accent-color-dark); }

.border-primary { border-color: var(--primary-color); }
.border-primary-light { border-color: var(--primary-color-light); }
.border-primary-dark { border-color: var(--primary-color-dark); }
.border-accent { border-color: var(--accent-color); }
.border-accent-light { border-color: var(--accent-color-light); }
.border-accent-dark { border-color: var(--accent-color-dark); }

/* Focus states con colori dinamici */
.focus\:ring-primary:focus { 
    --tw-ring-color: var(--primary-color-light); 
    --tw-ring-opacity: 0.5;
}
.focus\:ring-accent:focus { 
    --tw-ring-color: var(--accent-color-light); 
    --tw-ring-opacity: 0.5;
}

/* Hover states */
.hover\:bg-primary:hover { background-color: var(--primary-color-dark); }
.hover\:bg-primary-light:hover { background-color: var(--primary-color); }
.hover\:bg-accent:hover { background-color: var(--accent-color-dark); }
.hover\:bg-accent-light:hover { background-color: var(--accent-color); }

.hover\:text-primary:hover { color: var(--primary-color-dark); }
.hover\:text-accent:hover { color: var(--accent-color-dark); }

/* Gradient backgrounds */
.bg-primary-gradient { background: var(--primary-gradient); }
.bg-accent-gradient { background: var(--accent-gradient); }

/* Text gradients */
.text-primary-gradient { 
    background: var(--primary-gradient); 
    -webkit-background-clip: text; 
    -webkit-text-fill-color: transparent; 
    background-clip: text; 
}
.text-accent-gradient { 
    background: var(--accent-gradient); 
    -webkit-background-clip: text; 
    -webkit-text-fill-color: transparent; 
    background-clip: text; 
}

/* Shadows con colori dinamici */
.shadow-primary { 
    box-shadow: 0 4px 6px -1px var(--primary-shadow), 0 2px 4px -1px var(--primary-shadow); 
}
.shadow-primary-lg { 
    box-shadow: 0 10px 15px -3px var(--primary-shadow-lg), 0 4px 6px -2px var(--primary-shadow); 
}
.shadow-accent { 
    box-shadow: 0 4px 6px -1px var(--accent-shadow), 0 2px 4px -1px var(--accent-shadow); 
}
.shadow-accent-lg { 
    box-shadow: 0 10px 15px -3px var(--accent-shadow-lg), 0 4px 6px -2px var(--accent-shadow); 
}

/* Custom scrollbar */
.scrollbar-primary::-webkit-scrollbar-track { background: #f1f5f9; }
.scrollbar-primary::-webkit-scrollbar-thumb { 
    background: var(--primary-color); 
    border-radius: 0.375rem; 
}
.scrollbar-primary::-webkit-scrollbar-thumb:hover { 
    background: var(--primary-color-dark); 
}

/* Dark mode scrollbar */
.dark .scrollbar-primary::-webkit-scrollbar-track { background: #374151; }

/* Pulsanti personalizzati */
.btn-primary {
    background-color: var(--primary-color);
    color: white;
    transition: all 0.2s ease-in-out;
}
.btn-primary:hover {
    background-color: var(--primary-color-dark);
    transform: translateY(-1px);
}
.btn-primary:focus {
    outline: none;
    box-shadow: 0 0 0 3px var(--primary-focus);
}

.btn-accent {
    background-color: var(--accent-color);
    color: white;
    transition: all 0.2s ease-in-out;
}
.btn-accent:hover {
    background-color: var(--accent-color-dark);
    transform: translateY(-1px);
}
.btn-accent:focus {
    outline: none;
    box-shadow: 0 0 0 3px var(--accent-focus);
}

/* Badge e indicatori */
.badge-primary {
    background-color: var(--primary-color);
    color: white;
}
.badge-accent {
    background-color: var(--accent-color);
    color: white;
}

/* Link styles */
a.text-primary:hover {
    color: var(--primary-color-dark);
}
a.text-accent:hover {
    color: var(--accent-color-dark);
}

/* Input focus states */
input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
input[type="color"]:focus,
textarea:focus,
select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px var(--primary-focus);
}

/* Checkbox e radio custom */
input[type="checkbox"]:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}
input[type="radio"]:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Override system per sostituire automaticamente classi rosso */
.bg-red-50 { background-color: rgba(var(--primary-rgb), 0.05) !important; }
.bg-red-100 { background-color: rgba(var(--primary-rgb), 0.1) !important; }
.bg-red-200 { background-color: rgba(var(--primary-rgb), 0.2) !important; }
.bg-red-300 { background-color: rgba(var(--primary-rgb), 0.3) !important; }
.bg-red-400 { background-color: rgba(var(--primary-rgb), 0.4) !important; }
.bg-red-500 { background-color: var(--primary-color) !important; }
.bg-red-600 { background-color: var(--primary-color) !important; }
.bg-red-700 { background-color: var(--primary-color-dark) !important; }
.bg-red-800 { background-color: var(--primary-color-dark) !important; }
.bg-red-900 { background-color: var(--primary-color-dark) !important; }

.text-red-400 { color: var(--primary-color-light) !important; }
.text-red-500 { color: var(--primary-color) !important; }
.text-red-600 { color: var(--primary-color) !important; }
.text-red-700 { color: var(--primary-color-dark) !important; }
.text-red-800 { color: var(--primary-color-dark) !important; }
.text-red-900 { color: var(--primary-color-dark) !important; }

.border-red-200 { border-color: rgba(var(--primary-rgb), 0.3) !important; }
.border-red-300 { border-color: rgba(var(--primary-rgb), 0.4) !important; }
.border-red-400 { border-color: rgba(var(--primary-rgb), 0.5) !important; }
.border-red-500 { border-color: var(--primary-color) !important; }
.border-red-600 { border-color: var(--primary-color) !important; }
.border-red-700 { border-color: var(--primary-color-dark) !important; }

.hover\:bg-red-50:hover { background-color: rgba(var(--primary-rgb), 0.05) !important; }
.hover\:bg-red-100:hover { background-color: rgba(var(--primary-rgb), 0.1) !important; }
.hover\:bg-red-200:hover { background-color: rgba(var(--primary-rgb), 0.2) !important; }
.hover\:bg-red-400:hover { background-color: var(--primary-color-light) !important; }
.hover\:bg-red-600:hover { background-color: var(--primary-color) !important; }
.hover\:bg-red-700:hover { background-color: var(--primary-color-dark) !important; }
.hover\:bg-red-800:hover { background-color: var(--primary-color-dark) !important; }
.hover\:bg-red-900:hover { background-color: var(--primary-color-dark) !important; }

.hover\:text-red-400:hover { color: var(--primary-color-light) !important; }
.hover\:text-red-500:hover { color: var(--primary-color) !important; }
.hover\:text-red-600:hover { color: var(--primary-color) !important; }
.hover\:text-red-700:hover { color: var(--primary-color-dark) !important; }
.hover\:text-red-800:hover { color: var(--primary-color-dark) !important; }
.hover\:text-red-900:hover { color: var(--primary-color-dark) !important; }

.focus\:ring-red-200:focus { --tw-ring-color: rgba(var(--primary-rgb), 0.3) !important; }
.focus\:ring-red-500:focus { --tw-ring-color: var(--primary-color) !important; }
.focus\:ring-red-600:focus { --tw-ring-color: var(--primary-color) !important; }

.focus\:border-red-300:focus { border-color: rgba(var(--primary-rgb), 0.4) !important; }
.focus\:border-red-500:focus { border-color: var(--primary-color) !important; }
.focus\:border-red-600:focus { border-color: var(--primary-color) !important; }

.from-red-400 { --tw-gradient-from: var(--primary-color-light) !important; }
.from-red-500 { --tw-gradient-from: var(--primary-color) !important; }
.from-red-600 { --tw-gradient-from: var(--primary-color) !important; }
.from-red-700 { --tw-gradient-from: var(--primary-color-dark) !important; }
.from-red-800 { --tw-gradient-from: var(--primary-color-dark) !important; }
.to-red-500 { --tw-gradient-to: var(--primary-color) !important; }
.to-red-600 { --tw-gradient-to: var(--primary-color) !important; }
.to-red-700 { --tw-gradient-to: var(--primary-color-dark) !important; }
.to-red-800 { --tw-gradient-to: var(--primary-color-dark) !important; }
.to-pink-400 { --tw-gradient-to: var(--primary-color-light) !important; }
.to-pink-800 { --tw-gradient-to: var(--primary-color-dark) !important; }

.hover\:from-red-400:hover { --tw-gradient-from: var(--primary-color-light) !important; }
.hover\:from-red-500:hover { --tw-gradient-from: var(--primary-color) !important; }
.hover\:to-red-500:hover { --tw-gradient-to: var(--primary-color) !important; }
.hover\:to-red-600:hover { --tw-gradient-to: var(--primary-color) !important; }
.hover\:to-red-700:hover { --tw-gradient-to: var(--primary-color-dark) !important; }

.accent-red-600 { accent-color: var(--primary-color) !important; }
CSS;
    }

    /**
     * Converte un colore esadecimale in formato rgb()
     */
    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) .
                str_repeat(substr($hex, 1, 1), 2) .
                str_repeat(substr($hex, 2, 1), 2);
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "$r, $g, $b";
    }
}
