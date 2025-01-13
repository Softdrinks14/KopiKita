<?php

use Fruitcake\Cors\HandleCors;
use App\Http\Middleware\AblePayOrder;
use Illuminate\Foundation\Application;
use App\Http\Middleware\AbleCreateUser;
use App\Http\Middleware\AbleCreateOrder;
use App\Http\Middleware\AbleFinishOrder;
use App\Http\Middleware\AbleCreateUpdateItem;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'verified' => EnsureEmailIsVerified::class,
            'ableCreateOrder' => AbleCreateOrder::class,
            'ableFinishOrder' => AbleFinishOrder::class,
            'ableCreateUser' => AbleCreateUser::class,
            'ableCreateUpdateItem' => AbleCreateUpdateItem::class,
            'ablePayOrder' => AblePayOrder::class,
        ]);
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,

            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
