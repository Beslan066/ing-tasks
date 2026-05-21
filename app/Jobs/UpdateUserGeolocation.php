<?php

namespace App\Jobs;

use App\Models\UserSession;
use App\Traits\DeviceInfoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserGeolocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, DeviceInfoTrait;

    protected $session;

    public function __construct(UserSession $session)
    {
        $this->session = $session;
    }

    public function handle()
    {
        $geoInfo = $this->getGeolocation($this->session->ip_address);
        $this->session->update($geoInfo);
    }
}
