<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_nome',
        'cliente_whatsapp',
        'barber_id',
        'service_id',
        'data',
        'hora',
        'status',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Cancela automaticamente agendamentos que passaram mais de 1 hora do horário marcado
     * sem comparecimento do cliente.
     */
    public static function autoCancelExpired()
    {
        $appointments = self::where('status', 'agendado')->get();
        $now = now();
        
        foreach ($appointments as $apt) {
            $appointmentTime = \Carbon\Carbon::parse($apt->data . ' ' . $apt->hora);
            // Se o horário agendado + 1 hora já passou, cancela.
            if ($appointmentTime->copy()->addHour()->isPast()) {
                $apt->update(['status' => 'cancelado']);
            }
        }
    }
}