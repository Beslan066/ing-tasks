<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\StorageUsage;
use App\Services\StorageService;

class CheckStorageLimits extends Command
{
    protected $signature = 'storage:check-limits';
    protected $description = 'Проверка и синхронизация лимитов хранилища компаний';

    protected $storageService;

    public function __construct(StorageService $storageService)
    {
        parent::__construct();
        $this->storageService = $storageService;
    }

    public function handle()
    {
        $this->info('Начинаем проверку лимитов хранилища...');

        $companies = Company::all();
        $updated = 0;
        $errors = 0;

        foreach ($companies as $company) {
            try {
                // Инициализируем хранилище для компании, если его нет
                $storageUsage = $this->storageService->initializeCompanyStorage($company);

                // Синхронизируем лимит с текущим тарифом
                $licenseType = $company->license_type ?? 'basic';
                $expectedLimit = $this->storageService::LICENSE_TYPES[$licenseType]['limit'];

                if ($storageUsage->total_storage_limit != $expectedLimit) {
                    $storageUsage->total_storage_limit = $expectedLimit;
                    $storageUsage->license_type = $licenseType;
                    $storageUsage->save();

                    $this->info("Обновлен лимит для компании {$company->name}: {$expectedLimit} байт");
                    $updated++;
                }

            } catch (\Exception $e) {
                $this->error("Ошибка для компании {$company->name}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("\nПроверка завершена!");
        $this->info("Обновлено компаний: {$updated}");
        $this->info("Ошибок: {$errors}");

        return 0;
    }
}
