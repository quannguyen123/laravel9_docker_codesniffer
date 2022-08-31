<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $i = 0;
        while($i < 10) {
            $email = fake()->safeEmail();

            if(!User::where('email', $email)->exists() ){
                User::create([
                    'name' => fake()->name(),
                    'email' => $email,
                    'password' => Hash::make(12345678)
                ]);
            }
            $i++;
        }

        return 0;
    }
}
