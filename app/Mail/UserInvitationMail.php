<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $inviter;

    public function __construct(Invitation $invitation, $inviter)
    {
        $this->invitation = $invitation;
        $this->inviter = $inviter;
    }

    public function build()
    {
        return $this->subject('Приглашение присоединиться к компании')
            ->markdown('emails.user-invitation')
            ->with([
                'invitationUrl' => $this->invitation->getInvitationUrl(),
                'companyName' => $this->invitation->company->name,
                'inviterName' => $this->inviter->name,
                'expiresAt' => $this->invitation->expires_at->format('d.m.Y H:i'),
            ]);
    }
}
