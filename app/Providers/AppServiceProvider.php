<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Di Vercel filesystem bersifat read-only, hanya /tmp yang bisa ditulis.
        if (env('VERCEL')) {
            $fontDir = '/tmp/fonts';

            if (! is_dir($fontDir)) {
                @mkdir($fontDir, 0777, true);
            }

            config([
                'dompdf.font_dir'   => $fontDir,
                'dompdf.font_cache' => $fontDir,
            ]);
        }
    }
}
