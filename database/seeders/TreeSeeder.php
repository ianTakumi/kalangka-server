<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tree;
use Illuminate\Support\Str;

class TreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing records
        // Tree::truncate();

        // HARDCODED UUIDs for 20 trees
        $trees = [
            // East Langka (5 trees)
            [
                'id' => 'b5d0c261-7b87-45bc-ac25-9296546bbd76',
                'description' => 'East Langka #1',
                'latitude' => 14.505557176823386,
                'longitude' => 121.04352916677426,
            ],
            [
                'id' => '10529670-463f-436d-a664-d4e09202acbe',
                'description' => 'East Langka #2',
                'latitude' => 14.505521464492494,
                'longitude' => 121.04342465020623,
            ],
            [
                'id' => '018e39a3-3b74-4609-941c-48780d96f87c',
                'description' => 'East Langka #3',
                'latitude' => 14.505775418720406,
                'longitude' => 121.04391239419031,
            ],
            [
                'id' => 'db9225a2-1c3d-4cf7-b7c0-3b8c17cab26b',
                'description' => 'East Langka #4',
                'latitude' => 14.50584089124758,
                'longitude' => 121.04405994699222,
            ],
            [
                'id' => '97d83628-34f8-4829-a222-716ee34fa2af',
                'description' => 'East Langka #5',
                'latitude' => 14.505858747387995,
                'longitude' => 121.04418290766047,
            ],

            // North Langka (5 trees)
            [
                'id' => '7f13f5f3-9945-4072-b5e0-db73f47a2401',
                'description' => 'North Langka #1',
                'latitude' => 14.506523391590266,
                'longitude' => 121.04412552601528,
            ],
            [
                'id' => 'b125ad94-7c64-4242-b708-29b63eec2328',
                'description' => 'North Langka #2',
                'latitude' => 14.506636480106694,
                'longitude' => 121.0442218452054,
            ],
            [
                'id' => 'c6285e3e-c2ce-4a02-a4ef-385331b94b35',
                'description' => 'North Langka #3',
                'latitude' => 14.506705920395184,
                'longitude' => 121.04435915128495,
            ],
            [
                'id' => '783ac30b-eb62-4c6b-b8b4-716bac392b87',
                'description' => 'North Langka #4',
                'latitude' => 14.506793216726958,
                'longitude' => 121.04459072721016,
            ],
            [
                'id' => '5890576d-2e6e-4128-8c70-e6187decf2da',
                'description' => 'North Langka #5',
                'latitude' => 14.506819008818386,
                'longitude' => 121.04465425688875,
            ],

            // South Langka (5 trees)
            [
                'id' => '26a7eda6-4d44-4974-a28a-c3796d192864',
                'description' => 'South Langka #1',
                'latitude' => 14.50484931004892,
                'longitude' => 121.04342411071563,
            ],
            [
                'id' => '6e881fbb-f458-4e6b-b2f9-e8cd82adf1de',
                'description' => 'South Langka #2',
                'latitude' => 14.504577558719715,
                'longitude' => 121.0435606665031,
            ],
            [
                'id' => 'b3e49e38-d126-46ed-91a4-7ce022abcc8c',
                'description' => 'South Langka #3',
                'latitude' => 14.504445355249812,
                'longitude' => 121.04358342580102,
            ],
            [
                'id' => 'ef179d7d-f78b-45d2-8384-955181bb1256',
                'description' => 'South Langka #4',
                'latitude' => 14.50429479009073,
                'longitude' => 121.04366687656002,
            ],
            [
                'id' => '0fef3a1b-f310-47b6-b174-7ca8df44567c',
                'description' => 'South Langka #5',
                'latitude' => 14.504114846229776,
                'longitude' => 121.04376550018429,
            ],

            // West Langka (5 trees)
            [
                'id' => '044f2a78-306b-499d-b2c3-4bf8797f82e5',
                'description' => 'West Langka #1',
                'latitude' => 14.506689131273959,
                'longitude' => 121.04556348473848,
            ],
            [
                'id' => 'aac2fba8-e4f0-4ac0-809d-b0c9efcf16bd',
                'description' => 'West Langka #2',
                'latitude' => 14.506755232349041,
                'longitude' => 121.04559003725271,
            ],
            [
                'id' => '7ccbd87e-e8f7-40e5-956d-a38d9519cccc',
                'description' => 'West Langka #3',
                'latitude' => 14.506839694805155,
                'longitude' => 121.04585935561133,
            ],
            [
                'id' => 'b3e1bc6b-45ea-42f0-bc06-2e87ee58136a',
                'description' => 'West Langka #4',
                'latitude' => 14.506883762160774,
                'longitude' => 121.04590866742346,
            ],
            [
                'id' => 'd898b41d-14e3-4566-aa51-bb855604f8c4',
                'description' => 'West Langka #5',
                'latitude' => 14.506979241401202,
                'longitude' => 121.0461590197005,
            ],
        ];

        $imageUrl = 'https://gujmgaqntmdvqvvlwqhf.supabase.co/storage/v1/object/public/kalangka/Tree/images.jpg';

        // Create all trees
        foreach ($trees as $tree) {
            Tree::create([
                'id' => $tree['id'],
                'description' => $tree['description'],
                'latitude' => $tree['latitude'],
                'longitude' => $tree['longitude'],
                'status' => 'active',
                'is_synced' => true,
                'type' => 'Langka',
                'image_url' => $imageUrl,
            ]);
            
            $this->command->info("   ✅ Created: {$tree['description']} - ID: {$tree['id']}");
        }

        $this->command->info('====================================');
        $this->command->info('🌳 ' . Tree::count() . ' Langka trees seeded successfully!');
        $this->command->info('====================================');
        $this->command->info('📍 East Langka #1 to #5');
        $this->command->info('📍 North Langka #1 to #5');
        $this->command->info('📍 South Langka #1 to #5');
        $this->command->info('📍 West Langka #1 to #5');
        $this->command->info('====================================');
        $this->command->info('🖼️  Image: ' . $imageUrl);
    }
}