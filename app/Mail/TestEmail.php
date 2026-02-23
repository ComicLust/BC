<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailTemplate $template, array $data)
    {
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->template->subject)
                    ->view('emails.test')
                    ->with([
                        'content' => $this->template->parseTemplate($this->data)
                    ]);
    }
}
