<?php

namespace Umang\LaravelCodeFormatter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class FormatBladesCommand extends Command
{
    protected $signature = 'format:blades {path?}';
    protected $description = 'Format Blade files in a directory, or a specific file. If no path is given, formats all Blade files.';

    public function handle()
    {
        $inputPath = $this->argument('path');

        // Default: Format all Blade files in resources/views/
        if (!$inputPath) {
            return $this->formatDirectory(resource_path('views'));
        }

        // Check if input is a file or a directory
        $fullPath = resource_path("views/$inputPath");

        if (File::isDirectory($fullPath)) {
            return $this->formatDirectory($fullPath);
        }

        // If the user entered a filename without .blade.php, append it
        if (!str_ends_with($fullPath, '.blade.php')) {
            $fullPath .= '.blade.php';
        }

        if (File::exists($fullPath)) {
            return $this->formatSingleFile($fullPath);
        }

        $this->error("❌ Path does not exist: $fullPath");
    }

    private function formatSingleFile($filePath)
    {
        $this->info("✅ Formatting file: " . basename($filePath));
        $this->formatBladeFile($filePath);
        $this->info("🎉 Formatting completed!");
    }

    private function formatDirectory($dirPath)
    {
        $files = collect(File::allFiles($dirPath))
            ->filter(fn ($file) => $file->getExtension() === 'php' && str_contains($file->getFilename(), '.blade.php'))
            ->values();

        $totalFiles = $files->count();

        if ($totalFiles === 0) {
            $this->info("✅ No Blade files found in: $dirPath");
            return;
        }

        $this->info("🔍 Found $totalFiles Blade file(s) in: $dirPath");
        $this->newLine();

        $this->withProgressBar($files, function ($file) {
            $this->formatBladeFile($file->getRealPath());
        });

        $this->newLine();
        $this->newLine();
        $this->info("🎉 Formatting completed successfully!");
    }

    private function formatBladeFile($filePath)
    {
        $defaultConfigPath = __DIR__ . '/../../../config/default_formatter.json';
        $publishedConfigPath = base_path('formatter.json');

        $prettierConfig = file_exists($publishedConfigPath) ? $publishedConfigPath : $defaultConfigPath;

        if (!File::exists($prettierConfig)) {
            $this->error("❌ formatter.json file not found: $prettierConfig");
            return;
        }
        
        $process = new Process([
            'npx', 'prettier', '--write', '--parser', 'blade', '--config', $prettierConfig, $filePath
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("\n❌ Failed to format: $filePath");
            $this->error($process->getErrorOutput());
        }
    }
}