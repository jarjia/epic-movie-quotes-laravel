<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordRecoverMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public $user, public $expires, public $token)
    {
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $imagePath = public_path('/assets/quote-icon.png');

        return $this->from(address: 'epic@moviequotes.com', name: 'Epic Movie Quotes')
            ->subject(subject: 'Recover Password')
            ->view('mails.recover')
            ->attach($imagePath, [
                'as' => 'quote-icon.png',
                'mime' => 'quote-icon/png',
            ]);
    }
}
