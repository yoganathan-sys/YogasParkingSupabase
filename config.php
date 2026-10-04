<?php
/**
 * YOGA'S PARKING SYSTEM
 * Supabase REST API configuration
 *
 * Required environment variables:
 * SUPABASE_URL
 * SUPABASE_SERVICE_ROLE_KEY
 */

$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY');

if (!$supabaseUrl || !$supabaseKey) {
    die("Supabase configuration is missing. Set SUPABASE_URL and SUPABASE_SERVICE_ROLE_KEY.");
}

$supabaseUrl = rtrim($supabaseUrl, '/');

/**
 * Call Supabase PostgREST API.
 *
 * @param string $method GET|POST|PATCH|DELETE
 * @param string $endpoint Path beginning with /
 * @param array|null $data JSON body
 * @param array $extraHeaders Additional headers
 * @return array{status:int, body:mixed, raw:string}
 */
function supabaseRequest($method, $endpoint, $data = null, $extraHeaders = [])
{
    global $supabaseUrl, $supabaseKey;

    $ch = curl_init($supabaseUrl . $endpoint);

    $headers = [
        'apikey: ' . $supabaseKey,
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    foreach ($extraHeaders as $header) {
        $headers[] = $header;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 10
    ]);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $raw = curl_exec($ch);

    if ($raw === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("Supabase request failed: " . $error);
    }

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($raw, true);

    return [
        'status' => $status,
        'body' => $decoded,
        'raw' => $raw
    ];
}
?>
