<?php

namespace App\Jobs;

use App\Notifications\NotifyAdmins;
use App\Persistence\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAdminsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly int $currentAdminId, public readonly string $requestType)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::admin()->whereNotIn('id', [$this->currentAdminId])->get();
        $users->each(function ($user){
            $user->notify(new NotifyAdmins($this->requestType));
        });
    }
}
