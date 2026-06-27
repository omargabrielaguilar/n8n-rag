<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Listar las citas (con relaciones cargadas para no sufrir N+1)
     */
    public function index(Request $request)
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'service'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Agendar una nueva cita
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::create($validated);
        $appointment->load(['service', 'doctor', 'patient']);

        return response()->json([
            'message' => 'Cita agendada exitosamente',
            'data' => $appointment
        ], 201);
    }

    /**
     * Actualizar estado de la cita (Confirmar, Completar, Cancelar)
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,canceled'
        ]);

        $appointment->update(['status' => $validated['status']]);

        return response()->json([
            'message' => "Estado actualizado a {$validated['status']}",
            'data' => $appointment
        ]);
    }
}
