<?php

namespace Umang\LaravelCodeFormatter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InstallConfigCommand extends Command
{
    protected $signature = 'install:configuration';
    protected $description = 'Installs Prettier dependencies and copies configuration files.';

    public function handle()
    {
        $this->info("🚀 Installing required dependencies...");

        $dependencies = ['prettier', '@shufo/prettier-plugin-blade', '@prettier/plugin-php'];

        foreach ($dependencies as $dependency) {
            $this->installDependency($dependency);
        }

        $this->info("✅ Installation completed! Run `format:blades` to format Blade files.");
    }

    private function installDependency($dependency)
    {
        $this->info("📦 Installing $dependency...");
        $process = new Process(['npm', 'install', '--save-dev', $dependency]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("❌ Failed to install $dependency. Please install it manually: npm install --save-dev $dependency");
        }
    }
}
