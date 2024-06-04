<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RequireSpatieTranslatable extends Command
{
    protected $signature = 'composer:require-spatie-translatable';

    protected $description = 'Require spatie/laravel-translatable package using Composer';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Execute the composer require command
        $output = shell_exec('composer require spatie/laravel-translatable');
        $this->info($output);
    }
}
