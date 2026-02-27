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
        Tree::truncate();

        // HARDCODED UUIDs for 20 trees
        $trees = [
            // East Langka (5 trees)
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'description' => 'East Langka #1',
                'latitude' => 14.505557176823386,
                'longitude' => 121.04352916677426,
            ],
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'description' => 'East Langka #2',
                'latitude' => 14.505521464492494,
                'longitude' => 121.04342465020623,
            ],
            [
                'id' => '33333333-3333-3333-3333-333333333333',
                'description' => 'East Langka #3',
                'latitude' => 14.505775418720406,
                'longitude' => 121.04391239419031,
            ],
            [
                'id' => '44444444-4444-4444-4444-444444444444',
                'description' => 'East Langka #4',
                'latitude' => 14.50584089124758,
                'longitude' => 121.04405994699222,
            ],
            [
                'id' => '55555555-5555-5555-5555-555555555555',
                'description' => 'East Langka #5',
                'latitude' => 14.505858747387995,
                'longitude' => 121.04418290766047,
            ],

            // North Langka (5 trees)
            [
                'id' => '66666666-6666-6666-6666-666666666666',
                'description' => 'North Langka #1',
                'latitude' => 14.506523391590266,
                'longitude' => 121.04412552601528,
            ],
            [
                'id' => '77777777-7777-7777-7777-777777777777',
                'description' => 'North Langka #2',
                'latitude' => 14.506636480106694,
                'longitude' => 121.0442218452054,
            ],
            [
                'id' => '88888888-8888-8888-8888-888888888888',
                'description' => 'North Langka #3',
                'latitude' => 14.506705920395184,
                'longitude' => 121.04435915128495,
            ],
            [
                'id' => '99999999-9999-9999-9999-999999999999',
                'description' => 'North Langka #4',
                'latitude' => 14.506793216726958,
                'longitude' => 121.04459072721016,
            ],
            [
                'id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                'description' => 'North Langka #5',
                'latitude' => 14.506819008818386,
                'longitude' => 121.04465425688875,
            ],

            // South Langka (5 trees)
            [
                'id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
                'description' => 'South Langka #1',
                'latitude' => 14.50484931004892,
                'longitude' => 121.04342411071563,
            ],
            [
                'id' => 'cccccccc-cccc-cccc-cccc-cccccccccccc',
                'description' => 'South Langka #2',
                'latitude' => 14.504577558719715,
                'longitude' => 121.0435606665031,
            ],
            [
                'id' => 'dddddddd-dddd-dddd-dddd-dddddddddddd',
                'description' => 'South Langka #3',
                'latitude' => 14.504445355249812,
                'longitude' => 121.04358342580102,
            ],
            [
                'id' => 'eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee',
                'description' => 'South Langka #4',
                'latitude' => 14.50429479009073,
                'longitude' => 121.04366687656002,
            ],
            [
                'id' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
                'description' => 'South Langka #5',
                'latitude' => 14.504114846229776,
                'longitude' => 121.04376550018429,
            ],

            // West Langka (5 trees)
            [
                'id' => '11111111-2222-3333-4444-555555555555',
                'description' => 'West Langka #1',
                'latitude' => 14.506689131273959,
                'longitude' => 121.04556348473848,
            ],
            [
                'id' => '22222222-3333-4444-5555-666666666666',
                'description' => 'West Langka #2',
                'latitude' => 14.506755232349041,
                'longitude' => 121.04559003725271,
            ],
            [
                'id' => '33333333-4444-5555-6666-777777777777',
                'description' => 'West Langka #3',
                'latitude' => 14.506839694805155,
                'longitude' => 121.04585935561133,
            ],
            [
                'id' => '44444444-5555-6666-7777-888888888888',
                'description' => 'West Langka #4',
                'latitude' => 14.506883762160774,
                'longitude' => 121.04590866742346,
            ],
            [
                'id' => '55555555-6666-7777-8888-999999999999',
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