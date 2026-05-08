<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tree;

class TreeSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = 'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka/trees';

        $trees = [
            // J1 to J16 (16 trees)
            ['id' => '4592d34c-1123-45f4-a28e-65a1f88097b5', 'description' => 'J1', 'latitude' => 10.7032977, 'longitude' => 124.803214, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/1.jpg", 'created_at' => '2026-04-10 13:39:00', 'updated_at' => '2026-04-10 13:39:00'],
            ['id' => 'bc0be591-878b-45ce-9d22-3b725275dde8', 'description' => 'J2', 'latitude' => 10.7027429, 'longitude' => 124.8028978, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/2.jpg", 'created_at' => '2026-04-10 13:42:02', 'updated_at' => '2026-04-10 13:42:02'],
            ['id' => 'e5c713dc-39b5-4104-99ab-30157d71d0ec', 'description' => 'J3', 'latitude' => 10.702263, 'longitude' => 124.8029024, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/3.jpg", 'created_at' => '2026-04-10 14:08:56', 'updated_at' => '2026-04-10 14:08:56'],
            ['id' => '92b6ae26-ff22-4e3f-b8e5-4960cb3ae98d', 'description' => 'J4', 'latitude' => 10.7032667, 'longitude' => 124.8035223, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/4.jpg", 'created_at' => '2026-04-10 13:47:33', 'updated_at' => '2026-04-10 13:47:33'],
            ['id' => '950ac4e2-1d53-40b8-9982-0fe0e6156133', 'description' => 'J5', 'latitude' => 10.7033774, 'longitude' => 124.8033735, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/5.jpg", 'created_at' => '2026-04-10 13:49:17', 'updated_at' => '2026-04-10 13:49:17'],
            ['id' => '724cf75d-6d91-4d92-8b56-f96521de2ae8', 'description' => 'J6', 'latitude' => 10.7032284, 'longitude' => 124.8035681, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/6.jpg", 'created_at' => '2026-04-10 13:51:47', 'updated_at' => '2026-04-10 13:51:47'],
            ['id' => '57a9e637-2bf7-4c20-8c7a-7730936ac603', 'description' => 'J7', 'latitude' => 10.703497, 'longitude' => 124.8037514, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/7.jpg", 'created_at' => '2026-04-10 14:08:56', 'updated_at' => '2026-04-10 14:08:56'],
            ['id' => 'a5ca926c-607e-472c-9c1e-619a61d88878', 'description' => 'J8', 'latitude' => 10.7034943, 'longitude' => 124.8037312, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/8.jpg", 'created_at' => '2026-04-10 14:08:58', 'updated_at' => '2026-04-10 14:08:58'],
            ['id' => '796df468-d1d6-4ce5-8fd0-933e08a7efec', 'description' => 'J9', 'latitude' => 10.703442, 'longitude' => 124.8035806, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/9.jpg", 'created_at' => '2026-04-10 14:08:59', 'updated_at' => '2026-04-10 14:08:59'],
            ['id' => '56e06098-01e9-4675-a041-1d4ab4aca043', 'description' => 'J10', 'latitude' => 10.7028118, 'longitude' => 124.8034494, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/10.jpg", 'created_at' => '2026-04-10 14:20:40', 'updated_at' => '2026-04-10 14:20:40'],
            ['id' => '4c94521e-ea24-473e-9222-6c8211aae76d', 'description' => 'J11', 'latitude' => 10.7027745, 'longitude' => 124.8034546, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/11.jpg", 'created_at' => '2026-04-10 14:21:01', 'updated_at' => '2026-04-10 14:21:01'],
            ['id' => 'bad271cc-8d93-4273-a7f0-951e5a5ffb02', 'description' => 'J12', 'latitude' => 10.7024817, 'longitude' => 124.8030604, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/12.jpg", 'created_at' => '2026-04-10 14:19:52', 'updated_at' => '2026-04-10 14:19:52'],
            ['id' => 'e924c216-00b5-4f41-9bd8-d14a0e20a353', 'description' => 'J13', 'latitude' => 10.703393, 'longitude' => 124.8034005, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/13.jpg", 'created_at' => '2026-04-10 14:09:01', 'updated_at' => '2026-04-10 14:09:01'],
            ['id' => '75c43ba4-5969-4776-8d37-88a0c2f81198', 'description' => 'J15', 'latitude' => 10.703177, 'longitude' => 124.8034001, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/15.jpg", 'created_at' => '2026-04-10 14:29:26', 'updated_at' => '2026-04-10 14:29:26'],
            ['id' => '8042c2d8-e1b0-4736-84b7-66f7e109ddb8', 'description' => 'J16', 'latitude' => 10.7036311, 'longitude' => 124.8041115, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/16.jpg", 'created_at' => '2026-04-10 14:28:23', 'updated_at' => '2026-04-10 14:28:23'],

            // J21 to J23 (3 trees)
            ['id' => '79b1381f-b7d1-40a8-92d0-1be908b85b61', 'description' => 'J21', 'latitude' => 10.7034212, 'longitude' => 124.8034871, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/21.jpg", 'created_at' => '2026-04-10 14:09:05', 'updated_at' => '2026-04-10 14:09:05'],
            ['id' => '5fdaffa1-3ddd-49e2-8e68-15b909b42a47', 'description' => 'J22', 'latitude' => 10.7036317, 'longitude' => 124.8040257, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/22.jpg", 'created_at' => '2026-04-10 14:09:04', 'updated_at' => '2026-04-10 14:09:04'],
            ['id' => '933a4a4e-23eb-4a8e-be1d-4d08e0449402', 'description' => 'J23', 'latitude' => 10.703415, 'longitude' => 124.803615, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/23.jpg", 'created_at' => '2026-04-10 14:09:03', 'updated_at' => '2026-04-10 14:09:03'],

            // J25 to J28 (4 trees)
            ['id' => '12d1d639-d6c7-4c53-9e54-9715067f23d8', 'description' => 'J25', 'latitude' => 10.7035506, 'longitude' => 124.8038221, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/25.jpg", 'created_at' => '2026-04-10 14:24:31', 'updated_at' => '2026-04-10 14:24:31'],
            ['id' => '31ad8792-2fdd-461a-9e3b-1983d5428890', 'description' => 'J26', 'latitude' => 10.7030601, 'longitude' => 124.8033125, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/26.jpg", 'created_at' => '2026-04-10 13:55:44', 'updated_at' => '2026-04-10 13:55:44'],
            ['id' => 'b8b65fa7-b3a4-44b8-8d09-7831e378063c', 'description' => 'J27', 'latitude' => 10.7029879, 'longitude' => 124.8033142, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/27.jpg", 'created_at' => '2026-04-10 13:57:17', 'updated_at' => '2026-04-10 13:57:17'],
            ['id' => '402fd50a-6f27-44b2-b355-d34a8e84fb51', 'description' => 'J28', 'latitude' => 10.7028798, 'longitude' => 124.803297, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/28.jpg", 'created_at' => '2026-04-10 13:58:14', 'updated_at' => '2026-04-10 13:58:14'],

            // J30 to J34 (5 trees)
            ['id' => 'fd62a631-35ee-45fd-87b7-61ea1c61ffc1', 'description' => 'J30', 'latitude' => 10.7026397, 'longitude' => 124.8029232, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/30.jpg", 'created_at' => '2026-04-10 14:18:08', 'updated_at' => '2026-04-10 14:18:08'],
            ['id' => '7da77f02-f6c1-4371-a188-e730d01ee73b', 'description' => 'J31', 'latitude' => 10.7027411, 'longitude' => 124.8032159, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/31.jpg", 'created_at' => '2026-04-10 14:17:02', 'updated_at' => '2026-04-10 14:17:02'],
            ['id' => '72973539-3464-4d56-aa49-c24444e4d71a', 'description' => 'J32', 'latitude' => 10.7027846, 'longitude' => 124.8032574, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/32.jpg", 'created_at' => '2026-04-10 14:13:57', 'updated_at' => '2026-04-10 14:13:57'],
            ['id' => 'bcd4d6df-7f09-464d-abb1-89eb7035caa1', 'description' => 'J33', 'latitude' => 10.70282, 'longitude' => 124.8032538, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/33.jpg", 'created_at' => '2026-04-10 14:14:34', 'updated_at' => '2026-04-10 14:14:34'],
            ['id' => '68e9371b-c0db-4773-b86c-87f35b32dade', 'description' => 'J34', 'latitude' => 10.7029115, 'longitude' => 124.8032183, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/34.jpg", 'created_at' => '2026-04-10 14:15:14', 'updated_at' => '2026-04-10 14:15:14'],

            // J39 to J43 (5 trees)
            ['id' => '1b5f9a66-11a9-44f4-ab72-46a7d450c1a3', 'description' => 'J39', 'latitude' => 10.7034518, 'longitude' => 124.8032189, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/39.jpg", 'created_at' => '2026-04-10 14:01:02', 'updated_at' => '2026-04-10 14:01:02'],
            ['id' => '273b8043-9d6f-42da-b8f7-791810edefd4', 'description' => 'J40', 'latitude' => 10.7033786, 'longitude' => 124.8033782, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/40.jpg", 'created_at' => '2026-04-10 14:02:04', 'updated_at' => '2026-04-10 14:02:04'],
            ['id' => 'cb805f98-a246-4947-bfcc-dc2571953667', 'description' => 'J41', 'latitude' => 10.7034102, 'longitude' => 124.8034353, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/41.jpg", 'created_at' => '2026-04-10 14:05:03', 'updated_at' => '2026-04-10 14:05:03'],
            ['id' => '4f893b6f-5550-4279-bf01-7c0532cc5083', 'description' => 'J42', 'latitude' => 10.7033201, 'longitude' => 124.8031844, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/42.jpg", 'created_at' => '2026-04-10 14:03:22', 'updated_at' => '2026-04-10 14:03:22'],
            ['id' => '0b7ecdc8-bbdd-4605-b7a8-071529855f60', 'description' => 'J43', 'latitude' => 10.7034102, 'longitude' => 124.8034353, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/43.jpg", 'created_at' => '2026-04-10 14:06:10', 'updated_at' => '2026-04-10 14:06:10'],

            // J45 to J49 (5 trees)
            ['id' => 'e2df941a-0d0e-44a5-adce-3321d5873f5e', 'description' => 'J45', 'latitude' => 10.7031498, 'longitude' => 124.8031926, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/45.jpg", 'created_at' => '2026-04-10 14:08:39', 'updated_at' => '2026-04-10 14:09:21'],
            ['id' => 'e551f9a7-0bff-4b57-a8a1-667c0443bc23', 'description' => 'J46', 'latitude' => 10.7023899, 'longitude' => 124.8031117, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/46.jpg", 'created_at' => '2026-04-10 14:10:07', 'updated_at' => '2026-04-10 14:10:07'],
            ['id' => '194569cc-fdd9-43f8-aed7-d19eab561900', 'description' => 'J47', 'latitude' => 10.7033402, 'longitude' => 124.8033396, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/47.jpg", 'created_at' => '2026-04-10 14:11:54', 'updated_at' => '2026-04-10 14:11:54'],
            ['id' => '53e36cec-80a2-4a28-abf2-0ec64cf00929', 'description' => 'J48', 'latitude' => 10.7024028, 'longitude' => 124.8031084, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/48.jpg", 'created_at' => '2026-04-10 14:12:54', 'updated_at' => '2026-04-10 14:12:54'],
            ['id' => '01a378a1-772c-4d90-a32f-17e278d9409c', 'description' => 'J49', 'latitude' => 10.7024692, 'longitude' => 124.8030601, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/49.jpg", 'created_at' => '2026-04-10 14:17:35', 'updated_at' => '2026-04-10 14:17:35'],

            // J57 (1 tree)
            ['id' => 'b81bbb9c-55b1-4d1e-b54c-caf37fd7ab76', 'description' => 'J57', 'latitude' => 10.702263, 'longitude' => 124.8029024, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/57.jpg", 'created_at' => '2026-04-10 14:41:11', 'updated_at' => '2026-04-10 14:41:11'],

            // J90 (1 tree)
            ['id' => '78a60987-ca5f-4e4a-ac49-979454efaf1b', 'description' => 'J90', 'latitude' => 10.7023413, 'longitude' => 124.8022075, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/90.jpg", 'created_at' => '2026-04-10 14:33:10', 'updated_at' => '2026-04-10 14:33:10'],

            // J95 to J97 (3 trees)
            ['id' => '613616fa-7f20-45dd-beb7-ec0b5b23662d', 'description' => 'J95', 'latitude' => 10.7028912, 'longitude' => 124.8024864, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/95.jpg", 'created_at' => '2026-04-10 14:33:10', 'updated_at' => '2026-04-10 14:33:10'],
            ['id' => '24f924a4-24c1-4c1f-8cef-ac8071a3f6b1', 'description' => 'J96', 'latitude' => 10.7030692, 'longitude' => 124.8026928, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/96.jpg", 'created_at' => '2026-04-10 14:33:15', 'updated_at' => '2026-04-10 14:33:15'],
            ['id' => 'bf335c80-dc68-4957-9acf-32ed10218800', 'description' => 'J97', 'latitude' => 10.7029667, 'longitude' => 124.802765, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/97.jpg", 'created_at' => '2026-04-10 14:33:29', 'updated_at' => '2026-04-10 14:33:29'],

            // J113 (1 tree)
            ['id' => '6e69b5b4-ed0e-4713-89dc-2b277d5f981b', 'description' => 'J113', 'latitude' => 10.7026822, 'longitude' => 124.8026486, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/113.jpg", 'created_at' => '2026-04-10 14:33:29', 'updated_at' => '2026-04-10 14:33:29'],

            // J124 to J128 (5 trees)
            ['id' => 'f4a8e777-2ed5-438f-84b0-5a725deff50a', 'description' => 'J124', 'latitude' => 10.7026339, 'longitude' => 124.8025732, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/124.jpg", 'created_at' => '2026-04-10 14:33:35', 'updated_at' => '2026-04-10 14:33:35'],
            ['id' => 'd8e5ce86-7c2b-4892-ace1-9b275de7fd35', 'description' => 'J125', 'latitude' => 10.7026683, 'longitude' => 124.8026483, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/125.jpg", 'created_at' => '2026-04-10 14:33:32', 'updated_at' => '2026-04-10 14:33:32'],
            ['id' => '3278b3ce-3978-4709-b728-215f8f86a78c', 'description' => 'J126', 'latitude' => 10.7024703, 'longitude' => 124.8025447, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/126.jpg", 'created_at' => '2026-04-10 14:33:46', 'updated_at' => '2026-04-10 14:33:46'],
            ['id' => '1b6c3203-9ee6-4f2c-889b-361116203b20', 'description' => 'J127', 'latitude' => 10.702377, 'longitude' => 124.8024143, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/127.jpg", 'created_at' => '2026-04-10 14:33:44', 'updated_at' => '2026-04-10 14:33:44'],
            ['id' => 'bf413355-a709-4c5f-897d-036f2d9fa916', 'description' => 'J128', 'latitude' => 10.7021648, 'longitude' => 124.8020851, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/128.jpg", 'created_at' => '2026-04-10 14:33:43', 'updated_at' => '2026-04-10 14:33:43'],

            // J135 (1 tree)
            ['id' => '40664f34-1c4a-4a74-8e18-107a0fc4ffd8', 'description' => 'J135', 'latitude' => 10.7022478, 'longitude' => 124.8026474, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/135.jpg", 'created_at' => '2026-04-10 14:33:40', 'updated_at' => '2026-04-10 14:33:40'],

            // J140 to J141 (2 trees)
            ['id' => 'd250c0e1-9fdf-4c0e-8eef-91e51b72b4f5', 'description' => 'J140', 'latitude' => 10.7024823, 'longitude' => 124.8024499, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/140.jpg", 'created_at' => '2026-04-10 14:34:15', 'updated_at' => '2026-04-10 14:34:15'],
            ['id' => '49284825-6d97-43e8-aa14-673876cd6ded', 'description' => 'J141', 'latitude' => 10.7027192, 'longitude' => 124.802546, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/141.jpg", 'created_at' => '2026-04-10 14:34:18', 'updated_at' => '2026-04-10 14:34:18'],

            // J147 to J148 (2 trees)
            ['id' => '55afd784-66b2-425c-a775-e965d06c3111', 'description' => 'J147', 'latitude' => 10.7025467, 'longitude' => 124.8022883, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/147.jpg", 'created_at' => '2026-04-10 14:34:26', 'updated_at' => '2026-04-10 14:34:26'],
            ['id' => '3e85866f-3791-4226-b0b7-a9a890dc5b61', 'description' => 'J148', 'latitude' => 10.7027431, 'longitude' => 124.8025359, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/148.jpg", 'created_at' => '2026-04-10 14:34:25', 'updated_at' => '2026-04-10 14:34:25'],

            // J151 (1 tree)
            ['id' => '8c7ff3ac-462b-4426-9d27-9e560f0539f7', 'description' => 'J151', 'latitude' => 10.7021205, 'longitude' => 124.8020524, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/151.jpg", 'created_at' => '2026-04-10 14:34:06', 'updated_at' => '2026-04-10 14:34:06'],

            // J154 to J155 (2 trees)
            ['id' => 'd7383841-7204-47ed-8faa-c86003a45ca7', 'description' => 'J154', 'latitude' => 10.7023191, 'longitude' => 124.8025674, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/154.jpg", 'created_at' => '2026-04-10 14:33:53', 'updated_at' => '2026-04-10 14:33:53'],
            ['id' => '0e07db40-cf01-4947-b615-5caf1d16e66b', 'description' => 'J155', 'latitude' => 10.7021449, 'longitude' => 124.8020775, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/155.jpg", 'created_at' => '2026-04-10 14:33:59', 'updated_at' => '2026-04-10 14:33:59'],

            // J159 to J160 (2 trees)
            ['id' => 'c5269b52-9ac8-4cba-87e0-fcc204e5610e', 'description' => 'J159', 'latitude' => 10.7023183, 'longitude' => 124.802175, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/159.jpg", 'created_at' => '2026-04-10 14:34:30', 'updated_at' => '2026-04-10 14:34:30'],
            ['id' => 'b9b6f9be-db5f-499a-b3c3-ad6d02e6d3cb', 'description' => 'J160', 'latitude' => 10.7023725, 'longitude' => 124.8021992, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/160.jpg", 'created_at' => '2026-04-10 14:34:30', 'updated_at' => '2026-04-10 14:34:30'],

            // J200 to J202 (3 trees)
            ['id' => '5a00b450-6ae6-4e0c-9a48-c8e57fd14e3c', 'description' => 'J200', 'latitude' => 10.7026953, 'longitude' => 124.8028692, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/200.jpg", 'created_at' => '2026-04-10 14:33:13', 'updated_at' => '2026-04-10 14:33:13'],
            ['id' => '940dadca-81eb-44d7-a6a7-5dd1e0fa58db', 'description' => 'J201', 'latitude' => 10.702425, 'longitude' => 124.8025, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/201.jpg", 'created_at' => '2026-04-10 14:33:51', 'updated_at' => '2026-04-10 14:33:51'],
            ['id' => '1fe69ee8-9713-46cc-b3ed-62dc0af73f84', 'description' => 'J202', 'latitude' => 10.7025563, 'longitude' => 124.8024812, 'status' => 'active', 'is_synced' => true, 'type' => 'Langka', 'image_url' => "{$baseUrl}/202.jpg", 'created_at' => '2026-04-10 14:34:07', 'updated_at' => '2026-04-10 14:34:07'],
        ];

        $count = 0;
        foreach ($trees as $tree) {
            Tree::create($tree);
            $count++;
        }

        $this->command->info('====================================');
        $this->command->info("🌳 {$count} Langka trees seeded successfully!");
        $this->command->info('====================================');
    }
}