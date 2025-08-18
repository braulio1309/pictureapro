<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    protected $calendarService;

    public function __construct()
    {
        $this->calendarService = new GoogleCalendarService();
    }

    /**
     * Redirecciona a la autenticación de Google
     */
    public function connect(Request $request)
    {
        $request->session()->put('google_calendar_redirect', $request->headers->get('referer'));

        $authUrl = $this->calendarService->getAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Maneja el callback de Google OAuth
     */
    public function callback(Request $request)
    {
        try {
            if ($request->has('error')) {
                throw new \Exception('Error de autorización: ' . $request->error);
            }

            if (!$request->has('code')) {
                throw new \Exception('Código de autorización no recibido');
            }

            $success = $this->calendarService->handleCallback($request->code);

            if (!$success) {
                throw new \Exception('Error al procesar la autorización');
            }

            $redirectUrl = $request->session()->get('google_calendar_redirect', route('dashboard'));

            return redirect()->to($redirectUrl)
                ->with('success', 'Cuenta de Google Calendar conectada correctamente');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Error al conectar con Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Desconecta la cuenta de Google
     */
    public function disconnect()
    {
        try {
            $this->calendarService->disconnect();

            return redirect()->back()
                ->with('success', 'Cuenta de Google Calendar desconectada correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al desconectar: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los calendarios disponibles del usuario
     */
    public function getCalendars()
    {
        try {
            if (!$this->calendarService->isConnected()) {
                return response()->json(['error' => 'No conectado a Google'], 401);
            }

            $calendars = $this->calendarService->listCalendars();
            return response()->json($calendars);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Establece el calendario a usar
     */
    public function setCalendar(Request $request)
    {
        try {
            $validated = $request->validate([
                'calendar_id' => 'required|string'
            ]);

            $this->calendarService->setCalendar($validated['calendar_id']);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}