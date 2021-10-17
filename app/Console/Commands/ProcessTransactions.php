<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = Transaction::all()
            ->where('status', '=', 'planned')
            ->where('status_at', '<=', now()->toDateTimeString());

        if ($transactions->isNotEmpty()) {
            $transactions->each(function (Transaction $transaction) {
                DB::beginTransaction();
                try {
                    DB::table('users')
                        ->where('id', $transaction->sender_id)
                        ->decrement('balance', $transaction->amount);
                    DB::table('users')
                        ->where('id', $transaction->recipient_id)
                        ->increment('balance', $transaction->amount);
                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'status_at' => now()->format('Y-m-d H:i:s.u'),
                            'status' => 'completed'
                        ]);
                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                }
            });
        }
        return Command::SUCCESS;
    }
}
