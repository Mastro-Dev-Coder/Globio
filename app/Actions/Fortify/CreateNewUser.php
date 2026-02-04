<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $role = User::count() === 0 ? 'admin' : 'user';

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $role,
        ]);

        // Crea automaticamente il canale (UserProfile) con dati di default
        $this->createUserProfile($user, $input);

        return $user;
    }

    /**
     * Crea automaticamente il profilo utente (canale) con dati di default
     */
    private function createUserProfile(User $user, array $input): void
    {
        $username = $this->generateUniqueUsername($input['name']);
        
        UserProfile::create([
            'user_id' => $user->id,
            'username' => $username,
            'channel_name' => $input['name'] . ' Channel',
            'channel_description' => 'Benvenuto nel mio canale! Qui troverai i miei contenuti video.',
            'avatar_url' => null, // Potrai caricare un avatar personalizzato
            'banner_url' => null, // Potrai caricare un banner personalizzato
            'subscriber_count' => 0,
            'video_count' => 0,
            'total_views' => 0,
            'is_verified' => false,
            'is_channel_enabled' => true,
            'social_links' => [],
            'country' => 'IT',
            'channel_created_at' => now(),
        ]);
    }

    /**
     * Genera un username unico dal nome dell'utente
     */
    private function generateUniqueUsername(string $name): string
    {
        // Converte il nome in username (lowercase, rimuove spazi e caratteri speciali)
        $baseUsername = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', $name)));
        $baseUsername = trim($baseUsername, '_');
        
        // Se è troppo corto, aggiunge numeri casuali
        if (strlen($baseUsername) < 3) {
            $baseUsername .= rand(100, 999);
        }
        
        $username = $baseUsername;
        $counter = 1;
        
        // Assicura che l'username sia unico
        while (UserProfile::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }
        
        return $username;
    }
}
