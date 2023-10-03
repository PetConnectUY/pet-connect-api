<?php

namespace App\Jobs;

use App\Mail\PetFoundNotification;
use App\Models\PetFound;
use App\Models\QrCodeActivation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPetFoundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $activation;
    public $petFound;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(QrCodeActivation $activation, PetFound $petFound)
    {
        $this->activation = $activation;
        $this->petFound = $petFound;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->activation->user->email)->send(new PetFoundNotification($this->activation, $this->petFound));
    }
}
