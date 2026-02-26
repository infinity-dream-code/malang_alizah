<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    private $apiUrl = 'http://103.23.103.43/WS_CLIENT/Malang_alizah/index.php';

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'cf_turnstile_response' => 'required',
        ]);

        $turnstileRes = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret'),
            'response' => $request->cf_turnstile_response,
            'remoteip' => $request->ip(),
        ]);

        $turnstileData = $turnstileRes->json();
        if (!($turnstileData['success'] ?? false)) {
            return response()->json([
                'status' => 422,
                'message' => 'Validasi keamanan gagal. Silakan coba lagi.',
            ], 422);
        }

        try {
            $payload = [
                'method' => 'login',
                'username' => $request->username,
                'password' => $request->password,
            ];
            Log::info('[API] Login request', ['url' => $this->apiUrl, 'payload' => array_merge($payload, ['password' => '***'])]);
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            $json = $response->json() ?: [];
            Log::info('[API] Login response', ['status' => $response->status(), 'body' => $json]);
            return response()->json($json, $response->status());
        } catch (\Exception $e) {
            Log::error('[API] Login error', ['message' => $e->getMessage()]);
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
            'token' => 'required',
        ]);

        try {
            $unit = $request->unit;
            if (is_numeric($unit)) {
                $unit = (int) $unit;
            }
            $payload = [
                'token' => $request->token,
                'method' => 'ListPerizinan',
                'unit' => $unit,
            ];
            Log::info('[API] ListPerizinan request', ['url' => $this->apiUrl, 'payload' => $payload]);
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            $json = $response->json() ?: [];
            Log::info('[API] ListPerizinan response', ['status' => $response->status(), 'body' => $json]);
            return response()->json($json, $response->status());
        } catch (\Exception $e) {
            Log::error('[API] ListPerizinan error', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 500,
                'message' => 'Server tidak dapat terhubung ke API'
            ], 500);
        }
    }

    public function approvePerizinan(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'id_user' => 'required|integer',
            'unit' => 'required',
            'nmcust' => 'required',
            'date_action' => 'required',
            'status' => 'required|integer|in:1,2',
        ]);

        try {
            $payload = [
                'token' => $request->token,
                'method' => 'ApprovePerizinan',
                'id_user' => (int) $request->id_user,
                'unit' => (string) $request->unit,
                'nmcust' => $request->nmcust,
                'date_action' => $request->date_action,
                'status' => (int) $request->status,
            ];
            Log::info('[API] ApprovePerizinan request', ['payload' => $payload]);
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            $json = $response->json() ?: [];
            Log::info('[API] ApprovePerizinan response', ['status' => $response->status(), 'body' => $json]);
            return response()->json($json, $response->status());
        } catch (\Exception $e) {
            Log::error('[API] ApprovePerizinan error', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 500,
                'message' => 'Server tidak dapat terhubung ke API'
            ], 500);
        }
    }

    public function rekapPerizinan(Request $request)
    {
        $request->validate([
            'unit' => 'required',
            'token' => 'required',
        ]);

        try {
            $payload = [
                'token' => $request->token,
                'method' => 'rekapPerizinan',
                'unit' => (string) $request->unit,
                'q_unit' => trim((string) ($request->q_unit ?? '')),
                'q_nama' => trim((string) ($request->q_nama ?? '')),
                'q_no' => trim((string) ($request->q_no ?? '')),
                'tglMulai' => trim((string) ($request->tglMulai ?? '')),
                'tglSelesai' => trim((string) ($request->tglSelesai ?? '')),
                'page' => max(1, (int) ($request->page ?? 1)),
            ];
            Log::info('[API] rekapPerizinan request', ['payload' => $payload]);
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            $json = $response->json() ?: [];
            Log::info('[API] rekapPerizinan response', ['status' => $response->status()]);
            return response()->json($json, $response->status());
        } catch (\Exception $e) {
            Log::error('[API] rekapPerizinan error', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 500,
                'message' => 'Server tidak dapat terhubung ke API'
            ], 500);
        }
    }
}
