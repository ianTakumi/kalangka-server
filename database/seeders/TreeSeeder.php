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

        // Coordinates for each direction
        $coordinates = [
            'East' => [
                ['lat' => 14.505557176823386, 'lng' => 121.04352916677426],
                ['lat' => 14.505521464492494, 'lng' => 121.04342465020623],
                ['lat' => 14.505775418720406, 'lng' => 121.04391239419031],
                ['lat' => 14.50584089124758, 'lng' => 121.04405994699222],
                ['lat' => 14.505858747387995, 'lng' => 121.04418290766047],
            ],
            'North' => [
                ['lat' => 14.506523391590266, 'lng' => 121.04412552601528],
                ['lat' => 14.506636480106694, 'lng' => 121.0442218452054],
                ['lat' => 14.506705920395184, 'lng' => 121.04435915128495],
                ['lat' => 14.506793216726958, 'lng' => 121.04459072721016],
                ['lat' => 14.506819008818386, 'lng' => 121.04465425688875],
            ],
            'South' => [
                ['lat' => 14.50484931004892, 'lng' => 121.04342411071563],
                ['lat' => 14.504577558719715, 'lng' => 121.0435606665031],
                ['lat' => 14.504445355249812, 'lng' => 121.04358342580102],
                ['lat' => 14.50429479009073, 'lng' => 121.04366687656002],
                ['lat' => 14.504114846229776, 'lng' => 121.04376550018429],
            ],
            'West' => [
                ['lat' => 14.506689131273959, 'lng' => 121.04556348473848],
                ['lat' => 14.506755232349041, 'lng' => 121.04559003725271],
                ['lat' => 14.506839694805155, 'lng' => 121.04585935561133],
                ['lat' => 14.506883762160774, 'lng' => 121.04590866742346],
                ['lat' => 14.506979241401202, 'lng' => 121.0461590197005],
            ],
        ];

        $imageUrl = 'https://gujmgaqntmdvqvvlwqhf.supabase.co/storage/v1/object/public/kalangka/langka/images.jpg';

        // Create trees for each direction
        foreach ($coordinates as $direction => $locations) {
            foreach ($locations as $index => $coord) {
                $treeNumber = $index + 1;
                
                Tree::create([
                    'id' => Str::uuid()->toString(),
                    'description' => "{$direction} Langka #{$treeNumber}",
                    'latitude' => $coord['lat'],
                    'longitude' => $coord['lng'],
                    'status' => 'active',
                    'is_synced' => true,
                    'type' => 'Langka',
                    'image_url' => $imageUrl,
                ]);
                
                $this->command->info("   ✅ Created: {$direction} Langka #{$treeNumber}");
            }
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