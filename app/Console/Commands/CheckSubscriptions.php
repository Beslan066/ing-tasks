<?php

namespace App\Console\Commands;

use App\Services\PaymentService;
use Illuminate\Console\Command;

class CheckSubscriptions extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Check and update expired subscriptions';

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        parent::__construct();
        $this->paymentService = $paymentService;
    }

    public function handle()
    {
        $this->info('Checking subscriptions...');

        $processed = $this->paymentService->checkExpiredSubscriptions();

        $this->info("Processed {$processed} expired subscriptions");

        return Command::SUCCESS;
    }
}
