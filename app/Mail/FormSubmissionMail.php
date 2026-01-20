<?php

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    public function __construct(FormSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Form Submission from ' . ($this->submission->page_slug ?? 'Website'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.form_submission',
            with: [
                'data' => $this->submission->payload,
                'page' => $this->submission->page_slug,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
