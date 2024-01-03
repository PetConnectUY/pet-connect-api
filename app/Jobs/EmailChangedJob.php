<?php

namespace App\Jobs;

use App\Mail\EmailChangedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailChangedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $email, $fullname;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, string $fullname)
    {
        $this->email = $email;
        $this->fullname = $fullname;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new EmailChangedNotification($this->email, $this->fullname));
    }
}
