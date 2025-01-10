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
        // 当日分の予約データを取得
        $today = Carbon::today();
        $targetReservations = Reservation::with('user') -> whereDate('booked_datetime', $today) -> get();
        $emails = array();

        // 予約ごとにリマインダーメールを送る
        foreach($targetReservations as $targetReservation) {
            $email =  $targetReservation -> user -> email;
            $emails[] = $email;
            Log::channel('reminder_mail')->info('Send an email to '.$email);

            // QRコード
            $reservationModel = new Reservation();
            $qrCode = $reservationModel->createQrCode($targetReservation);

            // 予約内容をメールで送る
            $mailContent = [
                'user_name' => $targetReservation->user->name,
                'shop_name' => $targetReservation->shop->name,
                'datetime' => $targetReservation->booked_datetime,
                'people_counts' => $targetReservation->people_counts,
                'qrcode' => $qrCode,
            ];

            Mail::to($email)->send(new ReminderMail($mailContent));
        }
    }
}
