<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    //
    public function handle(Request $request)
    {


        $update = $request->all();

        // Ambil teks pesan dan chat id dari payload Telegram
        $message = $update['message']['text'] ?? null;
        $chat_id = $update['message']['chat']['id'] ?? null;

        if (!$message || !$chat_id) {
            return response()->noContent();
        }

        // Deteksi command /start
        if ($message === '/start') {
            $reply = "👋 Hallo, selamat datang di *Espay PBA!*";
        } else if ($message === '/about') {
            $reply = "Ini adalah bot untuk Espay PBA";
        } else {
            $reply = "Ketik /start untuk memulai.";
        }

        // Kirim balasan langsung ke chat yang sama
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => $chat_id,   // otomatis dari pesan yang diterima
            'text' => $reply,
            'parse_mode' => 'Markdown'
        ]);

        return response()->json(['ok' => true]);
    }
}
