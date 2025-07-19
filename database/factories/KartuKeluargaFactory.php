<?php

namespace Database\Factories;

use App\Models\KartuKeluarga;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Database\Eloquent\Factories\Factory;

class KartuKeluargaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = KartuKeluarga::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $klasifikasiOptions = ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'];

        return [
            'desa_id' => null, // Akan diisi di seeder
            'nomor_kk' => $this->faker->unique()->numerify('##############'), // 14 digit
            'rw_id' => null, // Akan diisi di seeder
            'rt_id' => null, // Akan diisi di seeder
            'alamat_lengkap' => $this->faker->address,
            'klasifikasi' => $this->faker->randomElement($klasifikasiOptions),
            'kepala_keluarga_id' => null, // Akan diisi di seeder setelah warga dibuat
        ];
    }
}