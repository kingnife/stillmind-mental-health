<?php
/**
 * STILLMIND MENTAL HEALTH LLC
 * Valant Scheduling API Client & Adapter
 *
 * HIPAA COMPLIANCE & DATA ISOLATION NOTICE:
 * This client serves purely as a stateless adapter. Per client instructions, Valant
 * is the authoritative EMR and scheduling platform. No PHI is ever stored locally,
 * cached, or logged.
 */

namespace Stillmind\Valant;

require_once __DIR__ . '/ValantApiInterface.php';

class ValantSchedulingClient implements ValantApiInterface
{
    private bool $isSandbox;
    private string $practiceId;
    private string $apiBaseUrl;
    private ?string $clientId;
    private ?string $clientSecret;

    public function __construct(array $config = [])
    {
        // Default to sandbox until live Valant BAA & credentials are provided
        $this->isSandbox = $config['sandbox'] ?? true;
        $this->practiceId = $config['practiceId'] ?? 'stillmind-in';
        $this->apiBaseUrl = $config['apiBaseUrl'] ?? 'https://api.valant.io/scheduling/v1';
        $this->clientId = $config['clientId'] ?? null;
        $this->clientSecret = $config['clientSecret'] ?? null;
    }

    /**
     * Retrieve available appointment slots (Mocked/Sandbox until live BAA signed)
     */
    public function getAvailableSlots(string $startDate, string $endDate, ?string $serviceTypeId = null): array
    {
        if ($this->isSandbox) {
            return $this->generateSandboxSlots($startDate, $endDate, $serviceTypeId);
        }

        // Live API call placeholder once BAA is executed
        return $this->callValantEndpoint('/availability', [
            'start' => $startDate,
            'end' => $endDate,
            'service_id' => $serviceTypeId,
        ]);
    }

    /**
     * Retrieve service offerings
     */
    public function getServiceTypes(): array
    {
        return [
            [
                'id' => 'adhd-eval',
                'name' => 'ADHD Comprehensive Evaluation',
                'category' => 'Diagnostic Assessment',
                'duration_minutes' => 90,
                'telehealth_available' => true,
                'in_person_available' => true,
                'description' => 'Clinical interviews, standardized rating scales, and cognitive assessment across all age groups.'
            ],
            [
                'id' => 'child-therapy',
                'name' => 'Child & Adolescent Psychotherapy',
                'category' => 'Pediatric Therapy',
                'duration_minutes' => 50,
                'telehealth_available' => true,
                'in_person_available' => true,
                'description' => 'Developmentally grounded care integrating parent collaboration and emotional regulation.'
            ],
            [
                'id' => 'adult-therapy',
                'name' => 'Adult Psychotherapy & Mood Support',
                'category' => 'Individual Therapy',
                'duration_minutes' => 50,
                'telehealth_available' => true,
                'in_person_available' => true,
                'description' => 'Evidence-based psychotherapy for anxiety, depression, life transitions, and nervous system steadiness.'
            ],
            [
                'id' => 'med-management',
                'name' => 'Psychiatric Evaluation & Medication Management',
                'category' => 'Psychiatry',
                'duration_minutes' => 60,
                'telehealth_available' => true,
                'in_person_available' => true,
                'description' => 'Conservative, collaborative psychiatric assessment and ongoing pharmacotherapy.'
            ],
            [
                'id' => 'trauma-care',
                'name' => 'Trauma-Informed Psychotherapy',
                'category' => 'Specialized Therapy',
                'duration_minutes' => 50,
                'telehealth_available' => true,
                'in_person_available' => true,
                'description' => 'Gentle, somatic and cognitive approaches to processing trauma and rebuilding internal safety.'
            ]
        ];
    }

    /**
     * Retrieve clinician specialties (Service-centric, not personal bios)
     */
    public function getClinicians(): array
    {
        return [
            [
                'id' => 'clinician-team-child',
                'department' => 'Child & Adolescent Behavioral Health',
                'modalities' => ['In-Person Indianapolis', 'Statewide Telehealth (Indiana)'],
                'service_types' => ['child-therapy', 'adhd-eval']
            ],
            [
                'id' => 'clinician-team-adult',
                'department' => 'Adult Psychotherapy & Clinical Care',
                'modalities' => ['In-Person Indianapolis', 'Statewide Telehealth (Indiana)'],
                'service_types' => ['adult-therapy', 'trauma-care', 'adhd-eval']
            ],
            [
                'id' => 'clinician-team-psychiatry',
                'department' => 'Psychiatric Services & Prescribing',
                'modalities' => ['In-Person Indianapolis', 'Statewide Telehealth (Indiana)'],
                'service_types' => ['med-management']
            ]
        ];
    }

    /**
     * Generate the direct, secure URL to Valant's hosted intake/booking workflow.
     * Always opened in a new tab to ensure HIPAA isolation.
     */
    public function getHostedBookingUrl(?string $serviceTypeId = null, ?string $preferredLocation = null): string
    {
        // For development/prototype, link to our branded preview simulation
        if ($this->isSandbox) {
            $query = http_build_query([
                'practice' => $this->practiceId,
                'service' => $serviceTypeId ?? 'all',
                'loc' => $preferredLocation ?? 'indianapolis',
                'mode' => 'sandbox_preview'
            ]);
            return 'valant-portal-preview.html?' . $query;
        }

        // Production Valant-hosted URL (configured with practice tenant ID)
        $query = http_build_query([
            'practiceId' => $this->practiceId,
            'serviceId' => $serviceTypeId,
            'location' => $preferredLocation,
            'ref' => 'stillmind_web'
        ]);

        return 'https://patientportal.valant.io/booking/' . $this->practiceId . '?' . $query;
    }

    /**
     * Sandbox mock generator for schedule availability
     */
    private function generateSandboxSlots(string $startDate, string $endDate, ?string $serviceTypeId = null): array
    {
        $slots = [];
        $services = ['adhd-eval', 'child-therapy', 'adult-therapy', 'med-management', 'trauma-care'];
        $chosenService = $serviceTypeId ?: $services[array_rand($services)];

        // Generate 6 realistic openings over upcoming business days
        $start = new \DateTime($startDate);
        for ($i = 1; $i <= 5; $i++) {
            $day = (clone $start)->modify("+$i weekdays");
            $dateStr = $day->format('Y-m-d');

            $slots[] = [
                'slot_id' => 'valant-slot-' . $dateStr . '-0930',
                'date' => $dateStr,
                'start_time' => '09:30 AM',
                'end_time' => '10:20 AM',
                'service_id' => $chosenService,
                'location_type' => 'in_person',
                'location_name' => 'Indianapolis Clinic (Keystone Crossing)'
            ];

            $slots[] = [
                'slot_id' => 'valant-slot-' . $dateStr . '-1400',
                'date' => $dateStr,
                'start_time' => '02:00 PM',
                'end_time' => '02:50 PM',
                'service_id' => $chosenService,
                'location_type' => 'telehealth',
                'location_name' => 'Secure Indiana Telehealth'
            ];
        }

        return [
            'practice_id' => $this->practiceId,
            'environment' => 'sandbox_mock',
            'hipaa_certified' => true,
            'query_range' => ['start' => $startDate, 'end' => $endDate],
            'slots_count' => count($slots),
            'available_slots' => $slots
        ];
    }

    /**
     * Stateless API Dispatcher for live Valant calls
     */
    private function callValantEndpoint(string $endpoint, array $params = []): array
    {
        // Guard against execution without confirmed BAA
        throw new \RuntimeException('Live Valant API calls blocked until signed BAA and client authorization are in place.');
    }
}
