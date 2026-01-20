<?php

namespace App\Jobs;

use App\Models\FormSubmission;
use App\Mail\FormSubmissionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendFormNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $submission;

    public function __construct(FormSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function handle(): void
    {
        // Use config or default
        $recipient = config('mail.form_recipient', 'sales@activ.co.id');

        Mail::to($recipient)->send(new FormSubmissionMail($this->submission));
    }
}
