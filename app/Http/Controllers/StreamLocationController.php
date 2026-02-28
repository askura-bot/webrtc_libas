<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\StreamLocation;

class StreamLocationController extends Controller
{
    /**
     * Simpan lokasi stream ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stream_id' => 'required|string|max:100',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address'   => 'nullable|string|max:500',
        ]);

        StreamLocation::create([
            'user_id'   => $request->user()->id,
            'stream_id' => $validated['stream_id'],
            'latitude'  => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address'   => $validated['address'] ?? null,
        ]);

        return response()->json(['message' => 'Lokasi berhasil disimpan'], 201);
    }

    /**
     * Proxy reverse geocode ke Nominatim — menghindari CORS dari browser.
     * GET /officer/geocode?lat=...&lng=...
     */
    public function geocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        try {
            $response = Http::withHeaders([
                'User-Agent'      => 'PolrestabesSemarang-LiveStream/1.0',
                'Accept-Language' => 'id',
            ])->timeout(8)->get('https://nominatim.openstreetmap.org/reverse', [
                'lat'    => $request->lat,
                'lon'    => $request->lng,
                'format' => 'json',
            ]);

            if ($response->failed()) {
                return response()->json(['address' => null, 'error' => 'Geocode gagal'], 502);
            }

            $data = $response->json();
            $addr = $data['address'] ?? [];

            $parts = array_filter([
                $addr['road']          ?? null,
                $addr['suburb']        ?? $addr['village'] ?? $addr['hamlet'] ?? null,
                $addr['city_district'] ?? $addr['county']  ?? null,
                $addr['city']          ?? $addr['town']    ?? null,
            ]);

            $address = implode(', ', $parts) ?: ($data['display_name'] ?? null);

            return response()->json(['address' => $address]);

        } catch (\Exception $e) {
            return response()->json(['address' => null, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil lokasi terakhir berdasarkan stream_id — untuk admin dashboard.
     * GET /admin/stream/location/{streamId}
     */
    public function show(string $streamId)
    {
        $location = StreamLocation::where('stream_id', $streamId)
            ->latest()
            ->first();

        if (!$location) {
            return response()->json(null);
        }

        return response()->json([
            'address'   => $location->address,
            'latitude'  => $location->latitude,
            'longitude' => $location->longitude,
        ]);
    }
}