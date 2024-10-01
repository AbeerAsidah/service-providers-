<?php

namespace App\Jobs;

use App\Models\User;
use App\Traits\HasFcmToken;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;

class SendFirebaseNotificationJob implements ShouldQueue,ShouldBeEncrypted
{
    use Queueable, HasFcmToken;

    protected string $token;
    protected array $body;
    protected string $title;
    /**
     * Create a new job instance.
     */
    public function __construct(string $token, string $title, array $body)
    {
        \Log::warning('constructor') ;

        $this->token = $token ;
        $this->title = $title ;
        $this->body = $body ;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::warning('handle') ;
        $this->sendFirebaseMessage($this->token , $this->title , $this->body) ;
    }
}
