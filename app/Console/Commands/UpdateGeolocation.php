<?php

namespace App\Console\Commands;

use App\Models\UserOnlineSession;
use App\Traits\GeolocationTrait;
use Illuminate\Console\Command;

class UpdateGeolocation extends Command
{
    use GeolocationTrait;

    protected $signature = 'geo:update';
    protected $description = 'Update geolocation for existing sessions';

    public function handle()
    {
        $sessions = UserOnlineSession::whereNull('latitude')
            ->orWhereNull('country')
            ->get();

        $bar = $this->output->createProgressBar($sessions->count());

        foreach ($sessions as $session) {
            if ($session->ip_address && !$this->isLocalIp($session->ip_address)) {
                $geoData = $this->getGeolocationByIp($session->ip_address);
                $session->update($geoData);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Geolocation updated for ' . $sessions->count() . ' sessions');
    }
}
