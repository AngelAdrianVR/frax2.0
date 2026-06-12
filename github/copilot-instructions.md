FRAX — Project guidelines for Deepseek / Cursor / Copilot
Backend: Laravel 12, PHP 8.3+
Frontend: Vue 3 (Composition API + <script setup>), Inertia.js 2, Vite
UI library: PrimeVue (Aura Theme with Zinc Preset, registered in app.js)
Auth & permissions: Laravel Sanctum, Spatie Laravel Permission
Styling: Tailwind CSS (utility classes to complement PrimeVue). Adhere strictly to the "iOS Premium / Apple Style" design system.

General principles
- All code, comments, variable names, class names, methods, routes, and files must be written in English.
- All user-facing text (labels, placeholders, messages, tooltips, validation errors) must be in Spanish and use sentence case — never title case or all caps.
  ✅ "Guardar cambios", "Este campo es obligatorio", "Órdenes de servicio"
  ❌ "Guardar Cambios", "Este Campo Es Obligatorio", "ÓRDENES DE SERVICIO"
- Strip native margins from headers (use m-0 or mb-0 on h1, h2, etc.) to maintain a compact design.
- Follow SOLID principles strictly. Prefer explicit over implicit code — clarity over cleverness.
- No commented-out dead code — delete it immediately.

Database & Relations Rule (CRITICAL)
- NEVER use 'resident_id' or entities called 'Resident'. All residents have been unified into the 'users' table ('user_id').
- To connect a resident/user with a property ('private_units'), ALWAYS use the pivot table 'private_unit_user' or its Eloquent relationship.

UI/UX & Form Standards (iOS Premium Style)
- Form Design: Clean, elegant, minimalist, and highly visual. Input elements should have smooth rounded corners (rounded-xl or rounded-2xl). Main dashboard cards/widgets must use rounded-3xl.
- Shadows & Borders: Use ultra-soft shadows (e.g., shadow-[0_8px_30px_rgb(0,0,0,0.04)]) and subtle semi-transparent borders in dark mode (border-white/5).
- Colors: 
  - The official primary brand color for main buttons is `#0E63B1`.
  - Outer Views / Pages background: NEVER hardcode a background color (like bg-white or bg-zinc-900) in the main wrapper of separate views. The global system layout already provides the background.
  - Dark Mode: Full native support using the `.dark` selector. Use deep grays: `zinc-950` for deep backgrounds, `zinc-900` or `zinc-800` for cards and form containers.
- Form Components: 
  - The following components are GLOBALLY registered in app.js and DO NOT need local imports in Vue files: <Accordion>, <DatePicker>, <Button>, and <InputText>.
  - NEVER use native HTML <select> elements. They look generic and break dark mode. Always use customized dropdown/select components from PrimeVue.
- Performance & Interactivity: Use Inertia.js Partial Reloads (`preserveState: true`, `preserveScroll`, or `only: []`) for table filters, search bars, and tab switching to ensure a desktop-like native fluid experience.

Architecture & Domain Folders (PSR-4 compliant)
Follow the "Thin Controller, Robust Model, Dedicated Action" pattern. Organize code cleanly into FRAX domains (Community, Finances, Amenities, AccessControl, Security, Settings).

File & folder structure example:
app/
├── Actions/
│   ├── Community/
│   │   ├── CreateNoticeBoardPostAction.php
│   │   └── UpdateVehicleAction.php
│   └── Finances/
│       └── ConciliateBankMovementAction.php
├── Http/
│   ├── Controllers/
│   │   ├── Community/
│   │   │   ├── NoticeBoardController.php
│   │   │   └── PrivateUnitController.php
│   │   └── Finances/
│   │       └── BankReconciliationController.php
│   └── Requests/
│       └── Community/
│           └── StorePostRequest.php
├── Models/
│   ├── Community/
│   │   ├── PrivateUnit.php
│   │   ├── PrivateUnitContact.php
│   │   ├── Vehicle.php
│   │   └── Post.php
│   ├── Finances/
│   │   ├── Payment.php
│   │   └── Fee.php
│   └── Settings/
│       └── Subdivision.php

Controllers — keep them thin
- One responsibility per method: receive request, call action or service, return response.
- No business logic, no heavy database queries, no calculations inside controllers.
- Always use Form Requests for request payload validation.
- Always use Inertia responses for views, or JSON responses for API.

What to avoid
❌ Business logic inside controllers
❌ $request->validate() inside controllers — always use Form Requests
❌ $guarded = [] in models — always define $fillable
❌ Looking up 'resident_id' or treating residents as a separate table from users
❌ Native HTML selectors (<select>)
❌ Hardcoded background colors in frontend views (leave it to the layout)
❌ Title Case in user-facing text — always sentence case
❌ Checking roles directly — always check permissions (e.g., $user->can('permission'))