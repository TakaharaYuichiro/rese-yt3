<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use DateTime;
use Carbon\Carbon;
use Mail;                   
use App\Mail\ReminderMail;

class SendReminderMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendReminderMail';

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
        // 予約データベースのデータを取得
        $today=Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $targetReservations = Reservation::with('user') -> whereDate('booked_datetime', $today) -> get();
        $emails = array();
        foreach($targetReservations as $targetReservation) {
            $email =  $targetReservation -> user -> email;
            $emails[] = $email;
            Log::channel('reminder_mail')->info('Send an email to '.$email);

            Mail::to($email)
                ->send(new ReminderMail($targetReservation));
        }
    }
}
