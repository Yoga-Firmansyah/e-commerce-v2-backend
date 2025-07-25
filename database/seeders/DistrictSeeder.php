<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Mengambil semua data kota dari RajaOngkir
        $cities = City::all();

        foreach ($cities as $city) {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key'),
            ])->get('https://rajaongkir.komerce.id/api/v1/destination/district/' . $city->city_id);

            $districts = $response->json();

            if (isset($districts['data']) && !empty($districts['data'])) {
                foreach ($districts['data'] as $district) {
                    District::create([
                        'city_id'     => $city->city_id,
                        'district_id' => $district['id'],
                        'name'        => $district['name'],
                    ]);
                }

                $this->command->info('Berhasil mengimpor data kecamatan untuk kota ' . $city['name']);
            } else {
                $this->command->error('Gagal mengambil data kecamatan untuk kota ' . $city->name);
            }
        }
    }
}
