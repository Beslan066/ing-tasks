<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRemovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $userName;

    public function __construct(Company $company, $userName)
    {
        $this->company = $company;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Ваш доступ к компании был отозван')
            ->markdown('emails.user-removed')
            ->with([
                'companyName' => $this->company->name,
                'userName' => $this->userName,
            ]);
    }
}
