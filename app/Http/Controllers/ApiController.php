<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    private $apiUrl = 'http://10.99.23.111/WS_CLIENT/Malang_alizah/index.php';

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, [
                    'method' => 'login',
                    'username' => $request->username,
                    'password' => $request->password,
                ]);

            return response()->json($response->json() ?: [], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server tidak dapat terhubung ke API. Pastikan server memiliki akses ke ' . parse_url($this->apiUrl, PHP_URL_HOST)
            ], 500);
        }
    }

    public function listPerizinan(Request $request)
    {
        $request->validate([
            'unit' => 'required',
        ]);

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, [
                    'method' => 'ListPerizinan',
                    'unit' => $request->unit,
                ]);

            return response()->json($response->json() ?: [], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server tidak dapat terhubung ke API'
            ], 500);
        }
    }
}
