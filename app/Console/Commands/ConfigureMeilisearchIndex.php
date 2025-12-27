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
            $key  = config('scout.meilisearch.key');

            if (!$host || !$key) {
                $this->error('Meilisearch host or key not configured.');
                return Command::FAILURE;
            }

            $client = new MeilisearchClient($host, $key);
            $index  = $client->index('properties');

            try {
                $client->createIndex('properties', ['primaryKey' => 'id']);
            } catch (\Throwable $e) {
                // Index already exists
            }

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

            $index->updateSortableAttributes([
                'price',
                'created_at',
                'view_count',
                'is_featured_active',
                'featured_until',
            ]);

            $index->updateRankingRules([
                'sort',
                'words',
                'typo',
                'proximity',
                'attribute',
                'exactness',
            ]);

            $index->updateTypoTolerance([
                'enabled' => true,
                'minWordSizeForTypos' => [
                    'oneTypo' => 4,
                    'twoTypos' => 8,
                ],
            ]);

            $index->updateSynonyms([
                'apartment' => ['flat', 'apt'],
                'flat' => ['apartment', 'apt'],
                'bedroom' => ['bd', 'br', 'bed'],
                'bathroom' => ['bath', 'bt'],
                'self-contained' => ['self contain', 'selfcon', 'studio'],
                'bq' => ['boys quarters'],
                'lekki' => ['leki'],
                'victoria island' => ['vi'],
                'abuja' => ['fct'],
                'duplex' => ['semi-detached', 'terrace'],
            ]);

            $this->info('✅ Meilisearch index configured successfully!');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to configure Meilisearch index: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
