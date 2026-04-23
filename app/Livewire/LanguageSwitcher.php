<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\App;

class LanguageSwitcher extends Component
{
    public $showDropdown = false;

    public function mount()
    {
        // La lingua corrente viene presa direttamente da app()->getLocale()
    }

    public function switchLocale($locale)
    {
        if (in_array($locale, ['it', 'en', 'es'])) {
            // Store in session
            Session::put('locale', $locale);
            
            // Store in cookie for 1 year
            Cookie::queue('locale', $locale, 60 * 24 * 365);
            
            // Set application locale
            App::setLocale($locale);
            
            $this->showDropdown = false;
            
            // Reload page to apply changes
            return redirect(request()->header('referer') ?? url()->previous() ?? route('home'));
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
