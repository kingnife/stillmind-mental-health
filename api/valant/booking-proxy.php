<?php
/**
 * STILLMIND MENTAL HEALTH LLC
 * Stateless Valant Booking Proxy Endpoint
 *
 * HIPAA COMPLIANCE:
 * - Emits strict no-cache, no-store headers.
 * - Does not accept, log, or persist PHI.
 */

namespace Stillmind\Valant;

require_once __DIR__ . '/ValantSchedulingClient.php';

// Security Headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

$action = $_GET['action'] ?? 'booking_url';
$serviceId = isset($_GET['service']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['service']) : null;
$location = isset($_GET['location']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['location']) : 'indianapolis';

$client = new ValantSchedulingClient(['sandbox' => true]);

try {
    switch ($action) {
        case 'services':
            echo json_encode([
                'status' => 'success',
                'data' => $client->getServiceTypes()
            ]);
            break;

        case 'slots':
            $start = $_GET['start'] ?? date('Y-m-d');
            $end = $_GET['end'] ?? date('Y-m-d', strtotime('+14 days'));
            echo json_encode([
                'status' => 'success',
                'data' => $client->getAvailableSlots($start, $end, $serviceId)
            ]);
            break;

        case 'booking_url':
        default:
            echo json_encode([
                'status' => 'success',
                'url' => $client->getHostedBookingUrl($serviceId, $location),
                'target' => '_blank',
                'note' => 'Opens Valant HIPAA-compliant patient intake in a new tab.'
            ]);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Stateless booking proxy unavailable. Please contact office directly.'
    ]);
}
