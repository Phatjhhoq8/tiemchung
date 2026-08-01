<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\VaccineRegistration\Models\Registration;
use Illuminate\Support\Facades\Log;

class SendRegistrationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Registration $registration;
    public string $event;

    /**
     * Create a new job instance.
     */
    public function __construct(Registration $registration, string $event = 'created')
    {
        $this->registration = $registration;
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Sending registration email for {$this->registration->registration_code} [Event: {$this->event}]");
    }
}
