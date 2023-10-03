<?php

namespace App\Mail;

use App\Models\PetFound;
use App\Models\QrCodeActivation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PetFoundNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $activation;
    public $petFound;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(QrCodeActivation $activation, PetFound $petFound)
    {
        $this->activation = $activation;
        $this->petFound = $petFound;
        $this->subject("Encontraron a ". $this->activation->pet->name);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: "Encontraron a ".$this->activation->pet->name,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.pet_found_notification',
            with: [
                'activation' => $this->activation,
                'pet_found' => $this->petFound,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
