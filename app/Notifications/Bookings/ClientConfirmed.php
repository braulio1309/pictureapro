<?php

namespace App\Notifications\Bookings;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ClientConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Booking $booking,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->booking->load(['calendar', 'calendar.tenant', 'availability', 'pack', 'pack.service', 'payment', 'payment.method']);

        $date = !is_null($this->booking->calendar_availability_id)
            ? $this->booking->availability->start_full
            : null;

        $data = [
            'photographer_name' => $this->booking->calendar->tenant->fullname,
            'service_name' => $this->booking->pack->service->name . ' - ' . $this->booking->pack->name,
            'date' => $date,
            'price' => $this->booking->payment->amount,
            'payment_method' => $this->booking->payment->method->name,
        ];

        return (new MailMessage)
            ->subject('Su reserva ha sido confirmada')
            ->markdown('mail.bookings.client-confirmed', ['data' => $data]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
