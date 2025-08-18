<?php

namespace App\Livewire\Components\Dashboard\Calendars;

use Carbon\Carbon;
use Mary\Traits\Toast;
use App\Models\Service;
use Livewire\Component;
use App\Models\Calendar;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\CalendarForm;
use App\Models\CalendarAvailability;
use App\Models\CalendarQuestion;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class Drawer extends Component
{
    use WithFileUploads;
    use Toast;

    public CalendarForm $form;
    public bool $open = false;
    public string $action = 'create';
    public string $tab_selected = 'general';
    public mixed $cover = null;
    public string $cover_key = '';
    public string $message = '';
    public array $questions = [];
    public string $questions_key = '';

    public array $select_days = [
        ['id' => Carbon::MONDAY, 'name' => 'Lunes'],
        ['id' => Carbon::TUESDAY, 'name' => 'Martes'],
        ['id' => Carbon::WEDNESDAY, 'name' => 'Miércoles'],
        ['id' => Carbon::THURSDAY, 'name' => 'Jueves'],
        ['id' => Carbon::FRIDAY, 'name' => 'Viernes'],
        ['id' => Carbon::SATURDAY, 'name' => 'Sábado'],
        ['id' => Carbon::SUNDAY, 'name' => 'Domingo'],
    ];

    public ?string $single_date = null;
    public ?string $single_time = null;
    public int $single_capacity = 1;

    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $start_time = null;
    public ?string $end_time = null;
    public ?string $second_start_time = null;
    public ?string $second_end_time = null;
    public int $duration = 30;
    public int $capacity = 1;
    public array $days = [];

    public array $tinyMCE_settings = [
        'menubar' => false,
        'statusbar' => false,
        'plugins' => 'lists link',
        'toolbar' => 'undo redo | bold italic underline | bullist numlist | link',
        'license_key' => 'gpl',
    ];

    public function resetDrawer()
    {
        $this->tab_selected = 'general';
        $this->cover = null;
        $this->cover_key = 'drawer-calendar-cover-' . uniqid();
        $this->message = '';
        $this->questions = [];

        $this->single_date = null;
        $this->single_time = null;
        $this->single_capacity = 1;
    
        $this->start_date = null;
        $this->end_date = null;
        $this->start_time = null;
        $this->end_time = null;
        $this->second_start_time = null;
        $this->second_end_time = null;
        $this->duration = 30;
        $this->capacity = 1;
        $this->days = [];

        $this->form->reset();
    }

    #[On('calendars:open-drawer')]
    public function open(string $action = 'create', ?int $id = null): void
    {
        $this->action = $action;
        $this->resetDrawer();

        if ($this->action === 'edit') {
            $calendar = Calendar::query()
                ->with(['media', 'services', 'questions', 'questions.services'])
                ->findOrFail($id);

                
            $this->form->set($calendar);
            $this->questions = $this->getCalendarQuestions();
        }

        $this->open = true;
    }

    public function getCalendarQuestions()
    {
        $questions = $this->form->calendar->questions
            ->sortBy('position')
            ->map(function ($question) {
                $services = $question->services->pluck('id')->toArray();

                return [
                    'calendar_question_id' => $question->id,
                    'position' => $question->position,
                    'question' => $question->question,
                    'services' => $services,
                    'is_active' => $question->is_active,
                    'is_required' => $question->is_required,
                ];
            })
            ->values()
            ->toArray();

        $this->questions_key = uniqid('questions-key-');

        return $questions;
    }

    public function submit()
    {
        if ($this->action === 'create') {
            $calendar = $this->form->store();

            if (!is_null($this->cover)) {
                $calendar->addMedia($this->cover)->toMediaCollection('cover');
            }

            $this->dispatch('calendars:updated', $calendar->id);
            $this->success('Calendario creado correctamente', css: 'bg-primary text-white');
        }

        if ($this->action === 'edit') {
            $calendar = $this->form->update();

            if (!is_null($this->cover)) {
                $calendar->clearMediaCollection('cover');
                $calendar->addMedia($this->cover)->toMediaCollection('cover');
            }

            // si tiene preguntas, crearlas o actualizarlas
            if (!empty($this->questions)) {
                foreach ($this->questions as $question) {
                    $updated_question = $calendar->questions()->find($question['calendar_question_id']);
                    $updated_question->update($question);
                    $updated_question->services()->sync($question['services']);
                }
            }

            $this->dispatch('calendars:updated');
            $this->success('Calendario actualizado correctamente', css: 'bg-primary text-white');
        }

        $this->open = false;
        $this->resetDrawer();
        $this->dispatch('calendars:availability-updated');
    }

    public function delete()
    {
        $id = $this->form->calendar->id;

        if ($this->action !== 'edit') {
            return;
        }

        $this->form->delete();

        $this->open = false;
        $this->resetDrawer();

        $this->dispatch('calendars:deleted', id: $id);
    }

    public function saveAvailability()
    {
        $this->validate([
            'single_date' => ['required', 'date:Y-m-d'],
            'single_time' => ['required', 'string'],
            'single_capacity' => ['required', 'integer', 'min:1'],
        ], [
            'single_date.required' => 'La fecha es obligatoria.',
            'single_time.required' => 'La hora es obligatoria.',
            'single_capacity.required' => 'La capacidad es obligatoria.',
            'single_capacity.min' => 'La capacidad debe ser al menos 1.',
        ]);
        $availability = CalendarAvailability::create([
            'calendar_id' => $this->form->calendar->id,
            'date' => $this->single_date,
            'start_time' => new Carbon($this->single_time),
            'end_time' => (new Carbon($this->single_time))->addMinutes($this->duration),
            'duration' => $this->duration,
            'capacity' => $this->single_capacity,
        ]);
        if (Auth::user()->google_refresh_token) {
           
                $service = new GoogleCalendarService(Auth::user());
                
                $eventData = [
                    'summary' => 'Cita: ' . $this->form->calendar->name,
                    'description' => $this->form->calendar->description ?? 'Cita creada desde el sistema',
                    'start' => [
                        'dateTime' => Carbon::parse($this->single_date . ' ' . $this->single_time)->toIso8601String(),
                        'timeZone' => config('app.timezone'),
                    ],
                    'end' => [
                        'dateTime' => Carbon::parse($this->single_date . ' ' . $this->single_time)
                            ->addMinutes($this->duration)
                            ->toIso8601String(),
                        'timeZone' => config('app.timezone'),
                    ],
                    'colorId' => $this->form->color ?? null,
                ];

                $result = $service->createEvent($eventData);
                
                if (!$result['success']) {
                    Log::error('Google Calendar sync failed: ' . ($result['error'] ?? 'Unknown error'));
                }
            
        }


        $this->dispatch('calendars:availability-updated');
        $this->open = false;
        $this->resetDrawer();
        $this->success('La cita ha sido creada correctamente.', css: 'bg-primary text-white');
    }

    public function saveAvailabilities(bool $confirmation = false)
    {
        $this->validate([
            'start_date' => ['required', 'date:Y-m-d'],
            'end_date' => ['required', 'date:Y-m-d'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'duration' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'days' => ['required', 'array'],
            'days.*' => ['integer'],
            'second_start_time' => ['nullable', 'string', 'required_with:second_end_time'],
            'second_end_time' => ['nullable', 'string', 'required_with:second_start_time'],
        ], [
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'duration.required' => 'La duración es obligatoria.',
            'duration.min' => 'La duración debe ser al menos 1.',
            'capacity.required' => 'La capacidad es obligatoria.',
            'capacity.min' => 'La capacidad debe ser al menos 1.',
            'days.required' => 'Los días son obligatorios.',
            'days.*' => 'Los días son obligatorios.',
            'second_start_time.required_with' => 'La segunda hora de inicio es obligatoria.',
            'second_end_time.required_with' => 'La segunda hora de fin es obligatoria.',
        ]);

        $data = [
            'start_date' => new Carbon($this->start_date),
            'end_date' => new Carbon($this->end_date),
            'timeslots' => [
                ['start_time' => $this->start_time, 'end_time' => $this->end_time]
            ],
            'duration' => $this->duration,
            'days' => $this->days,
            'capacity' => $this->capacity,
        ];
        if (!empty($this->second_start_time) && !empty($this->second_end_time)) {
            $data['timeslots'][] = [
                'start_time' => $this->second_start_time,
                'end_time' => $this->second_end_time,
            ];
        }

        // 1. make date period between start and end date
        $period = $data['start_date']->toPeriod($data['end_date']);
        
        // 2. iterate over each day and filter by selected days
        $days = $period->toArray();
        $days = array_filter($days, function ($day) use ($data) {
            return in_array($day->dayOfWeek, $data['days']);
        });
        $days = array_map(function ($day) use ($data) {
            $slots = [];

            foreach($data['timeslots'] as $timeslot) {
                // subdivide by hours
                $start = new Carbon($day->format('Y-m-d') . ' ' . $timeslot['start_time']);
                $end = new Carbon($day->format('Y-m-d') . ' ' . $timeslot['end_time']);
                $interval_minutes = $data['duration'];
                

                while ($start->lt($end)) {
                    $slots[] = [
                        'date' => $day->format('Y-m-d'),
                        'start_time' => $start->format('H:i'),
                        'end_time' => $start->copy()->addMinutes($interval_minutes)->format('H:i'),
                        'duration' => $data['duration'],
                        'capacity' => $data['capacity'],
                    ];

                    $start->addMinutes($interval_minutes);
                }
            }

            return $slots;
        }, $days);
        $days = array_merge(...$days);

        if (!$confirmation) {
            $info = $this->getEventSlots($days);
            $this->message = 'Se van a crear un total de '.$info['total'].' sesiones en '.$info['days'].' días.';
            return;
        }

        foreach ($days as $day) {
            CalendarAvailability::create([
                'calendar_id' => $this->form->calendar->id,
                'date' => $day['date'],
                'start_time' => $day['start_time'],
                'end_time' => $day['end_time'],
                'duration' => $day['duration'],
                'capacity' => $day['capacity'],
            ]);
            if (Auth::user()->google_refresh_token) {
                try {
                    $service = new GoogleCalendarService(Auth::user());
                    
                    $eventData = [
                        'summary' => 'Cita: ' . $this->form->calendar->name,
                        'description' => $this->form->calendar->description ?? 'Cita creada desde el sistema',
                        'start' => [
                            'dateTime' => Carbon::parse($day['date'] . ' ' . $day['start_time'])->toIso8601String(),
                            'timeZone' => config('app.timezone'),
                        ],
                        'end' => [
                            'dateTime' => Carbon::parse($day['date'] . ' ' . $day['end_time'])->toIso8601String(),
                            'timeZone' => config('app.timezone'),
                        ],
                    ];

                    $service->createEvent($eventData);
                } catch (\Exception $e) {
                    dd($e);
                    Log::error('Batch Google Calendar sync error: ' . $e->getMessage());
                }
            }
        

            
        }

        $this->dispatch('calendars:availability-updated');
        $this->open = false;
        $this->resetDrawer();
        $this->success('Las citas han sido creadas correctamente.', css: 'bg-primary text-white');
    }

    public function getEventSlots(array $data = []): array
    {
        $events = collect();

        foreach ($data as $availability) {
            $start = new Carbon($availability['date'] . ' ' . $availability['start_time']);
            $end = new Carbon($availability['date'] . ' ' . $availability['end_time']);
            $intervalMinutes = $availability['duration']; // duración de los bloques (ajustable)
            
            while ($start < $end) {
                $events->push(['date' => $start]);
                $start->addMinutes($intervalMinutes);
            }
        }

        return [
            'days' => $events->groupBy(fn($event) => $event['date']->format('Y-m-d'))->count(),
            'total' => $events->count() * $data[0]['capacity'],
        ];
    }

    /**
     * questions
     */
    public function addQuestion()
    {
        $position = CalendarQuestion::query()
            ->where('calendar_id', $this->form->calendar->id)
            ->max('position') ?? 0;

        $question = [
            'is_active' => true,
            'question' => '',
            'services' => [],
            'position' => $position + 1,
            'is_required' => false,
        ];

        $model = $this->form->calendar->questions()->create($question);

        $this->questions[] = [
            'is_active' => $model->is_active,
            'question' => $model->question,
            'services' => [],
            'calendar_question_id' => $model->id,
            'position' => $model->position,
            'is_required' => $model->is_required,
        ];
    }

    public function removeQuestion(int $index)
    {
        $question = $this->questions[$index];

        if (!is_null($question['calendar_question_id'])) {
            CalendarQuestion::find($question['calendar_question_id'])->delete();
        }

        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    /**
     * computed
     */
    #[Computed]
    public function coverThumbnail()
    {
        if (!is_null($this->cover)) {
            return $this->cover->temporaryUrl();
        }

        if (
            !is_null($this->form->calendar) &&
            !is_null($this->form->calendar->cover)
        ) {
            return $this->form->calendar->cover;
        }

        return asset('images/placeholder.webp');
    }

    #[Computed]
    public function calendarServicesOptions(): array
    {
        if (is_null($this->form->calendar)) {
            return [];
        }

        return $this->form->calendar->services
            ->map(fn($service) => (object) [
                'id' => $service->id,
                'name' => $service->name,
                'avatar' => $service->cover ?? asset('images/placeholder.webp'),
            ])
            ->toArray();
    }

    public function updateQuestionPosition($questions)
    {
        foreach($questions as $question) {
            $model = CalendarQuestion::find($question['value']);
            $model->update(['position' => $question['order']]);
        }

        $this->questions = $this->getCalendarQuestions();
    }

    public function connectGoogleCalendar()
    {
        try {
            return redirect()->route('google.connect');
        } catch (\Exception $e) {
            $this->error('Error al conectar con Google: ' . $e->getMessage());
        }
    }

    public function disconnectGoogleCalendar()
    {
        try {
            $service = new GoogleCalendarService(Auth::user());
            $service->disconnect();
            
            $this->success('Cuenta de Google desconectada');
            $this->dispatch('google-calendar-disconnected');
        } catch (\Exception $e) {
            $this->error('Error al desconectar: ' . $e->getMessage());
        }
    }

    /**
     * render
     */
    public function render()
    {
        $services = collect();

        if ($this->action === 'edit') {
            $services = Service::query()
                ->select('id', 'name')
                ->where('is_active', true)
                ->with('media')
                ->get()
                ->map(fn($service) => (object) [
                    'id' => $service->id,
                    'name' => $service->name,
                    'avatar' => $service->cover ?? asset('images/placeholder.webp'),
                ]);
        }

        return view('livewire.components.dashboard.calendars.drawer', compact('services'));
    }
}
