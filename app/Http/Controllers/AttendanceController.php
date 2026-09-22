<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;
use App\Models\Attendance;
use App\Models\Announcement;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $announcement = Announcement::where('is_active', true)->latest()->first();
        return view('colaborador.marcador', [
            'announcement' => $announcement,
            'demoMode'     => config('asefyl.demo_mode'),
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'timestamp'   => 'required|string',
            'signature'   => 'required|string',
            'device_code' => 'required|string',
        ]);

        $device = $this->resolveDevice($request->device_code);
        if (!$device) {
            return response()->json(['error' => 'Estación no autorizada o inactiva en el sistema.'], 403);
        }

        if (!$this->verifySignature($request, $device)) {
            return response()->json(['error' => 'Falla de seguridad: la firma digital de este equipo no es válida.'], 403);
        }

        $now = Carbon::parse($request->timestamp);
        $status = $now->format('H:i') > '08:05' ? 'tardanza' : 'a_tiempo';

        $attendance = Attendance::create([
            'user_id'      => $device->user_id,
            'device_id'    => $device->id,
            'check_in'     => $now,
            'signature_in' => $request->signature,
            'status'       => $status,
        ]);

        return response()->json([
            'success'       => true,
            'attendance_id' => $attendance->id,
            'time'          => $now->format('H:i'),
            'status'        => $status,
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'timestamp'   => 'required|string',
            'signature'   => 'required|string',
            'device_code' => 'required|string',
        ]);

        $device = $this->resolveDevice($request->device_code);
        if (!$device) {
            return response()->json(['error' => 'Estación no autorizada o inactiva en el sistema.'], 403);
        }

        if (!$this->verifySignature($request, $device)) {
            return response()->json(['error' => 'Falla de seguridad: la firma digital de este equipo no es válida.'], 403);
        }

        $now = Carbon::parse($request->timestamp);

        $attendance = Attendance::where('user_id', $device->user_id)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'No se encontró una entrada abierta para registrar la salida.'], 422);
        }

        $attendance->update([
            'check_out'     => $now,
            'signature_out' => $request->signature,
            'status'        => $attendance->status === 'a_tiempo' && $attendance->check_in ? $attendance->status : 'incompleto',
        ]);

        return response()->json([
            'success'       => true,
            'attendance_id' => $attendance->id,
            'time'          => $now->format('H:i'),
        ]);
    }

    private function resolveDevice(string $deviceCode): ?Device
    {
        $device = Device::where('device_code', $deviceCode)->where('is_active', true)->first();

        if ($device) {
            return $device;
        }

        if (config('asefyl.demo_mode')) {
            return Device::create([
                'user_id'     => Auth::id(),
                'device_code' => $deviceCode,
                'public_key'  => '',
                'is_active'   => true,
                'is_demo'     => true,
            ]);
        }

        return null;
    }

    private function verifySignature(Request $request, Device $device): bool
    {
        if (config('asefyl.demo_mode')) {
            return true;
        }

        if (empty($device->public_key)) {
            return false;
        }

        $signature = base64_decode($request->signature);
        $isValid = openssl_verify($request->timestamp, $signature, $device->public_key, OPENSSL_ALGO_SHA256);

        return $isValid === 1;
    }
}
