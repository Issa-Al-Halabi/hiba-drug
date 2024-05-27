<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderNotifictionDelete extends Command
{
    protected $signature = 'OrderNotificationDelete:cron';
    protected $description = 'Job for order notification delete at two days ago';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        try
        {
            Log::info(" Order Notification Delete Cron is working fine !");
            DB::table('order_notifications')->where('created_at', '>=', Carbon::now()->subMinutes(1)->toDateTimeString())->delete();
        }catch(Exception $e)
        {
            Log::error(" Order Notification Delete Cron is not working fine !");
        }
    }
}
