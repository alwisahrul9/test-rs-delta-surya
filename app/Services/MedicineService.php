<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MedicineService
{
    protected $email;
    protected $password;
    protected $baseUrl;

    public function __construct($email, $password, $baseUrl)
    {
        $this->email    = $email;
        $this->password = $password;
        $this->baseUrl  = $baseUrl;
    }

    // Mengambil Token dengan sistem Caching
    public function getAccessToken()
    {
        return Cache::remember('medicine_api_token', 86400, function () {
            $response = Http::post("{$this->baseUrl}/auth", [
                'email'    => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            if ($response->failed()) {
                activity('api-integration')
                    ->log("Gagal mendapat akases token");
            }

            throw new \Exception('Gagal melakukan autentikasi ke API RSDS');
        });
    }

    // Mendapatkan daftar obat
    public function getMedicines()
    {
        $token    = $this->getAccessToken();
        $response = Http::withToken($token)->get("{$this->baseUrl}/medicines");

        if ($response->failed()) {
            activity('api-integration')
                ->log("Gagal mengambil daftar obat dari API");
        }

        return $response->json('medicines') ?? [];
    }

    // Mencari harga obat berdasarkan waktu pemeriksaan
    public function getPriceByDate($medicineId, $examDate)
    {
        $token    = $this->getAccessToken();
        $response = Http::withToken($token)->get("{$this->baseUrl}/medicines/{$medicineId}/prices");

        if ($response->successful()) {
            $priceList = $response->json('prices');
            $dateStr   = Carbon::parse($examDate)->format('Y-m-d');

            $currentPrice = collect($priceList)->first(function ($p) use ($dateStr) {
                $start = $p['start_date']['value'];               // Berdasarkan JSON response API
                $end   = $p['end_date']['value'] ?? '9999-12-31'; // Sesuai logika fluktuatif
                return $dateStr >= $start && $dateStr <= $end;
            });

            return $currentPrice['unit_price'] ?? 0;
        }

        if ($response->failed()) {
            activity('api-integration')
                ->log("Gagal mengambil harga obat dari API untuk ID: {$medicineId}");
        }

        return 0;
    }
}
