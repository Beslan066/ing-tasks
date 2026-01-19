<?php

namespace App\Providers;

use App\Models\EmailTemplate;
use App\Models\SmtpSetting;
use App\Models\Email;
use App\Models\Tag;
use App\Models\File;
use App\Policies\EmailTemplatePolicy;
use App\Policies\SmtpSettingPolicy;
use App\Policies\EmailPolicy;
use App\Policies\TagPolicy;
use App\Policies\FilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class PolicyServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        EmailTemplate::class => EmailTemplatePolicy::class,
        SmtpSetting::class => SmtpSettingPolicy::class,
        Email::class => EmailPolicy::class,
        Tag::class => TagPolicy::class,
        File::class => FilePolicy::class,
    ];

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
        $this->registerPolicies();
    }
}
