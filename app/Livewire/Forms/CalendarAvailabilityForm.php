 <?php

namespace App\Livewire\Forms;

use App\Models\CalendarAvailability;
use Livewire\Form;

class CalendarAvailabilityForm extends Form
{
    public ?CalendarAvailability $availability = null;
    public ?int $calendar_id = null;
    public string $date = '';
    public string $start_time = '';
    public string $end_time = '';
    public ?int $duration = null;
    public ?int $capacity = null;
    public array $packs = [];

    public function rules()
    {
        return [
            'calendar_id' => ['required', 'integer', 'exists:calendars,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer'],
            'capacity' => ['required', 'integer'],
        ];
    }

    public function set(CalendarAvailability $availability): void
    {
        $this->availability = $availability;

        $this->calendar_id = $availability->calendar_id;
        $this->date = $availability->date->format('Y-m-d');
        $this->start_time = $availability->start_time->format('H:i');
        $this->end_time = $availability->end_time->format('H:i');
        $this->duration = $availability->duration;
        $this->capacity = $availability->capacity;
        
        if ($availability->relationLoaded('packs')) {
            $packs = $availability->packs->map(fn($pack) => $pack->id)->filter()->toArray();
            $this->packs = $packs;
        }
    }

    public function update(): CalendarAvailability
    {
        $data = $this->validate();
        $this->availability->update($data);

        if (!is_null($this->packs)) {
            $this->availability->packs()->sync($this->packs);
        }

        $this->availability->refresh();

        return $this->availability;
    }

    public function delete(): bool
    {
        return $this->availability->delete();
    }
}
