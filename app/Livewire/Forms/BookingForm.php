<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Booking;
use Illuminate\Support\Str;
use App\Enums\BookingStatus;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;

class BookingForm extends Form
{
    public ?Booking $booking = null;
    public ?int $calendar_id = null;
    public ?int $calendar_availability_id = null;
    public $client_id = null;
    public ?int $service_id = null;
    public ?int $service_pack_id = null;
    public BookingStatus $status = BookingStatus::PENDING;
    public ?string $code = null;
    public ?string $name = null;
    public ?string $notes = null;
    public bool $allow_share = false;
    public ?int $source_id = null;
    public bool $manual = false;

    public function rules()
    {
        return [
            'calendar_id' => ['required', 'integer', 'exists:App\Models\Calendar,id'],
            'calendar_availability_id' => ['sometimes', 'nullable', 'integer', 'exists:App\Models\CalendarAvailability,id'],
            'client_id' => ['required', 'integer', 'exists:App\Models\Client,id'],
            'service_pack_id' => ['required', 'integer', 'exists:App\Models\ServicePack,id'],
            'status' => ['required', Rule::enum(BookingStatus::class)],
            'name' => ['sometimes', 'nullable', 'string'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'allow_share' => ['required', 'boolean'],
            'source_id' => ['sometimes', 'nullable', 'integer', 'exists:App\Models\Source,id'],
            'manual' => ['required', 'boolean'],
        ];
    }

    public function set(Booking $booking): void
    {
        $this->booking = $booking;

        $this->calendar_id = $booking->calendar_id;
        $this->calendar_availability_id = $booking->calendar_availability_id;
        $this->client_id = $booking->client_id;
        $this->service_pack_id = $booking->service_pack_id;
        $this->status = $booking->status;
        $this->code = $booking->code;
        $this->name = $booking->name;
        $this->notes = $booking->notes;
        $this->allow_share = $booking->allow_share;
        $this->source_id = $booking->source_id;
        $this->manual = $booking->manual;

        $this->service_id = $booking->pack?->service_id;
    }

    public function store(): Booking
    {
        $data = $this->validate();

        $code = strtoupper(Str::random(4));
        while (Booking::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(4));
        }

        $data['code'] = $code;
        $booking = Booking::create($data);
        $this->reset();

        return $booking;
    }

    public function update()
    {
        $data = $this->validate();

        $this->booking->update($data);
        $this->booking->refresh();

        return $this->booking;
    }

    public function delete(): bool
    {
        $this->booking->payment()->delete();
        $this->booking->answers()->delete();
        $this->booking->assistants()->delete();

        return $this->booking->delete();
    }
}
