<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EspayVirtualAccount;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExpireVirtualAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'va:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menandai Virtual Account yang sudah melewati expired_date sebagai EXPIRED';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredVAs = EspayVirtualAccount::where('status', 'ACTIVE')
            ->where('expired_date', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredVAs as $va) {
            $va->update(['status' => 'EXPIRED']);
            Log::info("VA expired otomatis: {$va->va_number}");
            $this->info("Updated VA {$va->va_number} to EXPIRED");
            $count++;
        }

        $this->info("Expired VA check completed. {$count} VAs updated.");

        return 0;
    }
}
