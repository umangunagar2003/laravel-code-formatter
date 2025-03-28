<?php

namespace Umang\LaravelCodeFormatter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class FormatControllersCommand extends Command
{
    protected $signature = 'format:controllers {path?}';
    protected $description = 'Format Controller files in a directory, or a specific file. If no path is given, formats all Controllers.';

    public function handle()
    {
        $inputPath = $this->argument('path');

        // Default: Format all Controllers in app/Http/Controllers
        if (!$inputPath) {
            return $this->formatDirectory(app_path('Http/Controllers'));
        }

        // Check if input is a file or a directory
        $fullPath = app_path("Http/Controllers/$inputPath");

        if (File::isDirectory($fullPath)) {
            return $this->formatDirectory($fullPath);
        }

        // Ensure the given file has a .php extension
        if (!str_ends_with($fullPath, '.php')) {
            $fullPath .= '.php';
        }

        if (File::exists($fullPath)) {
            return $this->formatSingleFile($fullPath);
        }

        $this->error("❌ Path does not exist: $fullPath");
    }

    private function formatSingleFile($filePath)
    {
        $this->info("✅ Formatting file: " . basename($filePath));
        $this->formatPHPFile($filePath);
        $this->info("🎉 Formatting completed!");
    }

    private function formatDirectory($dirPath)
    {
        $files = collect(File::allFiles($dirPath))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->values();

        $totalFiles = $files->count();

        if ($totalFiles === 0) {
            $this->info("✅ No PHP files found in: $dirPath");
            return;
        }

        $this->info("🔍 Found $totalFiles Controller file(s) in: $dirPath");
        $this->newLine();

        $this->withProgressBar($files, function ($file) {
            $this->formatPHPFile($file->getRealPath());
        });

        $this->newLine();
        $this->newLine();
        $this->info("🎉 Formatting completed successfully!");
    }

    private function formatPHPFile($filePath)
    {
        $defaultConfigPath = __DIR__ . '/../../../config/default_formatter.json';
        $publishedConfigPath = base_path('formatter.json');

        $prettierConfig = file_exists($publishedConfigPath) ? $publishedConfigPath : $defaultConfigPath;

        if (!File::exists($prettierConfig)) {
            $this->error("❌ formatter.json file not found: $prettierConfig");
            return;
        }
        
        $process = new Process([
            'npx', 'prettier', '--write', '--parser', 'php', '--config', $prettierConfig, $filePath
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("\n❌ Failed to format: $filePath");
            $this->error($process->getErrorOutput());
        }
    }
}