<?php

namespace App\Mail;

use App\Models\ClassroomCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClassroomDiscrepancyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $check;
    public $tutorName;
    public $auditoryName;
    public $discrepancies;

    /**
     * Create a new message instance.
     */
    public function __construct(ClassroomCheck $check, string $tutorName, string $auditoryName, array $discrepancies)
    {
        $this->check = $check;
        $this->tutorName = $tutorName;
        $this->auditoryName = $auditoryName;
        $this->discrepancies = $discrepancies;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('ALERT: Выявлена неисправность / некомплект техники в ' . $this->auditoryName)
                    ->view('emails.classroom_discrepancy');
    }
}
