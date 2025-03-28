<?php

namespace Umang\LaravelCodeFormatter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class FormatModelsCommand extends Command
{
    protected $signature = 'format:models {path?}';
    protected $description = 'Format model files in a directory, or a specific file. If no path is given, formats all models.';

    public function handle()
    {
        $inputPath = $this->argument('path');

        // Base Models directory
        $modelsPath = base_path('app/Models');

        // Determine what to format
        if (!$inputPath) {
            return $this->formatDirectory($modelsPath);
        }

        // Check if input is a file or a directory
        $fullPath = base_path("app/Models/{$inputPath}");

        if (File::isDirectory($fullPath)) {
            return $this->formatDirectory($fullPath);
        }

        // If the user entered a filename without .php, append it
        if (!str_ends_with($fullPath, '.php')) {
            $fullPath .= '.php';
        }

        if (File::exists($fullPath)) {
            return $this->formatSingleFile($fullPath);
        }

        $this->error("❌ Path does not exist: $fullPath");
    }

    private function formatSingleFile(string $filePath)
    {
        $this->info("✅ Formatting file: " . basename($filePath));
        $this->formatPHPFile($filePath);
        $this->info("🎉 Formatting completed!");
    }

    private function formatDirectory(string $dirPath)
    {
        $files = collect(File::allFiles($dirPath))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->values();

        $totalFiles = $files->count();

        if ($totalFiles === 0) {
            $this->info("✅ No PHP files found in: $dirPath");
            return;
        }

        $this->info("🔍 Found $totalFiles model file(s) in: $dirPath");
        $this->newLine();

        $this->withProgressBar($files, function ($file) {
            $this->formatPHPFile($file->getRealPath());
        });

        $this->newLine();
        $this->newLine();
        $this->info("🎉 Formatting completed successfully!");
    }

    private function formatPHPFile(string $filePath)
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