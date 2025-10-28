<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BalanceUpdateController extends Controller
{


    public function index()
    {
        try {
            // Add timeout and retry settings for external API connection
            $response = Http::withToken(env('REMOTE_API_TOKEN'))
                ->acceptJson()
                ->timeout(30)
                ->retry(3, 1000)
                ->get(env('REMOTE_API_URL') . '/history');

            if ($response->successful()) {
                $history = $response->json();
                return view('admin.balanceupdate.index', compact('history'));
            } else {
                // Log the error for debugging
                \Log::error('Balance Update API Error: ' . $response->status() . ' - ' . $response->body());
                
                // Return view with empty history and error message
                $history = [];
                $error = 'Unable to connect to balance update system. Status: ' . $response->status();
                return view('admin.balanceupdate.index', compact('history', 'error'));
            }
        } catch (\Exception $e) {
            // Handle connection errors (DNS, timeout, etc.)
            \Log::error('Balance Update Connection Error: ' . $e->getMessage());
            
            $history = [];
            $error = 'Connection to balance update system failed: ' . $e->getMessage();
            return view('admin.balanceupdate.index', compact('history', 'error'));
        }
    }
}