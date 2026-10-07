/**
 * STILLMIND MENTAL HEALTH LLC — MAIN CLIENT SCRIPTS
 * 
 * Interactive Systems:
 * 1. Sticky Frosted Header Controller.
 * 2. Animated Mobile Drawer with Keyboard & Focus Management.
 * 3. Scroll Reveal Micro-Animations (IntersectionObserver).
 * 4. HIPAA-Safe Accessible Contact Form Engine (Inline Live Validation).
 * 5. Safe Smooth Anchor Scrolling.
 */

(function () {
  'use strict';

  // 1. Sticky Header Scroll Treatment
  const siteHeader = document.querySelector('.site-header');
  if (siteHeader) {
    const onScroll = () => {
      const scrollY = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollY > 15) {
        siteHeader.classList.add('scrolled');
      } else {
        siteHeader.classList.remove('scrolled');
      }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // 2. Animated Mobile Navigation Toggle with Full Keyboard & Focus Trap
  const menuToggle = document.querySelector('.menu-toggle-btn');
  const mobileMenu = document.querySelector('.mobile-menu');

  if (menuToggle && mobileMenu) {
    function toggleMobileMenu(forceState) {
      const isCurrentlyOpen = mobileMenu.classList.contains('is-open');
      const shouldOpen = forceState !== undefined ? forceState : !isCurrentlyOpen;

      mobileMenu.classList.toggle('is-open', shouldOpen);
      menuToggle.classList.toggle('is-active', shouldOpen);
      menuToggle.setAttribute('aria-expanded', shouldOpen.toString());
      menuToggle.setAttribute('aria-label', shouldOpen ? 'Close navigation menu' : 'Open navigation menu');

      if (shouldOpen) {
        const firstLink = mobileMenu.querySelector('a, button');
        if (firstLink) firstLink.focus();
      }
    }

    menuToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleMobileMenu();
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
        toggleMobileMenu(false);
        menuToggle.focus();
      }
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      if (mobileMenu.classList.contains('is-open') && !mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
        toggleMobileMenu(false);
      }
    });

    // Close when clicking any nav link
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        toggleMobileMenu(false);
      });
    });
  }

  // 3. Dynamic Bidirectional Scroll Fade (Appear & Disappear as user scrolls past)
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  
  const itemAndCardSelectors = [
    '.reveal-on-scroll',
    '.service-card',
    '.population-card',
    '.principle-item',
    '.insurance-pill',
    '.fee-item',
    '.location-info-card',
    '.telehealth-box',
    '.faq-item',
    '.step-card',
    '.sidebar-box',
    '.cta-banner-inline',
    '.help-image-card',
    '.contact-form-wrapper',
    '.hero-badge-item'
  ].join(', ');

  const scrollElements = Array.from(document.querySelectorAll(itemAndCardSelectors));

  if (scrollElements.length > 0) {
    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
      scrollElements.forEach(el => el.classList.add('is-revealed'));
    } else {
      scrollElements.forEach(el => {
        el.classList.add('reveal-on-scroll');
      });

      const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
          } else {
            // Disappear when scrolled past or outside visible viewport
            entry.target.classList.remove('is-revealed');
          }
        });
      }, {
        threshold: 0.1,
        rootMargin: '20px 0px -20px 0px'
      });

      scrollElements.forEach(el => revealObserver.observe(el));
    }
  }

  // 4. HIPAA-Safe Accessible Contact Form Engine
  const contactForm = document.getElementById('clinicalContactForm');
  const formSuccess = document.getElementById('contactFormSuccess');

  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const nameInput = document.getElementById('contactName');
      const emailInput = document.getElementById('contactEmail');
      const messageInput = document.getElementById('contactMessage');
      const ackCheckbox = document.getElementById('hipaaAck');

      // Clear previous error states
      [nameInput, emailInput, messageInput, ackCheckbox].forEach(el => {
        if (el) el.removeAttribute('aria-invalid');
      });
      document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

      let hasErrors = false;
      let firstInvalid = null;

      // Validate Name
      if (!nameInput.value.trim()) {
        nameInput.setAttribute('aria-invalid', 'true');
        const nameError = document.getElementById('nameError');
        if (nameError) nameError.textContent = 'Please provide your full name.';
        hasErrors = true;
        firstInvalid = firstInvalid || nameInput;
      }

      // Validate Email
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailInput.value.trim() || !emailPattern.test(emailInput.value.trim())) {
        emailInput.setAttribute('aria-invalid', 'true');
        const emailError = document.getElementById('emailError');
        if (emailError) emailError.textContent = 'Please enter a valid email address.';
        hasErrors = true;
        firstInvalid = firstInvalid || emailInput;
      }

      // Validate Message
      if (messageInput && !messageInput.value.trim()) {
        messageInput.setAttribute('aria-invalid', 'true');
        const messageError = document.getElementById('messageError');
        if (messageError) messageError.textContent = 'Please enter your administrative question or inquiry.';
        hasErrors = true;
        firstInvalid = firstInvalid || messageInput;
      }

      // Validate HIPAA Acknowledgment
      if (ackCheckbox && !ackCheckbox.checked) {
        ackCheckbox.setAttribute('aria-invalid', 'true');
        const ackError = document.getElementById('ackError');
        if (ackError) ackError.textContent = 'Please acknowledge this notice before transmitting.';
        hasErrors = true;
        firstInvalid = firstInvalid || ackCheckbox;
      }

      if (hasErrors) {
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      // Dispatch simulation with accessible state feedback
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Transmitting securely...';

      const directEmailLink = document.getElementById('contactDirectEmailLink');
      if (directEmailLink && nameInput) {
        const sub = encodeURIComponent('Stillmind Inquiry - ' + nameInput.value.trim());
        const body = encodeURIComponent(
          'From: ' + nameInput.value.trim() + '\nEmail: ' + emailInput.value.trim() + '\n\nMessage:\n' + (messageInput ? messageInput.value.trim() : '')
        );
        directEmailLink.href = 'mailto:intake@stillmindmentalhealth.com?subject=' + sub + '&body=' + body;
      }

      setTimeout(() => {
        contactForm.reset();
        contactForm.style.display = 'none';
        if (formSuccess) {
          formSuccess.style.display = 'block';
          formSuccess.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          formSuccess.focus();
        }
      }, 650);
    });
  }

  // 5. Safe Smooth Anchor Scrolling
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '') return;

      try {
        const targetId = href.startsWith('#') ? href.slice(1) : href;
        const target = document.getElementById(targetId);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth' });
        }
      } catch (_) {
        // Ignore unhandled selector syntax error gracefully
      }
    });
  });

})();
