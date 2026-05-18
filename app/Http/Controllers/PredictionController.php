<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictionController extends Controller
{
    public function index()
    {
        $templates = [
            'prediksi_suhu' => 'Bagaimana prediksi suhu untuk 24 jam ke depan?',
            'penyiraman_diperlukan' => 'Berdasarkan data hari ini, apakah penyiraman otomatis diperlukan besok?',
            'analisis_anomali' => 'Tolong analisis anomali pada data sensor hari ini.',
        ];

        return view('prediction', compact('templates'));
    }

    public function ask(Request $request)
    {
        $request->validate([
            'template_key' => 'required|string',
        ]);

        $templates = [
            'prediksi_suhu' => 'Bagaimana prediksi suhu untuk 24 jam ke depan?',
            'penyiraman_diperlukan' => 'Berdasarkan data hari ini, apakah penyiraman otomatis diperlukan besok?',
            'analisis_anomali' => 'Tolong analisis anomali pada data sensor hari ini.',
        ];

        if (!array_key_exists($request->template_key, $templates)) {
            return response()->json(['error' => 'Template tidak valid'], 400);
        }

        $prompt = $templates[$request->template_key];

        $apiKey = config('services.gemini.key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API Key belum dikonfigurasi di services.php'], 500);
        }

        $context = "Anda adalah asisten AI untuk sistem IoT Jamkot. Jawablah dengan singkat, padat, dan ramah. Gunakan data yang relevan jika tersedia.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $context . "\n\nPertanyaan: " . $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Gagal menghubungi Gemini API: ' . $response->body()], 500);
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada jawaban.';

        return response()->json(['response' => $text]);
    }
}
