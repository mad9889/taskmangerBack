<?php 
return [
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    'check.token.expiry' => \App\Http\Middleware\CheckTokenExpiry::class,

],
];
