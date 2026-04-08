<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Adresse du client qui doit recevoir le message
        $clientEmail = env('CONTACT_RECEIVER_EMAIL');

        Mail::to($clientEmail)->send(
            new ContactMessageMail(
                $validatedData['name'],
                $validatedData['email'],
                $validatedData['subject'],
                $validatedData['message']
            )
        );

        return response()->json([
            'message' => 'Message envoyé avec succès.'
        ], 200);
    }
}