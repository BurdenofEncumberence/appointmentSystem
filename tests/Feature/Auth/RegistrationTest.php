<?php

use App\Mail\RegistrationOtpMail;
use Illuminate\Support\Facades\Mail;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('submitting registration dispatches OTP email and redirects to verification page without creating user yet', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'first_name' => 'John',
        'middle_name' => 'Fitzgerald',
        'last_name' => 'Kennedy',
        'email' => 'john.kennedy@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Should redirect to OTP screen, and user is NOT authenticated yet
    $response->assertRedirect(route('register.otp.show'));
    $this->assertGuest();

    // Database should NOT have the user yet
    $this->assertDatabaseMissing('users', [
        'email' => 'john.kennedy@example.com',
    ]);

    // OTP mail should have been sent
    Mail::assertSent(RegistrationOtpMail::class, function ($mail) {
        return $mail->hasTo('john.kennedy@example.com');
    });

    // Session has pending registration
    $this->assertTrue(session()->has('pending_registration'));
});

test('otp verification screen can be rendered when registration is pending', function () {
    Mail::fake();

    $this->post('/register', [
        'first_name' => 'John',
        'middle_name' => 'Fitzgerald',
        'last_name' => 'Kennedy',
        'email' => 'john.kennedy@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response = $this->get(route('register.otp.show'));

    $response->assertOk();
    $response->assertSee('Verify your email');
});

test('entering valid otp creates user, marks email verified, logs in, and redirects to booking', function () {
    Mail::fake();

    $this->post('/register', [
        'first_name' => 'John',
        'middle_name' => 'Fitzgerald',
        'last_name' => 'Kennedy',
        'email' => 'john.kennedy@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $pending = session('pending_registration');
    $otp = $pending['otp_plain_dev'];
    $this->assertNotNull($otp);

    $verifyResponse = $this->post(route('register.otp.verify'), [
        'otp' => $otp,
    ]);

    $verifyResponse->assertRedirect(route('booking'));
    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'first_name' => 'John',
        'middle_name' => 'Fitzgerald',
        'last_name' => 'Kennedy',
        'name' => 'John Fitzgerald Kennedy',
        'email' => 'john.kennedy@example.com',
        'role' => 'player',
    ]);

    $user = \App\Models\User::where('email', 'john.kennedy@example.com')->first();
    $this->assertNotNull($user->email_verified_at);
    $this->assertFalse(session()->has('pending_registration'));
});

test('entering invalid otp is rejected with errors and does not create user', function () {
    Mail::fake();

    $this->post('/register', [
        'first_name' => 'Jane',
        'middle_name' => '',
        'last_name' => 'Doe',
        'email' => 'jane.doe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $verifyResponse = $this->from(route('register.otp.show'))->post(route('register.otp.verify'), [
        'otp' => '000000',
    ]);

    $verifyResponse->assertRedirect(route('register.otp.show'));
    $verifyResponse->assertSessionHasErrors(['otp']);
    $this->assertGuest();

    $this->assertDatabaseMissing('users', [
        'email' => 'jane.doe@example.com',
    ]);
});

test('users can request resend of otp code after cooldown', function () {
    Mail::fake();

    $this->post('/register', [
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'email' => 'alice@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Immediate resend should be blocked by cooldown
    $blockedResend = $this->post(route('register.otp.resend'));
    $blockedResend->assertSessionHasErrors(['otp']);

    // Advance time past cooldown
    $pending = session('pending_registration');
    $pending['resend_available_at'] = now()->subSeconds(5)->timestamp;
    session(['pending_registration' => $pending]);

    $resendResponse = $this->post(route('register.otp.resend'));
    $resendResponse->assertSessionHas('status');

    Mail::assertSent(RegistrationOtpMail::class, 2);
});

test('user can cancel registration and return to register form with prefilled inputs', function () {
    Mail::fake();

    $this->post('/register', [
        'first_name' => 'Bob',
        'last_name' => 'Marley',
        'email' => 'bob@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response = $this->get(route('register.otp.cancel'));

    $response->assertRedirect(route('register'));
    $this->assertFalse(session()->has('pending_registration'));
});

test('submitting registration with lowercase names automatically capitalizes first letter of names', function () {
    Mail::fake();

    $this->post('/register', [
        'first_name' => 'geoff patrick',
        'middle_name' => 'dela cruz',
        'last_name' => 'granada',
        'email' => 'lowercase.user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $pending = session('pending_registration');
    $this->assertNotNull($pending);
    $this->assertSame('Geoff Patrick', $pending['first_name']);
    $this->assertSame('Dela Cruz', $pending['middle_name']);
    $this->assertSame('Granada', $pending['last_name']);
    $this->assertSame('Geoff Patrick Dela Cruz Granada', $pending['name']);

    $otp = $pending['otp_plain_dev'];
    $verifyResponse = $this->post(route('register.otp.verify'), [
        'otp' => $otp,
    ]);

    $verifyResponse->assertRedirect(route('booking'));

    $this->assertDatabaseHas('users', [
        'first_name' => 'Geoff Patrick',
        'middle_name' => 'Dela Cruz',
        'last_name' => 'Granada',
        'name' => 'Geoff Patrick Dela Cruz Granada',
        'email' => 'lowercase.user@example.com',
    ]);
});
