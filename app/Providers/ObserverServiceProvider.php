<?php

namespace App\Providers;

use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\SmtpSetting;
use App\Models\File;
use App\Observers\EmailObserver;
use App\Observers\EmailTemplateObserver;
use App\Observers\SmtpSettingObserver;
use App\Observers\FileObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Email::observe(EmailObserver::class);
        EmailTemplate::observe(EmailTemplateObserver::class);
        SmtpSetting::observe(SmtpSettingObserver::class);
        File::observe(FileObserver::class);
    }
}
