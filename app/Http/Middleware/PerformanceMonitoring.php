<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PerformanceLog;

class PerformanceMonitoring
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        // Log performance metrics
        PerformanceLog::create([
            'route' => $request->route()->getName(),
            'method' => $request->method(),
            'response_time' => ($endTime - $startTime) * 1000, // milliseconds
            'memory_usage' => $endMemory - $startMemory,
            'status_code' => $response->getStatusCode(),
            'user_id' => auth()->id(),
            'ip_address' => $request->ip()
        ]);
        
        return $response;
    }
}