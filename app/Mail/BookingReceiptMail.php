<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public float $subtotal = 0.0;
    public float $totalAmount = 0.0;
    public float $discountAmount = 0.0;
    public ?SiteSettings $siteSettings = null;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param array|\Illuminate\Support\Collection $bookings
     * @param string $refNum
     * @param string $paymentMethod
     * @param float $discountPercent
     * @param Event|null $event
     */
    public function __construct(
        public User $user,
        public $bookings,
        public string $refNum,
        public string $paymentMethod = 'online',
        public float $discountPercent = 0.0,
        public ?Event $event = null
    ) {
        $this->siteSettings = SiteSettings::first();

        // Calculate financials
        foreach ($this->bookings as $booking) {
            $court = $booking->court;
            $start = \Carbon\Carbon::parse($booking->start_time);
            $end = \Carbon\Carbon::parse($booking->end_time);
            $hours = max(1, $start->diffInMinutes($end) / 60);
            $slotPrice = $court ? ($court->price_per_hour * $hours) : 0;
            $this->subtotal += $slotPrice;
        }

        if ($this->discountPercent > 0) {
            $this->discountAmount = round($this->subtotal * ($this->discountPercent / 100), 2);
            $this->totalAmount = max(0, $this->subtotal - $this->discountAmount);
        } else {
            $this->totalAmount = $this->subtotal;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $systemName = $this->siteSettings?->system_name ?: 'KYMNET';

        return new Envelope(
            subject: "[{$systemName}] Booking Confirmation & Official Receipt: {$this->refNum}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-receipt',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
