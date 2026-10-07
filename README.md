# Stillmind Mental Health LLC

> *"Where the mind can settle."*

Production website and clinical portal for **Stillmind Mental Health LLC**, a HIPAA-regulated behavioral health practice based in Indianapolis, IN, serving children, adolescents, adults, and families via in-person clinic appointments at Keystone Crossing and statewide telehealth across Indiana.

---

## Architecture & Key Features

- **Modern Floating "Compact Card" Header**:
  - Island card container (`rounded-2xl`, off-white backdrop blur, soft drop shadow) built with Tailwind CSS.
  - Centered vertical icon-above-text links with subtle hover effects and active tab indicators.
  - Side-by-side pill CTAs: **Crisis 988** (with pulsing red indicator) and **Book Appointment** (dark forest green).
  - Accessible mobile drawer with keyboard focus trapping.
- **Dedicated Multi-Page Section Architecture**:
  - [`index.html`](index.html): Executive practice overview with 240-frame scroll-driven canvas stream sequencer.
  - [`services.html`](services.html): Comprehensive clinical services overview with links to deep-dive guides.
  - [`who-we-help.html`](who-we-help.html): Lifespan care demographics (Children, Teens, Adults, Seniors, Caregivers).
  - [`philosophy.html`](philosophy.html): 5-stage therapeutic arc (*Turbulence, Flow, Stillness, Reflection, Life*) & non-promissory ethics.
  - [`pricing.html`](pricing.html): In-network insurance plans, fee schedules, and Good Faith Estimate compliance.
  - [`locations.html`](locations.html): Keystone Crossing North Indianapolis clinic details, parking, and statewide virtual care.
  - [`contact.html`](contact.html): HIPAA-safe administrative inquiry form, clinic reception phone, and fax lines.
- **Deep-Dive Clinical Service Guides**:
  - [`services/adhd-evaluation.html`](services/adhd-evaluation.html): ADHD diagnostic testing & psychometric battery.
  - [`services/child-adolescent-therapy.html`](services/child-adolescent-therapy.html): Pediatric & adolescent psychotherapy.
  - [`services/adult-psychotherapy.html`](services/adult-psychotherapy.html): Adult CBT, ACT, and mood support.
  - [`services/medication-management.html`](services/medication-management.html): Conservative psychiatric medication management.
- **HIPAA-Compliant Stateless Architecture**:
  - Zero Protected Health Information (PHI) stored on marketing servers.
  - Direct appointment scheduling handoff into Valant EMR (`target="_blank"` with `rel="noopener noreferrer"`).
- **Integrated Emergency Access**:
  - Instant access to the `988 Suicide & Crisis Lifeline` embedded across every header and page footer.

---

## Technology Stack

- **Frontend**: Semantic HTML5, Vanilla CSS Design System with CSS variables and glassmorphic tokens, Tailwind CSS (non-preflight scoped).
- **Animation Engine**: HTML5 Canvas with `requestAnimationFrame` interpolation and dynamic scroll-progress tracking.
- **Accessibility & SEO**: WCAG 2.1 AA compliant, valid Schema.org MedicalClinic JSON-LD, XML sitemap.
- **Backend / Dev Server**: Zero-dependency native Node.js static server (`server.js`).

---

## Directory Structure

```text
├── api/
│   └── valant/
│       ├── ValantApiInterface.php       # PHP interface for scheduling contract
│       ├── ValantSchedulingClient.php   # Stateless adapter (Sandbox + Live Valant)
│       └── booking-proxy.php            # Public stateless JSON proxy endpoint
├── assets/
│   ├── css/
│   │   └── styles.css                   # Complete design system & responsive styling
│   ├── images/                          # Clinical photography, logos, favicons
│   └── js/
│       ├── hero-player.js               # Canvas scroll sequencer & frame engine
│       ├── main.js                      # Client navigation & HIPAA form validation
│       └── valant-client.js             # Valant EMR booking integration
├── ezgif-6c5fe2f9e9242931-jpg/          # 240 pre-rendered stream video frames
├── services/
│   ├── adhd-evaluation.html             # ADHD diagnostic guide
│   ├── adult-psychotherapy.html         # Adult therapy guide
│   ├── child-adolescent-therapy.html    # Pediatric therapy guide
│   └── medication-management.html       # Psychiatry & pharmacotherapy guide
├── contact.html                         # Dedicated practice inquiry page
├── index.html                           # Executive practice overview & portal
├── locations.html                       # Physical clinic & telehealth details
├── philosophy.html                      # 5-stage therapeutic arc & ethics
├── pricing.html                         # Rates, insurance & Good Faith Estimate
├── services.html                        # Services directory & overview
├── who-we-help.html                     # Lifespan demographics
├── sitemap.xml                          # Search engine index (11 URLs)
├── robots.txt                           # Search crawler directives
├── server.js                            # Native Node.js web server
└── README.md                            # Documentation
```

---

## Running Locally

To run the local development server:

```bash
node server.js
```

Then open `http://localhost:8080` in your browser.

To run on a custom port:
```bash
node server.js 3000
```

---

## License & Compliance

© Stillmind Mental Health LLC. All rights reserved.  
Compliant with HIPAA Administrative Simplification rules. All patient booking handoffs execute directly on Valant-hosted systems.
