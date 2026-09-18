<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // ១. ប្រសិនបើមិនទាន់ Login ត្រូវបញ្ជូនទៅទំព័រ Login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // ២. ប្រសិនបើជា Admin គឺអនុញ្ញាតឱ្យចូលគ្រប់ផ្នែកទាំងអស់ដោយស្វ័យប្រវត្ត
        if ($user->isAdmin()) {
            return $next($request);
        }

        // ៣. ពិនិត្យមើលសិទ្ធិរបស់គណនី (រួមបញ្ចូលទាំងការគាំទ្រតួនាទី 'user' ស្មើនឹង 'cashier' ផងដែរ)
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
            if ($role === 'owner' && $user->role === 'shop_owner') {
                return $next($request);
            }
            if ($role === 'cashier' && $user->role === 'user') {
                return $next($request);
            }
        }

        // ៤. បដិសេធ ប្រសិនបើគ្មានសិទ្ធិ
        abort(403, 'អ្នកមិនមានសិទ្ធិចូលទៅកាន់ផ្នែកនេះឡើយ។');
    }
}
