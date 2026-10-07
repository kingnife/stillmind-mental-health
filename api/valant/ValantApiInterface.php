<?php
/**
 * STILLMIND MENTAL HEALTH LLC
 * Valant Scheduling API Interface
 *
 * Strict HIPAA Compliance Guardrails:
 * - Stateless proxy only; our servers are NEVER a system of record for PHI.
 * - No patient identity, appointment notes, or PHI persisted in database, logs, or error trackers.
 */

namespace Stillmind\Valant;

interface ValantApiInterface
{
    /**
     * Retrieve available appointment slots for a given date range and optional service.
     *
     * @param string $startDate ISO 8601 (YYYY-MM-DD)
     * @param string $endDate   ISO 8601 (YYYY-MM-DD)
     * @param string|null $serviceTypeId Optional service filter (e.g., 'adhd-eval', 'psychotherapy')
     * @return array List of available time slots with provider IDs and timestamps.
     */
    public function getAvailableSlots(string $startDate, string $endDate, ?string $serviceTypeId = null): array;

    /**
     * Retrieve clinical services configured in Valant.
     *
     * @return array List of behavioral health service types and appointment durations.
     */
    public function getServiceTypes(): array;

    /**
     * Retrieve clinician specialties and appointment formats (in-person vs telehealth).
     *
     * @return array Clinician profiles (service-focused, non-promissory).
     */
    public function getClinicians(): array;

    /**
     * Generate a secure, pre-configured Valant-hosted intake handoff URL.
     * Users complete their HIPAA-compliant booking directly on Valant's hosted infrastructure in a new tab.
     *
     * @param string|null $serviceTypeId
     * @param string|null $preferredLocation 'indianapolis' | 'telehealth'
     * @return string Valid HTTPS URL to the Valant hosted intake flow.
     */
    public function getHostedBookingUrl(?string $serviceTypeId = null, ?string $preferredLocation = null): string;
}
