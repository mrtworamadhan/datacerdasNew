<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Imagick;

class OcrController extends Controller
{
    public function preprocessImage($uploadedFile)
    {
        $manager = new ImageManager(new Driver()); // PAKAI IMAGICK

        $image = $manager->read($uploadedFile->getRealPath());

        $processedImage = $image
            ->greyscale()
            ->contrast(10)
            ->scale(1000, null);

        $tempPath = storage_path('app/temp_ocr_image.jpg');
        $processedImage->toJpeg()->save($tempPath);

        return $tempPath;
    }

    public function scanKtpOcr(Request $request)
    {
        $user = Auth::user();

        if (! $user->isAdminDesa() && ! $user->isSuperAdmin() && ! $user->isAdminRw() && ! $user->isAdminRt()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'ktp_image' => 'required|image|max:5120',
        ]);

        $tempPath = $this->preprocessImage($request->file('ktp_image'));

        $credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS', storage_path('app/googlecredentials.json'));
        if (!file_exists($credentialsPath)) {
            return response()->json(['error' => 'File kredensial Google Vision tidak ditemukan.'], 500);
        }

        putenv("GOOGLE_APPLICATION_CREDENTIALS={$credentialsPath}");

        try {
            $client = new ImageAnnotatorClient();
            $imageContent = file_get_contents($tempPath);

            $image = (new Image())->setContent($imageContent);
            $feature = (new Feature())->setType(\Google\Cloud\Vision\V1\Feature\Type::TEXT_DETECTION);

            $annotateRequest = (new AnnotateImageRequest())
                ->setImage($image)
                ->setFeatures([$feature]);

            $batchRequest = (new BatchAnnotateImagesRequest())
                ->setRequests([$annotateRequest]);

            $response = $client->batchAnnotateImages($batchRequest);
            $client->close();

            $fullText = '';
            foreach ($response->getResponses() as $res) {
                if ($res->getTextAnnotations()->count() > 0) {
                    $fullText = $res->getTextAnnotations()[0]->getDescription();
                    break;
                }
            }

            $parsedData = $this->parseOcrText($fullText);
            Log::info('OCR Result:', [
                'full_text' => $fullText,
                'parsed_data' => $parsedData
            ]);
            return response()->json([
                'success' => true,
                'full_text' => $fullText,
                'parsed_data' => $parsedData,
            ]);
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal memproses OCR: ' . $e->getMessage()], 500);
        }
    }

    private function parseOcrText(string $text): array
    {
        $data = [
            'nik' => '',
            'nama_lengkap' => '',
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'jenis_kelamin' => '',
            'agama' => '',
            'status_perkawinan' => '',
            'pekerjaan' => '',
            'kewarganegaraan' => '',
            'golongan_darah' => '',
            'alamat_lengkap' => '',
            'rt' => '',
            'rw' => '',
            'kel_desa' => '',
            'kecamatan' => '',
            'kota' => '',
            'provinsi' => '',
        ];

        $lines = collect(explode("\n", $text))
            ->map(fn($line) => trim(preg_replace('/\s+/', ' ', $line)))
            ->filter()
            ->values();

        for ($i = 0; $i < $lines->count(); $i++) {
            $line = $lines[$i];
            $lower = strtolower($line);

            if (!$data['nik'] && preg_match('/\b\d{16}\b/', $line, $m)) {
                $data['nik'] = $m[0];
            }

            if (!$data['nama_lengkap'] && $data['nik']) {
                foreach ($lines as $i => $line) {
                    if (str_contains($line, $data['nik'])) {
                        // Coba cek baris setelahnya
                        for ($j = 1; $j <= 3; $j++) {
                            $nextLine = $lines[$i + $j] ?? '';
                            $nextLineClean = trim($nextLine, ":.- ");

                            // Hindari baris yang merupakan label
                            if (!preg_match('/^(provinsi|kabupaten|kota|nik|tempat|jenis|alamat|rt\/rw|kel|desa|kecamatan|agama|status|pekerjaan|kewarganegaraan|berlaku|gol)/i', strtolower($nextLineClean))) {
                                $data['nama_lengkap'] = $nextLineClean;
                                break 2; // keluar dari 2 loop sekaligus
                            }
                        }
                    }
                }
            }

            if (str_contains($lower, 'tempat') && str_contains($lower, 'lahir')) {
                if (preg_match('/tempat.*lahir[:\-]?\s*(.+)/i', $line, $m)) {
                    $split = explode(',', $m[1]);
                    $data['tempat_lahir'] = trim($split[0] ?? '');
                    $data['tanggal_lahir'] = $this->parseDate($split[1] ?? '');
                }
            }

            if (!$data['jenis_kelamin'] && str_contains($lower, 'laki')) $data['jenis_kelamin'] = 'Laki-laki';
            if (!$data['jenis_kelamin'] && str_contains($lower, 'perempuan')) $data['jenis_kelamin'] = 'Perempuan';

            if (!$data['golongan_darah'] && str_contains($lower, 'gol') && preg_match('/[ABO\-]{1,2}/', $line, $m)) {
                $data['golongan_darah'] = strtoupper($m[0]);
            }

            if (str_starts_with($lower, 'alamat')) {
                $alamat = preg_replace('/alamat[:\-]?\s*/i', '', $line);
                $alamat .= ' ' . ($lines[$i + 1] ?? '');
                $data['alamat_lengkap'] = trim($alamat);
                $i++;
            }

            if (preg_match('/(\d{3})[\/\-](\d{3})/', $line, $m)) {
                $data['rt'] = $m[1];
                $data['rw'] = $m[2];
            }

            if (!$data['kel_desa'] && str_contains($lower, 'kel') && str_contains($lower, 'desa')) {
                $data['kel_desa'] = trim(preg_replace('/(kel\/?desa)[:\-]?\s*/i', '', $line));
            }

            if (!$data['kecamatan'] && str_contains($lower, 'kecamatan')) {
                $data['kecamatan'] = trim(preg_replace('/kecamatan[:\-]?\s*/i', '', $line));
            }

            if (!$data['agama'] && str_contains($lower, 'agama')) {
                $data['agama'] = trim(preg_replace('/agama[:\-]?\s*/i', '', $line));
            }

            if (!$data['status_perkawinan'] && str_contains($lower, 'perkawinan')) {
                if (str_contains($lower, 'kawin')) $data['status_perkawinan'] = 'Kawin';
                elseif (str_contains($lower, 'belum')) $data['status_perkawinan'] = 'Belum Kawin';
                elseif (str_contains($lower, 'cerai')) $data['status_perkawinan'] = 'Cerai';
            }

            if (!$data['pekerjaan'] && str_contains($lower, 'pekerjaan')) {
                $next = $lines[$i + 1] ?? '';
                $data['pekerjaan'] = $next;
                $i++;
            }

            if (!$data['kewarganegaraan'] && str_contains($lower, 'kewarganegaraan')) {
                $data['kewarganegaraan'] = str_contains($lower, 'wna') ? 'WNA' : 'WNI';
            }

            if (!$data['provinsi'] && str_contains($lower, 'provinsi')) {
                $data['provinsi'] = trim(str_ireplace('provinsi', '', $line));
            }

            if (!$data['kota'] && (str_contains($lower, 'kabupaten') || str_contains($lower, 'kota'))) {
                $data['kota'] = trim(preg_replace('/(kabupaten|kota)/i', '', $line));
            }
        }
        foreach ($data as $key => $value) {
            $data[$key] = trim($value, ":- \t\n\r\0\x0B");
        }


        $data['alamat_lengkap'] = implode(', ', array_filter([
            $data['alamat_lengkap'],
            $data['rt'] ? 'RT ' . $data['rt'] : '',
            $data['rw'] ? 'RW ' . $data['rw'] : '',
            $data['kel_desa'],
            $data['kecamatan'],
            $data['kota'],
            $data['provinsi']
        ]));

        return $data;
    }

    private function parseDate($tgl)
    {
        $tgl = str_replace(['.', '/', '\\'], '-', trim($tgl));
        try {
            return Carbon::createFromFormat('d-m-Y', $tgl)->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }

    private function isValidNameCandidate(string $name): bool
    {
        $keywords = [
            'provinsi',
            'kabupaten',
            'kota',
            'nik',
            'tempat',
            'lahir',
            'jenis',
            'alamat',
            'rt',
            'rw',
            'kel',
            'desa',
            'kecamatan',
            'agama',
            'status',
            'perkawinan',
            'pekerjaan',
            'kewarganegaraan',
            'berlaku'
        ];
        $nameLower = strtolower($name);

        foreach ($keywords as $keyword) {
            if (str_contains($nameLower, $keyword)) return false;
        }

        return str_word_count($name) >= 2 && strlen($name) >= 5;
    }
}
