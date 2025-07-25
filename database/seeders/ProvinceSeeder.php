<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key'),
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

        $provinces = $response->json();

        if (isset($provinces['data']) && !empty($provinces['data'])) {
            foreach ($provinces['data'] as $province) {
                Province::create([
                    'province_id' => $province['id'],
                    'name'        => $province['name']
                ]);
            }

            $this->command->info('Berhasil mengimpor data provinsi dari RajaOngkir');
        } else {
            $this->command->error('Gagal mengambil data provinsi dari RajaOngkir');
        }
    }
}
