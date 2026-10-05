<x-guest-layout title="Privacy Policy — KYMNET" label="Privacy Policy" card-class="max-w-[760px]">
    <div class="mb-6">
        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 mb-3">
            ← Back to Registration
        </a>
        <h1 class="gz-font-display font-bold text-2xl md:text-3xl text-stone-900 mb-1">Privacy Policy</h1>
        <p class="gz-hint text-sm">Last updated: {{ date('F Y') }} · Data Privacy Act of 2012 Compliance</p>
    </div>

    <div class="space-y-6 text-sm text-stone-700 leading-relaxed max-h-[500px] overflow-y-auto pr-2 border-y border-stone-200 py-4">
        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">1. Overview</h2>
            <p>
                KYMNET ("we", "us", "our") respects your personal data and is committed to protecting your privacy in compliance with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012 of the Philippines.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">2. Information We Collect</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li><strong>Account Information:</strong> First name, middle name, last name, email address, contact numbers, and secure hashed passwords.</li>
                <li><strong>Reservation & Transaction Data:</strong> Booked court dates, schedules, payment methods, transaction reference codes, and attendance history.</li>
                <li><strong>Technical Data:</strong> IP addresses, browser types, session records, and device security cookies necessary for user authentication.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">3. Purpose of Processing</h2>
            <p>We process your personal information for the following legitimate purposes:</p>
            <ul class="list-disc pl-5 space-y-1 mt-1">
                <li>To register, verify via One-Time Password (OTP), and authenticate your user account.</li>
                <li>To manage and schedule court reservations and issue automated email receipts.</li>
                <li>To verify front-desk check-in, attendance, and resolve scheduling conflicts.</li>
                <li>To prevent fraud, double-booking abuse, and ensure facility security.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">4. Data Storage & Security Measures</h2>
            <p>
                We implement robust technical and organizational security controls, including encrypted communications (HTTPS), bcrypt password hashing, and role-based access restrictions. Your personal information is retained only as long as necessary to fulfill court reservation services or as required by law.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">5. Third-Party Disclosures</h2>
            <p>
                KYMNET does not sell, rent, or trade your personal data to third parties. Information may only be shared with payment processors (e.g., GCash, Maya) or official law enforcement authorities when strictly required by Philippine legal processes.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">6. Your Rights as a Data Subject</h2>
            <p>
                Under the Data Privacy Act, you have the right to be informed, access your data, rectify inaccurate details, or request erasure of your account directly through your Profile settings or by contacting our data protection officer.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">7. Contact Desk</h2>
            <p>
                For questions regarding this Privacy Policy or your personal information, please reach out to our team at Davao City, Philippines or email <span class="font-semibold text-emerald-600">support@kymnet.ph</span>.
            </p>
        </section>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <a href="{{ route('terms') }}" class="gz-link text-sm">Read Terms & Conditions →</a>
        <a href="{{ route('register') }}" class="gz-btn-primary">Return to Register</a>
    </div>
</x-guest-layout>
