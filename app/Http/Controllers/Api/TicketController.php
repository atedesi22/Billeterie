<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    //
    public function validate(Request $request)
    {
        $ticket = Ticket::where('uuid', $request->uuid)->first();

        if (! $ticket) {
            // code...
            return response()->json(['status' => 'NOT_FOUND', 'message' => 'Billet invalide !'], 404);
        }

        if ($ticket->is_scanned) {
            return response()->json([
                'status' => 'ALREADY_USED',
                'message' => 'Attention ! Ce billet a déjà été utilisé.',
                'user' => $ticket->user_name,
                'time' => $ticket->scanned_at->format('H:i'),
            ], 403);
        }

        // Marquer comme utilisé
        $ticket->update([
            'is_scanned' => true,
            'scanned_at' => now(),
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Billet valide. Bienvenue !',
            'user' => $ticket->user_name,
            'type' => $ticket->type, // Pour savoir si on donne le repas (Full Conso)
        ]);

    }

    public function checkAvailability()
    {
        $soldTickets = Ticket::count();
        if ($soldTickets >= 70) {
            return response()->json(['message' => 'SOLD_OUT'], 422);
        }
    }
}
