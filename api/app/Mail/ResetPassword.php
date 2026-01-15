<?php

namespace App\Mail;

use App\Models\RegistrationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RegistrationModel $user)
    {
        //
    $token = rand (100000, 999999);

    $token_hash = Hash::make($token);

    $user->update(["password"=>$token_hash]);

    $this->user = $user;

    $this->token = $token;

    $this->subject = "Reset Password Account";

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');

            $view = '';

            return $this->markdown($view)->subject($this->subject)
            ->with([
                "subject"=>$this->subject,
                "token"=>$this->token,
            ]);
    }
}
