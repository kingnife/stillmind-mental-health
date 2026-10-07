/**
 * STILLMIND MENTAL HEALTH LLC — VALANT CLIENT ADAPTER (FRONT-END)
 * 
 * Strict HIPAA Architecture:
 * - Directs the patient to the secure, Valant-hosted booking flow in a new tab.
 * - Zero patient data is transmitted to or collected by Stillmind's web servers.
 */

(function () {
  'use strict';

  const ValantConfig = {
    // Set to true when deploying live to connect to Valant production portal
    isProduction: false,
    livePortalUrl: 'https://valant.io/portal/stillmind-in',
    sandboxUrl: 'valant-portal-preview.html'
  };

  const ValantClient = {
    config: ValantConfig,

    /**
     * Resolve the appropriate target URL for booking
     * @param {string|null} serviceId
     * @param {string|null} location
     * @returns {string}
     */
    getBookingUrl(serviceId, location) {
      const params = new URLSearchParams();
      if (serviceId) params.set('service', serviceId);
      if (location) params.set('loc', location);
      params.set('ref', 'stillmind_marketing');

      if (ValantConfig.isProduction) {
        const query = params.toString() ? `?${params.toString()}` : '';
        return `${ValantConfig.livePortalUrl}${query}`;
      }

      // Adjust relative path based on whether we are in a subfolder (e.g. /services/)
      const isInSubfolder = window.location.pathname.includes('/services/');
      const basePath = isInSubfolder ? '../' : '';

      return `${basePath}${ValantConfig.sandboxUrl}?${params.toString()}`;
    },

    /**
     * Launch the Valant-hosted scheduling flow in a new browser tab
     * @param {string|null} serviceId
     * @param {string|null} location
     */
    openBookingFlow(serviceId, location) {
      const url = this.getBookingUrl(serviceId, location);
      window.open(url, '_blank', 'noopener,noreferrer');
    },

    /**
     * Initialize automatic bindings to any button or link with [data-valant-booking]
     */
    init() {
      // Sync hrefs on all anchor tags immediately to prevent popup blocker interception
      document.querySelectorAll('a[data-valant-booking]').forEach(anchor => {
        const serviceId = anchor.getAttribute('data-valant-service') || null;
        const location = anchor.getAttribute('data-valant-location') || null;
        anchor.href = ValantClient.getBookingUrl(serviceId, location);
        anchor.target = '_blank';
        anchor.rel = 'noopener noreferrer';
      });

      // Delegate click for dynamically inserted buttons
      document.addEventListener('click', (e) => {
        const trigger = e.target.closest('button[data-valant-booking]');
        if (!trigger) return;

        e.preventDefault();
        const serviceId = trigger.getAttribute('data-valant-service') || null;
        const location = trigger.getAttribute('data-valant-location') || 'indianapolis';
        ValantClient.openBookingFlow(serviceId, location);
      });
    }
  };

  // Expose globally and auto-init on DOM ready
  window.ValantClient = ValantClient;
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => ValantClient.init());
  } else {
    ValantClient.init();
  }
})();
