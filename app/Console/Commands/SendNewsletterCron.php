<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserBulkNotification;
use App\Models\Subscriber;
use App\Models\CronLog;

class SendNewsletterCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendnewsletter:cron';

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
        // \Log::info("Cron is working fine!");

        // Do entry in Cron log
        $createCronLog = CronLog::create([
            'cron_name' => "SendNewsletterCron",
            'cron_start_time' => date("Y-m-d H:i:s")
        ]);

        // This is to get unsend newsletters (Created from newsletter management)
        $getUnsendNewsletters = UserBulkNotification::where('send_via', 'Email to subscribrs')->where('status', 0)->get();

        if(!empty($getUnsendNewsletters)){
            foreach ($getUnsendNewsletters as $key => $newsletterRow){
                $newsletterRow->status = 2; // processing
                $newsletterRow->save();

                // Get all party subscribers
                $getAllPartySubscribers = Subscriber::where('PPID', $newsletterRow->PPID)->get();

                if(!empty($getAllPartySubscribers)){
                    foreach($getAllPartySubscribers as $key => $subscriberRow){
                        /******* Send newsletter mail **********/
                        $subject = $newsletterRow->subject;
                        $mail_data = ['email' => $subscriberRow->email,'templateData' => $newsletterRow,'subject' => $subject];
                        // $mail_data = ['email' => "kajal.gupta@arkasoftwares.com",'templateData' => $newsletterRow,'subject' => $subject];

                        mailSend($mail_data);
                        /*********************************************************/
                    }
                }

                $newsletterRow->status = 1; // Sent
                $newsletterRow->save();
            }
        }

        // Update Cron log
        $updateCronLog = CronLog::where('id', $createCronLog->id)->update(['cron_end_time' => date("Y-m-d H:i:s")]);

        $this->info('SendNewsletterCron:Cron Command Run successfully!');
    }
}
