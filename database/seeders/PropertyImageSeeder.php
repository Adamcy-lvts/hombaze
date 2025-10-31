<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertyImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🖼️  Starting Property Image Seeding...');

        $properties = Property::all();
        $this->command->info("Found {$properties->count()} properties to add images to.");

        foreach ($properties as $property) {
            $this->addPropertyImages($property);
        }

        $this->command->info('✅ Property Image Seeding completed successfully!');
    }

    /**
     * Add property images using Spatie Media Library
     */
    private function addPropertyImages(Property $property): void
    {
        try {
            // Skip if property already has images
            if ($property->getMedia('featured')->count() > 0) {
                $this->command->comment("⏭️  Skipping {$property->title} - already has images");
                return;
            }

            // High-quality real estate property images from Unsplash
            $imageUrls = [
                'https://images.unsplash.com/photo-1505843513577-22bb7d21e455?w=800&h=600&q=80', // Modern house exterior
                'https://images.unsplash.com/photo-1522050212171-61b01dd24579?w=800&h=600&q=80', // Apartment building
                'https://images.unsplash.com/photo-1489171078254-c3365d6e359f?w=800&h=600&q=80', // Luxury home exterior
                'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?w=800&h=600&q=80', // Modern interior
                'https://images.unsplash.com/photo-1502005229762-cf1b2da7c5d6?w=800&h=600&q=80', // Kitchen
                'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&q=80', // Living room
                'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&q=80', // Bedroom
                'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=800&h=600&q=80', // Modern kitchen
                'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800&h=600&q=80', // Bathroom
                'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?w=800&h=600&q=80', // House exterior
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&q=80', // Bedroom interior
                'https://images.unsplash.com/photo-1556909114-f7c31d2b281b?w=800&h=600&q=80', // Living area
                'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&h=600&q=80', // Living room with window
                'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&q=80', // Modern bedroom
                'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&h=600&q=80', // Beautiful home exterior
                'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=800&h=600&q=80', // Kitchen dining
                'https://images.unsplash.com/photo-1604709177225-055f99402ea3?w=800&h=600&q=80', // Modern apartment
                'https://images.unsplash.com/photo-1571055107559-3e67626fa8be?w=800&h=600&q=80', // Kitchen island
                'https://images.unsplash.com/photo-1628744876497-eb30460be9f6?w=800&h=600&q=80', // Luxury bathroom
                'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?w=800&h=600&q=80', // Modern home exterior
            ];

            // Property type specific images
            $propertyTypeImages = [
                'house' => [
                    'https://images.unsplash.com/photo-1505843513577-22bb7d21e455?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1489171078254-c3365d6e359f?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?w=800&h=600&q=80',
                ],
                'apartment' => [
                    'https://images.unsplash.com/photo-1522050212171-61b01dd24579?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1604709177225-055f99402ea3?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&q=80',
                ],
                'commercial' => [
                    'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=600&q=80',
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&q=80',
                ],
                'default' => $imageUrls
            ];

            // Select images based on property type
            $propertyTypeName = strtolower($property->propertyType->name ?? 'default');
            $availableImages = $propertyTypeImages[$propertyTypeName] ?? $propertyTypeImages['default'];

            // Shuffle and select 3-6 images for this property
            shuffle($availableImages);
            $selectedImages = array_slice($availableImages, 0, rand(3, 6));

            // Add featured image (first image)
            $featuredUrl = $selectedImages[0];
            $property
                ->addMediaFromUrl($featuredUrl)
                ->usingName("Featured image for {$property->title}")
                ->usingFileName("featured_" . $property->id . "_" . time() . ".jpg")
                ->toMediaCollection('featured');

            $this->command->info("📷 Added featured image for: {$property->title}");

            // Add gallery images (remaining images)
            for ($i = 1; $i < count($selectedImages); $i++) {
                $property
                    ->addMediaFromUrl($selectedImages[$i])
                    ->usingName("Gallery image " . $i . " for {$property->title}")
                    ->usingFileName("gallery_" . $property->id . "_" . $i . "_" . time() . ".jpg")
                    ->toMediaCollection('gallery');
            }

            $galleryCount = count($selectedImages) - 1;
            $this->command->info("🖼️  Added {$galleryCount} gallery images for: {$property->title}");

            // Small delay to avoid overwhelming the API
            usleep(500000); // 0.5 second delay

        } catch (\Exception $e) {
            // If image download fails, continue without breaking the seeder
            $this->command->warn("⚠️  Failed to add images for {$property->title}: " . $e->getMessage());
        }
    }
}