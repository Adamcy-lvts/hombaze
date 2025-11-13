<?php

namespace App\Console\Commands;

use Spatie\MediaLibrary\Conversions\FileManipulator;
use Exception;
use App\Models\Property;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RegenerateMediaConversions extends Command
{
    protected $signature = 'media:regenerate-conversions {--model=Property} {--collection=} {--force}';
    protected $description = 'Regenerate media conversions for existing media files';

    public function handle()
    {
        $modelClass = 'App\\Models\\' . $this->option('model');
        $collection = $this->option('collection');
        $force = $this->option('force');

        if (!class_exists($modelClass)) {
            $this->error("Model {$modelClass} does not exist.");
            return 1;
        }

        $this->info("Regenerating media conversions for {$modelClass}...");

        $query = Media::where('model_type', $modelClass);

        if ($collection) {
            $query->where('collection_name', $collection);
        }

        $mediaItems = $query->get();
        $this->info("Found {$mediaItems->count()} media items to process.");

        $progressBar = $this->output->createProgressBar($mediaItems->count());
        $progressBar->start();

        $processed = 0;
        $errors = 0;

        foreach ($mediaItems as $media) {
            try {
                $model = $media->model;
                if (!$model) {
                    $this->newLine();
                    $this->warn("Skipping media {$media->id} - model not found");
                    continue;
                }

                // Check if conversions already exist
                $hasConversions = $media->getGeneratedConversions()->isNotEmpty();

                if (!$hasConversions || $force) {
                    // Regenerate conversions using the FileManipulator
                    try {
                        app(FileManipulator::class)
                            ->createDerivedFiles($media);

                        $processed++;
                    } catch (Exception $e) {
                        $this->newLine();
                        $this->warn("Failed to generate conversions for media {$media->id}: {$e->getMessage()}");
                    }
                }

                $progressBar->advance();

            } catch (Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error processing media {$media->id}: {$e->getMessage()}");
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Processed {$processed} media items.");

        if ($errors > 0) {
            $this->warn("Encountered {$errors} errors during processing.");
        }

        return 0;
    }
}