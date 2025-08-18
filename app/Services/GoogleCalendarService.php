<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;
    protected $calendarService;
    protected $user;

    public function __construct(User $user = null)
    {
        $this->user = $user ?: auth()->user();
        $this->initializeClient();
    }

    protected function initializeClient()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent'); // importante para forzar refresh_token la 1ra vez
        $this->client->setIncludeGrantedScopes(true);

        // Cargar tokens si existen en BD
        if ($this->user->google_access_token) {
            $token = [
                'access_token'  => $this->user->google_access_token,
                'refresh_token' => $this->user->google_refresh_token,
                'expires_in'    => $this->user->google_token_expires_at
                    ? Carbon::parse($this->user->google_token_expires_at)->timestamp - now()->timestamp
                    : null,
                'created'       => now()->timestamp,
            ];

            $this->client->setAccessToken($token);

            // Si expiró, refrescar
            if ($this->client->isAccessTokenExpired()) {
                $this->refreshToken();
            }
        }

        $this->calendarService = new Calendar($this->client);
    }

    protected function refreshToken()
    {
        try {
            if (!$this->user->google_refresh_token) {
                Log::warning("El usuario {$this->user->id} no tiene refresh_token.");
                return false;
            }

            $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->user->google_refresh_token);

            if (isset($newToken['error'])) {
                Log::error('Error refrescando Google token: ' . $newToken['error']);
                return false;
            }

            $this->user->update([
                'google_access_token'     => $newToken['access_token'],
                'google_token_expires_at' => now()->addSeconds($newToken['expires_in']),
            ]);

            // Actualizar cliente
            $this->client->setAccessToken($newToken);

            return true;
        } catch (\Exception $e) {
            Log::error('Excepción al refrescar Google token: ' . $e->getMessage());
            return false;
        }
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback($code)
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                Log::error('Error en callback de Google: ' . $token['error']);
                return false;
            }

            $this->user->update([
                'google_access_token'     => $token['access_token'],
                // Guardar refresh_token SOLO si viene en la respuesta
                'google_refresh_token'    => $token['refresh_token'] ?? $this->user->google_refresh_token,
                'google_token_expires_at' => now()->addSeconds($token['expires_in']),
            ]);

            // Obtener calendario principal
            $calendar = $this->calendarService->calendars->get('primary');
            $this->user->update(['google_calendar_id' => $calendar->getId()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar callback error: ' . $e->getMessage());
            return false;
        }
    }

    public function createEvent($eventData)
    {
        $this->refreshToken();

        $event = new Event([
            'summary'     => $eventData['summary'] ?? 'Nueva cita',
            'description' => $eventData['description'] ?? '',
            'colorId'     => $eventData['colorId'] ?? null,
        ]);

        // Inicio
        $start = new EventDateTime();
        $start->setDateTime(Carbon::parse($eventData['start']['dateTime'])->toIso8601String());
        $start->setTimeZone(config('app.timezone'));
        $event->setStart($start);

        // Fin
        $end = new EventDateTime();
        $end->setDateTime(Carbon::parse($eventData['end']['dateTime'])->toIso8601String());
        $end->setTimeZone(config('app.timezone'));
        $event->setEnd($end);

        // Recordatorios opcionales
        if (isset($eventData['reminders'])) {
            $reminders = new \Google\Service\Calendar\EventReminders();
            $reminders->setUseDefault(false);
            $reminders->setOverrides([['method' => 'popup', 'minutes' => 30]]);
            $event->setReminders($reminders);
        }

        $calendarId = $this->user->google_calendar_id ?? 'primary';
        $createdEvent = $this->calendarService->events->insert($calendarId, $event);

        return [
            'success'   => true,
            'event_id'  => $createdEvent->getId(),
            'html_link' => $createdEvent->getHtmlLink()
        ];
    }

    public function updateEvent($eventId, $eventData)
    {
        try {
            $calendarId = $this->user->google_calendar_id ?? 'primary';
            $event = $this->calendarService->events->get($calendarId, $eventId);

            if (isset($eventData['summary'])) $event->setSummary($eventData['summary']);
            if (isset($eventData['description'])) $event->setDescription($eventData['description']);

            if (isset($eventData['start']['dateTime'])) {
                $start = new EventDateTime();
                $start->setDateTime(Carbon::parse($eventData['start']['dateTime'])->toIso8601String());
                $start->setTimeZone(config('app.timezone'));
                $event->setStart($start);
            }

            if (isset($eventData['end']['dateTime'])) {
                $end = new EventDateTime();
                $end->setDateTime(Carbon::parse($eventData['end']['dateTime'])->toIso8601String());
                $end->setTimeZone(config('app.timezone'));
                $event->setEnd($end);
            }

            $updatedEvent = $this->calendarService->events->update($calendarId, $event->getId(), $event);

            return [
                'success'  => true,
                'event_id' => $updatedEvent->getId()
            ];
        } catch (\Exception $e) {
            Log::error('Error updating Google Calendar event: ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    public function deleteEvent($eventId)
    {
        try {
            $calendarId = $this->user->google_calendar_id ?? 'primary';
            $this->calendarService->events->delete($calendarId, $eventId);

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Error deleting Google Calendar event: ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    public function listCalendars()
    {
        try {
            $calendarList = $this->calendarService->calendarList->listCalendarList();
            return $calendarList->getItems();
        } catch (\Exception $e) {
            Log::error('Error listing Google Calendars: ' . $e->getMessage());
            return [];
        }
    }

    public function setCalendar($calendarId)
    {
        $this->user->update(['google_calendar_id' => $calendarId]);
        return true;
    }

    public function disconnect()
    {
        $this->user->update([
            'google_access_token'     => null,
            'google_refresh_token'    => null,
            'google_token_expires_at' => null,
            'google_calendar_id'      => null,
        ]);
        return true;
    }

    public function isConnected()
    {
        return !empty($this->user->google_access_token);
    }
}
