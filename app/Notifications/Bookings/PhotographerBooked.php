<?php

namespace App\Notifications\Bookings;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PhotographerBooked extends Notification
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
        $this->booking->load(['calendar', 'availability', 'client', 'pack', 'pack.service', 'payment', 'payment.method']);

        $date = !is_null($this->booking->calendar_availability_id)
            ? $this->booking->availability->start_full
            : null;

        $data = [
            'calendar_name' => $this->booking->calendar->name,
            'client_name' => $this->booking->client->fullname,
            'service_name' => $this->booking->pack->service->name . ' - ' . $this->booking->pack->name,
            'date' => $date,
            'price' => $this->booking->payment->amount,
            'payment_method' => $this->booking->payment->method->name,
            'url' => route('dashboard.calendars.show', ['id' => $this->booking->calendar_id]),
        ];

        return (new MailMessage)
            ->subject('Nueva reserva')
            ->markdown('mail.bookings.photographer-booked', ['data' => $data]);
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
