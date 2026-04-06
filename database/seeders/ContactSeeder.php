<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table - simple lang dahil walang foreign key
        // DB::table('contacts')->truncate();
        
        $contacts = [];

        // Filipino first names
        $firstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Antonio', 'Rosa', 'Manuel', 'Teresa', 'Ramon', 'Fe',
            'Pedro', 'Luz', 'Carlos', 'Nena', 'Ricardo', 'Cora', 'Fernando', 'Lita', 'Gregorio', 'Nora',
            'Rogelio', 'Mila', 'Felipe', 'Belinda', 'Ernesto', 'Cecilia', 'Romeo', 'Julieta', 'Dante', 'Lourdes',
            'Rolando', 'Marites', 'Edwin', 'Susan', 'Reynaldo', 'Gloria', 'Randy', 'Luzviminda', 'Rico', 'Marilou',
            'Allan', 'Analyn', 'Michael', 'Jennifer', 'Jomar', 'Michelle', 'Jerome', 'Angela', 'Christian', 'Catherine',
            'Patrick', 'Kimberly', 'Marvin', 'Mary Grace', 'Ronald', 'Janice', 'Jeffrey', 'Diana', 'Bryan', 'Christine'
        ];

        // Filipino last names
        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Garcia', 'Mendoza', 'Aquino', 'Fernandez', 'Lazaro', 'Gonzales', 'Torres',
            'Villanueva', 'Flores', 'Navarro', 'Ramos', 'Bautista', 'Dela Cruz', 'Magsaysay', 'Marcos', 'Pangilinan',
            'Zamora', 'Gatchalian', 'Villafuerte', 'Salonga', 'Locsin', 'Tiangco', 'Revilla', 'Estrada', 'Binay', 'Eusebio',
            'Cayetano', 'Drilon', 'Enrile', 'Pimentel', 'Sotto', 'Recto', 'Angara', 'Lacson', 'Escudero', 'Osmena',
            'Padilla', 'Romualdez', 'Villar', 'Roxas', 'Abad', 'Belmonte', 'Casiño', 'Dimagiba', 'Evardone', 'Ferrer'
        ];

        // Subjects
        $subjects = [
            'General Inquiry',
            'Technical Support',
            'Partnership Opportunity',
            'Product Inquiry'
        ];

        // Sample messages for each subject type
        $messages = [
            'General Inquiry' => [
                'Magandang araw! Gusto ko sana magtanong tungkol sa inyong mga produkto at serbisyo.',
                'Hello! I would like to know more about your jackfruit products.',
                'Magandang umaga! Interested po ako sa inyong mga alok.',
                'Hi! May tanong lang po ako tungkol sa inyong services.',
                'Good day! I need some information about your products.'
            ],
            'Technical Support' => [
                'Paano po mag-ayos ng issue sa app? Hindi po kasi gumagana ng maayos.',
                'Need technical assistance with my account login.',
                'May problema po ako sa pag-scan ng QR code.',
                'The app keeps crashing. Please help!',
                'Hindi ko po ma-access ang aking account. Need support.'
            ],
            'Partnership Opportunity' => [
                'Gusto ko pong makipag-partner sa inyo para sa aming farming cooperative.',
                'We are interested in collaborating with your organization.',
                'May proposal po ako para sa partnership sa aming grupo.',
                'Looking for potential partners in the agriculture sector.',
                'Magandang oportunidad po ito para sa ating dalawa.'
            ],
            'Product Inquiry' => [
                'Magkano po ang presyo ng inyong mga produkto?',
                'Do you offer bulk discounts for farmers?',
                'Saan po pwedeng bumili ng inyong mga produkto?',
                'What are the available products for jackfruit farming?',
                'May available po ba kayong mga paninda para sa aming farm?'
            ]
        ];

        // Generate 60 contacts
        for ($i = 1; $i <= 60; $i++) {
            $statuses = ['new', 'read', 'resolved'];
            $status = $statuses[array_rand($statuses)];
            
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $name = $firstName . ' ' . $lastName;
            
            $subject = $subjects[array_rand($subjects)];
            $messageOptions = $messages[$subject];
            $message = $messageOptions[array_rand($messageOptions)];
            
            // Generate email based on name
            $email = strtolower($firstName . '.' . $lastName . '@gmail.com');
            $email = str_replace(' ', '', $email);
            
            // Generate Philippine mobile number
            $prefixes = ['0917', '0918', '0919', '0920', '0921', '0922', '0923', '0924', '0925', '0926', '0927', '0928', '0929', '0930', '0931', '0932', '0933', '0934', '0935', '0936', '0937', '0938', '0939', '0940', '0941', '0942', '0943', '0944', '0945', '0946', '0947', '0948', '0949', '0950', '0951', '0952', '0953', '0954', '0955', '0956', '0957', '0958', '0959', '0960', '0961', '0962', '0963', '0964', '0965', '0966', '0967', '0968', '0969', '0970', '0971', '0972', '0973', '0974', '0975', '0976', '0977', '0978', '0979', '0980', '0981', '0982', '0983', '0984', '0985', '0986', '0987', '0988', '0989', '0990', '0991', '0992', '0993', '0994', '0995', '0996', '0997', '0998', '0999'];
            $prefix = $prefixes[array_rand($prefixes)];
            $phone = $prefix . rand(1000000, 9999999);
            
            // Random date within the last 6 months
            $randomDays = rand(0, 180);
            $createdAt = now()->subDays($randomDays);
            $updatedAt = $createdAt;
            
            $contacts[] = [
                'id' => (string) Str::uuid(),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        }

        // Insert in chunks of 20 for better performance
        $chunks = array_chunk($contacts, 20);
        foreach ($chunks as $chunk) {
            DB::table('contacts')->insert($chunk);
        }
        
        $this->command->info('60 contacts seeded successfully!');
        $this->command->info('Status breakdown:');
        $this->command->info('- New: ' . DB::table('contacts')->where('status', 'new')->count());
        $this->command->info('- Read: ' . DB::table('contacts')->where('status', 'read')->count());
        $this->command->info('- Resolved: ' . DB::table('contacts')->where('status', 'resolved')->count());
    }
}