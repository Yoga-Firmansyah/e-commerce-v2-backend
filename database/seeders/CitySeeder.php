<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengambil semua data kota dari RajaOngkir
        $provinces = Province::all();

        foreach ($provinces as $province) {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key'),
            ])->get('https://rajaongkir.komerce.id/api/v1/destination/city/' . $province->province_id);

            $cities = $response->json();

            if (isset($cities['data']) && !empty($cities['data'])) {
                foreach ($cities['data'] as $city) {
                    City::create([
                        'province_id' => $province->province_id,
                        'city_id'     => $city['id'],
                        'name'        => $city['name'],
                    ]);
                }

                $this->command->info('Berhasil mengimpor data kota untuk provinsi ' . $province->name);
            } else {
                $this->command->error('Gagal mengambil data kota untuk provinsi ' . $province->name);
            }
        }
    }
}
