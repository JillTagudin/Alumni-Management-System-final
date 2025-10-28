<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Alumni;

class AIAnalyticsService
{
    private $provider;
    private $apiKey;
    private $baseUrl;
    
    public function __construct()
    {
        $this->provider = config('services.ai.provider', 'claude'); // Default to Claude
        $this->setupProvider();
    }
    
    private function setupProvider()
    {
        switch ($this->provider) {
            case 'claude':
                $this->apiKey = config('services.anthropic.api_key');
                $this->baseUrl = 'https://api.anthropic.com/v1/messages';
                break;
            case 'openai':
                $this->apiKey = config('services.openai.api_key');
                $this->baseUrl = 'https://api.openai.com/v1/chat/completions';
                break;
            case 'huggingface':
                $this->apiKey = config('services.huggingface.api_key');
                $this->baseUrl = 'https://api-inference.huggingface.co/models/microsoft/DialoGPT-large';
                break;
        }
    }
    
    public function generateInsights($data, $context = '')
    {
        // Cache insights for 5 minutes to allow responsive updates
        $cacheKey = 'ai_insights_' . md5(json_encode($data) . $context);
        
        return Cache::remember($cacheKey, 300, function () use ($data, $context) {
            try {
                switch ($this->provider) {
                    case 'claude':
                        return $this->callClaudeAPI($data, $context);
                    case 'openai':
                        return $this->callOpenAIAPI($data, $context);
                    case 'huggingface':
                        return $this->callHuggingFaceAPI($data, $context);
                    default:
                        return $this->getFallbackInsights($data);
                }
            } catch (\Exception $e) {
                Log::error('AI Analytics Error: ' . $e->getMessage());
                return $this->getFallbackInsights($data);
            }
        });
    }
    
    public function generateChartData($chartType = 'all')
    {
        // Cache chart data for 2 minutes to allow responsive updates
        $cacheKey = 'ai_chart_data_' . $chartType;
        
        return Cache::remember($cacheKey, 120, function () use ($chartType) {
            try {
                $alumniData = $this->getAlumniData();
                
                switch ($this->provider) {
                    case 'claude':
                        return $this->generateClaudeChartData($alumniData, $chartType);
                    case 'openai':
                        return $this->generateOpenAIChartData($alumniData, $chartType);
                    default:
                        return $this->getFallbackChartData($alumniData, $chartType);
                }
            } catch (\Exception $e) {
                Log::error('AI Chart Generation Error: ' . $e->getMessage());
                return $this->getFallbackChartData($this->getAlumniData(), $chartType);
            }
        });
    }
    
    private function getAlumniData()
    {
        $alumni = Alumni::all();
        
        return [
            'total_count' => $alumni->count(),
            'gender_distribution' => $alumni->groupBy('Gender')->map->count(),
            'age_groups' => $alumni->groupBy(function($item) {
                $age = $item->Age;
                if ($age < 25) return '18-24';
                if ($age < 35) return '25-34';
                if ($age < 45) return '35-44';
                if ($age < 55) return '45-54';
                return '55+';
            })->map->count(),
            'courses' => $alumni->groupBy('Course')->map->count(),
            'employment_status' => $alumni->whereNotNull('Occupation')->groupBy('Occupation')->map->count(),
            'membership_status' => $alumni->groupBy('membership_status')->map->count(),
            'membership_types' => $alumni->groupBy('membership_type')->map->count(),
            'batch_years' => $alumni->groupBy('Batch')->map->count(),
            'companies' => $alumni->whereNotNull('Company')->groupBy('Company')->map->count(),
            'registration_trends' => $alumni->groupBy(function($item) {
                return $item->created_at->format('Y-m');
            })->map->count()->sortKeys()
        ];
    }
    
    private function generateClaudeChartData($data, $chartType)
    {
        $prompt = $this->buildChartPrompt($data, $chartType);
        
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01'
        ])->post($this->baseUrl, [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 2000,
            'messages' => [[
                'role' => 'user',
                'content' => $prompt
            ]]
        ]);
        
        return $this->parseChartResponse($response->json());
    }
    
    private function generateOpenAIChartData($data, $chartType)
    {
        $prompt = $this->buildChartPrompt($data, $chartType);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [[
                'role' => 'user',
                'content' => $prompt
            ]],
            'max_tokens' => 2000
        ]);
        
        return $this->parseOpenAIChartResponse($response->json());
    }
    
    private function buildChartPrompt($data, $chartType)
    {
        return "Generate comprehensive chart data for alumni analytics dashboard.\n\n" .
               "Alumni Data: " . json_encode($data) . "\n\n" .
               "Chart Type: {$chartType}\n\n" .
               "Please generate Chart.js compatible data for the following charts:\n" .
               "1. Gender Distribution (pie chart)\n" .
               "2. Age Groups (bar chart)\n" .
               "3. Course Distribution (doughnut chart)\n" .
               "4. Employment Status (horizontal bar chart)\n" .
               "5. Membership Status (pie chart)\n" .
               "6. Batch Years (line chart)\n" .
               "7. Registration Trends (line chart)\n" .
               "8. Top Companies (bar chart)\n\n" .
               "Format as valid JSON with this structure:\n" .
               "{\n" .
               "  \"charts\": {\n" .
               "    \"gender\": {\"type\": \"pie\", \"data\": {...}, \"options\": {...}},\n" .
               "    \"age_groups\": {\"type\": \"bar\", \"data\": {...}, \"options\": {...}},\n" .
               "    \"courses\": {\"type\": \"doughnut\", \"data\": {...}, \"options\": {...}},\n" .
               "    \"employment\": {\"type\": \"horizontalBar\", \"data\": {...}, \"options\": {...}},\n" .
               "    \"membership_status\": {\"type\": \"pie\", \"data\": {...}, \"options\": {...}},\n" .
               "    \"batch_years\": {\"type\": \"line\", \"data\": {...}, \"options\": {...}},\n" .
               "    \"registration_trends\": {\"type\": \"line\", \"data\": {...}, \"options\": {...}},\n" .
               "    \"companies\": {\"type\": \"bar\", \"data\": {...}, \"options\": {...}}\n" .
               "  },\n" .
               "  \"insights\": {\n" .
               "    \"trends\": [...],\n" .
               "    \"recommendations\": [...],\n" .
               "    \"predictions\": [...]\n" .
               "  }\n" .
               "}\n\n" .
               "Include proper Chart.js data structure with labels, datasets, backgroundColor, borderColor, and responsive options.";
    }
    
    private function parseChartResponse($response)
    {
        $content = $response['content'][0]['text'] ?? '';
        return $this->extractChartJSONFromResponse($content);
    }
    
    private function parseOpenAIChartResponse($response)
    {
        $content = $response['choices'][0]['message']['content'] ?? '';
        return $this->extractChartJSONFromResponse($content);
    }
    
    private function extractChartJSONFromResponse($content)
    {
        // Try to extract JSON from response
        preg_match('/\{.*\}/s', $content, $matches);
        
        if (!empty($matches)) {
            $decoded = json_decode($matches[0], true);
            if ($decoded && isset($decoded['charts'])) {
                return $decoded;
            }
        }
        
        // Fallback to basic chart data
        return $this->getFallbackChartData($this->getAlumniData(), 'all');
    }
    
    private function getFallbackChartData($data, $chartType)
    {
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'];
        
        return [
            'charts' => [
                'gender' => [
                    'type' => 'pie',
                    'data' => [
                        'labels' => array_keys($data['gender_distribution']->toArray()),
                        'datasets' => [[
                            'data' => array_values($data['gender_distribution']->toArray()),
                            'backgroundColor' => array_slice($colors, 0, count($data['gender_distribution']))
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ],
                'age_groups' => [
                    'type' => 'bar',
                    'data' => [
                        'labels' => array_keys($data['age_groups']->toArray()),
                        'datasets' => [[
                            'label' => 'Alumni Count',
                            'data' => array_values($data['age_groups']->toArray()),
                            'backgroundColor' => '#36A2EB'
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ],
                'courses' => [
                    'type' => 'doughnut',
                    'data' => [
                        'labels' => array_keys($data['courses']->toArray()),
                        'datasets' => [[
                            'data' => array_values($data['courses']->toArray()),
                            'backgroundColor' => $colors
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ],
                'employment' => [
                    'type' => 'horizontalBar',
                    'data' => [
                        'labels' => array_slice(array_keys($data['employment_status']->toArray()), 0, 10),
                        'datasets' => [[
                            'label' => 'Alumni Count',
                            'data' => array_slice(array_values($data['employment_status']->toArray()), 0, 10),
                            'backgroundColor' => '#4BC0C0'
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ],
                'membership_status' => [
                    'type' => 'pie',
                    'data' => [
                        'labels' => array_keys($data['membership_status']->toArray()),
                        'datasets' => [[
                            'data' => array_values($data['membership_status']->toArray()),
                            'backgroundColor' => ['#9966FF', '#FF9F40']
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ],
                'batch_years' => [
                    'type' => 'line',
                    'data' => [
                        'labels' => array_keys($data['batch_years']->toArray()),
                        'datasets' => [[
                            'label' => 'Alumni Count',
                            'data' => array_values($data['batch_years']->toArray()),
                            'borderColor' => '#FF6384',
                            'fill' => false
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ],
                'registration_trends' => [
                    'type' => 'line',
                    'data' => [
                        'labels' => array_keys($data['registration_trends']->toArray()),
                        'datasets' => [[
                            'label' => 'Registrations',
                            'data' => array_values($data['registration_trends']->toArray()),
                            'borderColor' => '#36A2EB',
                            'fill' => true,
                            'backgroundColor' => 'rgba(54, 162, 235, 0.1)'
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ],
                'companies' => [
                    'type' => 'bar',
                    'data' => [
                        'labels' => array_slice(array_keys($data['companies']->toArray()), 0, 10),
                        'datasets' => [[
                            'label' => 'Alumni Count',
                            'data' => array_slice(array_values($data['companies']->toArray()), 0, 10),
                            'backgroundColor' => '#FFCE56'
                        ]]
                    ],
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false]
                ]
            ],
            'insights' => [
                'trends' => [
                    'Total alumni: ' . $data['total_count'],
                    'Most common course: ' . $data['courses']->keys()->first(),
                    'Gender distribution shows ' . $data['gender_distribution']->keys()->first() . ' majority'
                ],
                'recommendations' => [
                    'Focus on engaging alumni from underrepresented courses',
                    'Develop targeted programs for different age groups',
                    'Strengthen industry partnerships with top companies'
                ],
                'predictions' => [
                    'Continued growth in alumni registrations expected',
                    'Increasing diversity in career paths',
                    'Growing engagement in membership programs'
                ]
            ]
        ];
    }
    
    private function callClaudeAPI($data, $context)
    {
        $prompt = $this->buildAnalyticsPrompt($data, $context);
        
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01'
        ])->post($this->baseUrl, [
            'model' => 'claude-3-haiku-20240307', // Cheaper model
            'max_tokens' => 1000,
            'messages' => [[
                'role' => 'user',
                'content' => $prompt
            ]]
        ]);
        
        return $this->parseClaudeResponse($response->json());
    }
    
    private function callOpenAIAPI($data, $context)
    {
        $prompt = $this->buildAnalyticsPrompt($data, $context);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl, [
            'model' => 'gpt-3.5-turbo', // Cheaper than GPT-4
            'messages' => [[
                'role' => 'user',
                'content' => $prompt
            ]],
            'max_tokens' => 1000
        ]);
        
        return $this->parseOpenAIResponse($response->json());
    }
    
    private function callHuggingFaceAPI($data, $context)
    {
        // Simplified prompt for free model
        $prompt = "Analyze alumni data: " . json_encode($data) . ". Provide insights.";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->baseUrl, [
            'inputs' => $prompt,
            'parameters' => [
                'max_length' => 500,
                'temperature' => 0.7
            ]
        ]);
        
        return $this->parseHuggingFaceResponse($response->json());
    }
    
    private function buildAnalyticsPrompt($data, $context)
    {
        $totalAlumni = $data['total_alumni'] ?? 0;
        $recentRegistrations = $data['recent_registrations'] ?? 0;
        $totalUsers = $data['total_users'] ?? 0;
        
        return "You are an alumni analytics expert. Analyze this alumni management system data and provide specific, actionable insights:\n\n" .
               "ALUMNI DATA SUMMARY:\n" .
               "- Total Alumni: {$totalAlumni}\n" .
               "- Recent Registrations (last week): {$recentRegistrations}\n" .
               "- Total Users: {$totalUsers}\n" .
               "- Gender Distribution: " . json_encode($data['alumni_by_gender'] ?? []) . "\n" .
               "- Age Groups: " . json_encode($data['alumni_by_age_group'] ?? []) . "\n" .
               "- Employment Status: " . json_encode($data['alumni_employment_status'] ?? []) . "\n\n" .
               "ANALYSIS REQUIREMENTS:\n" .
               "Provide insights in JSON format with these specific categories:\n\n" .
               "1. trends: Identify 2-3 key patterns (e.g., registration growth, demographic shifts, engagement patterns)\n" .
               "2. anomalies: Highlight any concerning patterns (e.g., low engagement, demographic imbalances, registration drops)\n" .
               "3. recommendations: Provide 2-3 specific actionable recommendations for improving alumni engagement\n" .
               "4. predictions: Make 1-2 data-driven predictions for the next month based on current trends\n\n" .
               "EXAMPLE OUTPUT FORMAT:\n" .
               '{\n' .
               '  "trends": ["Registration increased by X% this week", "Most alumni are in age group Y"],\n' .
               '  "anomalies": ["Low engagement in Z demographic"],\n' .
               '  "recommendations": ["Focus outreach on underrepresented groups", "Implement targeted engagement campaigns"],\n' .
               '  "predictions": ["Expect continued growth in registrations next month"]\n' .
               '}\n\n' .
               "Context: {$context}\n\n" .
               "Respond with ONLY valid JSON in the exact format shown above. Each array should contain 1-3 meaningful insights as strings.";
    }
    
    private function parseClaudeResponse($response)
    {
        $content = $response['content'][0]['text'] ?? '';
        return $this->extractJSONFromResponse($content);
    }
    
    private function parseOpenAIResponse($response)
    {
        $content = $response['choices'][0]['message']['content'] ?? '';
        return $this->extractJSONFromResponse($content);
    }
    
    private function parseHuggingFaceResponse($response)
    {
        $content = $response[0]['generated_text'] ?? '';
        return $this->extractJSONFromResponse($content);
    }
    
    private function extractJSONFromResponse($content)
    {
        // Try multiple approaches to find valid JSON
        $json = $this->findValidJSON($content);
        
        if ($json && $this->isValidInsightsStructure($json)) {
            // Ensure all values are strings, not arrays
            return $this->normalizeInsightsToStrings($json);
        }
        
        // Fallback: parse text manually and ensure strings
        $fallback = [
            'trends' => $this->extractSection($content, 'trend'),
            'anomalies' => $this->extractSection($content, 'anomal'),
            'recommendations' => $this->extractSection($content, 'recommend'),
            'predictions' => $this->extractSection($content, 'predict')
        ];
        
        return $this->normalizeInsightsToStrings($fallback);
    }
    
    private function findValidJSON($content)
    {
        // Method 1: Look for JSON in code blocks
        if (preg_match('/```(?:json)?\s*({.*?})\s*```/s', $content, $matches)) {
            $decoded = json_decode($matches[1], true);
            if ($this->isValidJSON($matches[1]) && $decoded) {
                return $decoded;
            }
        }
        
        // Method 2: Find balanced braces with proper JSON structure
        if (preg_match('/{[^{}]*(?:{[^{}]*}[^{}]*)*}/s', $content, $matches)) {
            if ($this->isValidJSON($matches[0])) {
                return json_decode($matches[0], true);
            }
        }
        
        // Method 3: Look for the first complete JSON object
        $start = strpos($content, '{');
        if ($start !== false) {
            $braceCount = 0;
            $inString = false;
            $escaped = false;
            
            for ($i = $start; $i < strlen($content); $i++) {
                $char = $content[$i];
                
                if (!$inString) {
                    if ($char === '{') $braceCount++;
                    elseif ($char === '}') $braceCount--;
                    elseif ($char === '"') $inString = true;
                } else {
                    if ($char === '"' && !$escaped) $inString = false;
                    $escaped = ($char === '\\' && !$escaped);
                }
                
                if ($braceCount === 0 && $i > $start) {
                    $jsonStr = substr($content, $start, $i - $start + 1);
                    if ($this->isValidJSON($jsonStr)) {
                        return json_decode($jsonStr, true);
                    }
                    break;
                }
            }
        }
        
        return null;
    }
    
    private function isValidJSON($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    private function isValidInsightsStructure($data)
    {
        return is_array($data) && (
            isset($data['trends']) || 
            isset($data['anomalies']) || 
            isset($data['recommendations']) || 
            isset($data['predictions'])
        );
    }
    
    private function normalizeInsightsToStrings($insights)
    {
        $normalized = [];
        
        foreach (['trends', 'anomalies', 'recommendations', 'predictions'] as $key) {
            if (isset($insights[$key])) {
                $value = $insights[$key];
                
                if (is_array($value)) {
                    // Convert array to array of strings
                    $normalized[$key] = array_map(function($item) {
                        return is_string($item) ? $item : (string)$item;
                    }, array_filter($value, function($item) {
                        return !empty(trim((string)$item)) && $item !== '}' && $item !== '},';
                    }));
                } else {
                    // Convert single value to array of strings
                    $stringValue = (string)$value;
                    if (!empty(trim($stringValue)) && $stringValue !== '}' && $stringValue !== '},') {
                        $normalized[$key] = [$stringValue];
                    } else {
                        $normalized[$key] = [];
                    }
                }
            } else {
                $normalized[$key] = [];
            }
        }
        
        return $normalized;
    }
    
    private function extractSection($content, $keyword)
    {
        $lines = explode("\n", $content);
        $section = [];
        $capturing = false;
        
        foreach ($lines as $line) {
            if (stripos($line, $keyword) !== false) {
                $capturing = true;
                continue;
            }
            
            if ($capturing && (empty(trim($line)) || stripos($line, ':') !== false)) {
                break;
            }
            
            if ($capturing) {
                $section[] = trim($line, '- •');
            }
        }
        
        return array_filter($section);
    }
    
    /**
     * Clear all analytics cache
     */
    public function clearAnalyticsCache()
    {
        // Clear insights cache for all possible data combinations
        Cache::forget('ai_insights_' . md5(json_encode($this->getAlumniData())));
        
        // Clear chart data cache for all chart types
        $chartTypes = ['all', 'gender', 'courses', 'batch_years', 'employment', 'companies'];
        foreach ($chartTypes as $chartType) {
            Cache::forget('ai_chart_data_' . $chartType);
        }
        
        // Clear any other analytics-related cache keys
        Cache::flush(); // This is aggressive but ensures all analytics cache is cleared
    }
    
    /**
     * Clear insights cache specifically
     */
    public function clearInsightsCache($data = null, $context = '')
    {
        if ($data === null) {
            $data = $this->getAlumniData();
        }
        Cache::forget('ai_insights_' . md5(json_encode($data) . $context));
    }
    
    /**
     * Clear chart data cache specifically
     */
    public function clearChartDataCache()
    {
        $chartTypes = ['all', 'gender', 'courses', 'batch_years', 'employment', 'companies'];
        foreach ($chartTypes as $chartType) {
            Cache::forget('ai_chart_data_' . $chartType);
        }
    }
    
    /**
     * Generate insights with option to bypass cache
     */
    public function generateInsightsWithRefresh($data, $context = '', $forceRefresh = false)
    {
        if ($forceRefresh) {
            $this->clearInsightsCache($data, $context);
        }
        return $this->generateInsights($data, $context);
    }
    
    /**
     * Generate chart data with option to bypass cache
     */
    public function generateChartDataWithRefresh($chartType = 'all', $forceRefresh = false)
    {
        if ($forceRefresh) {
            $this->clearChartDataCache();
        }
        return $this->generateChartData($chartType);
    }

    private function getFallbackInsights($data)
    {
        // Static insights when AI is unavailable
        $totalAlumni = $data['total_alumni'] ?? 0;
        $recentRegistrations = $data['recent_registrations'] ?? 0;
        
        return [
            'trends' => [
                "Total alumni count: {$totalAlumni}",
                "Recent registrations: {$recentRegistrations}",
                "System is operating normally"
            ],
            'anomalies' => [],
            'recommendations' => [
                "Continue monitoring alumni engagement",
                "Regular data backup recommended"
            ],
            'predictions' => [
                "Steady growth expected based on current trends"
            ]
        ];
    }

    /**
     * Generate comprehensive report insights using AI
     */
    public function generateReportInsights($data)
    {
        $cacheKey = 'ai_report_insights_' . md5(json_encode($data));
        
        return Cache::remember($cacheKey, 600, function () use ($data) { // Cache for 10 minutes
            try {
                switch ($this->provider) {
                    case 'claude':
                        return $this->generateClaudeReportInsights($data);
                    case 'openai':
                        return $this->generateOpenAIReportInsights($data);
                    default:
                        return $this->getFallbackReportInsights($data);
                }
            } catch (\Exception $e) {
                Log::error('AI Report Insights Error: ' . $e->getMessage());
                return $this->getFallbackReportInsights($data);
            }
        });
    }

    private function generateClaudeReportInsights($data)
    {
        $prompt = $this->buildReportInsightsPrompt($data);
        
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01'
        ])->post($this->baseUrl, [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 3000,
            'messages' => [[
                'role' => 'user',
                'content' => $prompt
            ]]
        ]);
        
        return $this->parseReportInsightsResponse($response->json());
    }

    private function generateOpenAIReportInsights($data)
    {
        $prompt = $this->buildReportInsightsPrompt($data);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [[
                'role' => 'user',
                'content' => $prompt
            ]],
            'max_tokens' => 3000
        ]);
        
        return $this->parseReportInsightsResponse($response->json());
    }

    private function buildReportInsightsPrompt($data)
    {
        $totalAlumni = $data['total_alumni'] ?? 0;
        $reportType = $data['report_type'] ?? 'general';
        $sections = $data['data_sections'] ?? [];
        
        return "You are an expert alumni data analyst. Generate a comprehensive report analysis based on the following alumni management system data:\n\n" .
               "REPORT DETAILS:\n" .
               "- Report Type: {$reportType}\n" .
               "- Total Alumni: {$totalAlumni}\n" .
               "- Generation Date: " . ($data['generation_date'] ?? now()->format('Y-m-d')) . "\n" .
               "- Data Sections: " . json_encode($sections) . "\n\n" .
               "ANALYSIS REQUIREMENTS:\n" .
               "Generate a comprehensive analysis in JSON format with these sections:\n\n" .
               "1. summary: A 2-3 sentence executive summary of the overall alumni status\n" .
               "2. key_findings: Array of 3-5 most important data points or discoveries\n" .
               "3. trends: Array of 2-4 observable patterns or trends in the data\n" .
               "4. recommendations: Array of 3-5 specific, actionable recommendations for improvement\n" .
               "5. predictions: Array of 2-3 data-driven predictions for future alumni engagement\n\n" .
               "EXAMPLE OUTPUT FORMAT:\n" .
               '{\n' .
               '  "summary": "The alumni network shows strong growth with X total members, demonstrating healthy engagement across multiple demographics.",\n' .
               '  "key_findings": [\n' .
               '    "Gender distribution shows Y% male and Z% female alumni",\n' .
               '    "Most alumni are employed in the technology sector",\n' .
               '    "Recent registration trends indicate growing interest"\n' .
               '  ],\n' .
               '  "trends": [\n' .
               '    "Steady increase in alumni registrations over the past quarter",\n' .
               '    "Higher engagement rates among recent graduates"\n' .
               '  ],\n' .
               '  "recommendations": [\n' .
               '    "Implement targeted outreach for underrepresented demographics",\n' .
               '    "Develop mentorship programs connecting recent and experienced alumni",\n' .
               '    "Create industry-specific networking events"\n' .
               '  ],\n' .
               '  "predictions": [\n' .
               '    "Expected 15-20% growth in registrations next quarter",\n' .
               '    "Increased demand for professional development programs"\n' .
               '  ]\n' .
               '}\n\n' .
               "Provide insights that are:\n" .
               "- Specific and data-driven\n" .
               "- Actionable and practical\n" .
               "- Professional and suitable for executive reporting\n" .
               "- Based on the actual data provided\n\n" .
               "Respond with ONLY valid JSON in the exact format shown above.";
    }

    private function parseReportInsightsResponse($response)
    {
        if ($this->provider === 'claude') {
            $content = $response['content'][0]['text'] ?? '';
        } else {
            $content = $response['choices'][0]['message']['content'] ?? '';
        }
        
        return $this->extractReportInsightsJSON($content);
    }

    private function extractReportInsightsJSON($content)
    {
        // Try to find valid JSON in the response
        $json = $this->findValidJSON($content);
        
        if ($json && $this->isValidReportInsightsStructure($json)) {
            return $json;
        }
        
        // Fallback parsing
        return $this->getFallbackReportInsights([]);
    }

    private function isValidReportInsightsStructure($json)
    {
        $requiredKeys = ['summary', 'key_findings', 'trends', 'recommendations', 'predictions'];
        
        foreach ($requiredKeys as $key) {
            if (!isset($json[$key])) {
                return false;
            }
        }
        
        return true;
    }

    private function getFallbackReportInsights($data)
    {
        $totalAlumni = $data['total_alumni'] ?? 0;
        
        return [
            'summary' => "This comprehensive alumni report provides detailed insights into the current status of {$totalAlumni} registered alumni members, showcasing demographic distributions, engagement patterns, and growth trends.",
            'key_findings' => [
                "Total registered alumni: {$totalAlumni}",
                "Report generated with real-time data analysis",
                "Multiple demographic categories tracked and analyzed",
                "Comprehensive data coverage across all alumni segments"
            ],
            'trends' => [
                "Consistent data collection and management practices",
                "Regular monitoring of alumni engagement metrics",
                "Systematic approach to demographic tracking"
            ],
            'recommendations' => [
                "Continue regular data collection and analysis",
                "Implement targeted outreach programs for different demographics",
                "Develop engagement strategies based on alumni preferences",
                "Establish regular reporting cycles for stakeholder updates",
                "Consider implementing feedback mechanisms for continuous improvement"
            ],
            'predictions' => [
                "Continued growth in alumni database with proper outreach",
                "Improved engagement through targeted communication strategies",
                "Enhanced data quality through systematic collection processes"
            ]
        ];
    }
}

