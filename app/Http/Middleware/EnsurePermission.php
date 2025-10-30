<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission, string ...$permissions): Response
    {
        $user = $request->user();

        $requiredPermissions = collect([$permission, ...$permissions])
            ->flatMap(function (string $value): Collection {
                return collect(preg_split('/[|,]/', $value) ?: []);
            })
            ->map(fn (string $value): string => trim($value))
            ->filter()
            ->values();

        if (! $user || $requiredPermissions->isEmpty()) {
            abort(403, __('You do not have permission to perform this action.'));
        }

        $authorized = $requiredPermissions->contains(function (string $ability) use ($user): bool {
            return $user->can($ability);
        });

        if (! $authorized) {
            abort(403, __('You do not have permission to perform this action.'));
        }

        return $next($request);
    }
}
