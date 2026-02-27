<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class StreamController extends Controller
{
    private string $amsBase = 'http://host.docker.internal:5080/LiveApp/rest/v2';

    public function index()
    {
        try {
            $response = Http::timeout(5)
                ->withBasicAuth(
                    config('services.ams.user'),
                    config('services.ams.password')
                )
                ->get("{$this->amsBase}/broadcasts/list/0/50");

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Gagal terhubung ke AMS, status: ' . $response->status()
                ], 500);
            }

            $streams = collect($response->json())
                ->filter(fn($s) => ($s['status'] ?? '') === 'broadcasting')
                ->values()
                ->map(fn($s) => [
                    'streamId'  => $s['streamId'],
                    'name'      => $s['name'] ?? $s['streamId'],
                    'startTime' => $s['startTime'] ?? null,
                ]);

            return response()->json($streams);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}