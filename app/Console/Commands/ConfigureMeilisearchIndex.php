<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client as MeilisearchClient;

class ConfigureMeilisearchIndex extends Command
{
    protected $signature = 'search:configure';

    protected $description = 'Configure Meilisearch index settings for property search';

    public function handle(): int
    {
        $this->info('Configuring Meilisearch index settings...');

        try {
            $host = config('scout.meilisearch.host');
            $key = config('scout.meilisearch.key');

            if (!$host || !$key) {
                $this->error('Meilisearch host or key not configured. Check MEILISEARCH_HOST and MEILISEARCH_KEY in .env');
                return Command::FAILURE;
            }

            $client = new MeilisearchClient($host, $key);
            $index = $client->index('properties');

            // Create index if it doesn't exist
            try {
                $client->createIndex('properties', ['primaryKey' => 'id']);
                $this->info('Created properties index.');
            } catch (\Exception $e) {
                // Index already exists, continue
            }

            // Wait for index to be ready
            $this->info('Waiting for index to be ready...');
            sleep(1);

            // Searchable attributes (order = priority)
            $this->info('Setting searchable attributes...');
            $index->updateSearchableAttributes([
                'title',
                'area_name',
                'city_name',
                'address',
                'property_type_name',
                'description',
                'landmark',
                'state_name',
            ]);

            // Filterable attributes
            $this->info('Setting filterable attributes...');
            $index->updateFilterableAttributes([
                'listing_type',
                'property_type_id',
                'property_subtype_id',
                'state_id',
                'city_id',
                'area_id',
                'bedrooms',
                'bathrooms',
                'furnishing_status',
                'price',
                'is_featured_active',
                'is_verified',
                'is_featured',
            ]);

            // Sortable attributes
            $this->info('Setting sortable attributes...');
            $index->updateSortableAttributes([
                'price',
                'created_at',
                'view_count',
                'is_featured_active',
                'featured_until',
            ]);

            // Ranking rules (featured first!)
            $this->info('Setting ranking rules...');
            $index->updateRankingRules([
                'sort',
                'words',
                'typo',
                'proximity',
                'attribute',
                'exactness',
            ]);

            // Typo tolerance settings
            $this->info('Setting typo tolerance...');
            $index->updateTypoTolerance([
                'enabled' => true,
                'minWordSizeForTypos' => [
                    'oneTypo' => 4,
                    'twoTypos' => 8,
                ],
            ]);

            // Synonyms (Nigerian real estate terms)
            $this->info('Setting synonyms...');
            $index->updateSynonyms([
                'apartment' => ['flat', 'apt'],
                'flat' => ['apartment', 'apt'],
                'bedroom' => ['bd', 'br', 'bed'],
                'bathroom' => ['bath', 'bt'],
                'self-contained' => ['self contain', 'selfcon', 'studio'],
                'selfcon' => ['self-contained', 'self contain', 'studio'],
                'studio' => ['self-contained', 'selfcon'],
                'boys quarters' => ['bq', 'boys quarter'],
                'bq' => ['boys quarters', 'boys quarter'],
                'lekki' => ['leki'],
                'leki' => ['lekki'],
                'victoria island' => ['vi', 'v.i.'],
                'vi' => ['victoria island'],
                'ikoyi' => ['ikoy'],
                'ikeja' => ['ikj'],
                'yaba' => ['yab'],
                'surulere' => ['suru'],
                'abuja' => ['fct'],
                'fct' => ['abuja'],
                'lagos' => ['lag'],
                'duplex' => ['semi-detached', 'terrace'],
                'terrace' => ['terraced', 'terrace house'],
                'bungalow' => ['bungallow'],
                'penthouse' => ['pent house', 'pent-house'],
                'maisonette' => ['maisonnette'],
            ]);

            $this->info('Meilisearch index configured successfully!');
            $this->newLine();
            $this->info('Next steps:');
            $this->line('  1. Start Meilisearch: docker compose up -d meilisearch');
            $this->line('  2. Import properties: php artisan scout:import "App\Models\Property"');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to configure Meilisearch index: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Make sure Meilisearch is running:');
            $this->line('  docker compose up -d meilisearch');

            return Command::FAILURE;
        }
    }
}
