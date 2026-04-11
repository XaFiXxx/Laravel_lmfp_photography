<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterMail;
use App\Mail\SharePostMail;
use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    // ========================
    // SUBSCRIBE
    // ========================
    public function subscribe(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($validatedData['email']));

        $subscriber = Subscriber::where('email', $email)->first();

        if (!$subscriber) {
            Subscriber::create([
                'email' => $email,
                'is_active' => true,
                'unsubscribe_token' => Str::uuid(),
                'subscribed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Inscription réussie à la newsletter.'
            ], 201);
        }

        if ($subscriber->is_active) {
            return response()->json([
                'message' => 'Cet email est déjà abonné.'
            ], 200);
        }

        $subscriber->update([
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        return response()->json([
            'message' => 'Votre abonnement a été réactivé.'
        ], 200);
    }

    // ========================
    // UNSUBSCRIBE
    // ========================
    public function unsubscribe($token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return response()->json([
                'message' => 'Lien invalide.'
            ], 404);
        }

        if (!$subscriber->is_active) {
            return response()->json([
                'message' => 'Vous êtes déjà désabonné.'
            ], 200);
        }

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Vous êtes désabonné de la newsletter.'
        ], 200);
    }

    // ========================
    // INDEX DASHBOARD
    // ========================
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $subscribers = Subscriber::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('email', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(12);

        return response()->json([
            'subscribers' => $subscribers,
            'stats' => [
                'total' => Subscriber::count(),
                'active' => Subscriber::where('is_active', true)->count(),
                'inactive' => Subscriber::where('is_active', false)->count(),
            ],
        ]);
    }

    // ========================
    // SEND FREE NEWSLETTER
    // ========================
    public function send(Request $request)
    {
        $validatedData = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
        ]);

        $subscribers = Subscriber::where('is_active', true)->get();

        if ($subscribers->isEmpty()) {
            return response()->json([
                'message' => 'Aucun abonné actif pour recevoir la newsletter.'
            ], 422);
        }

        foreach ($subscribers as $subscriber) {
            $unsubscribeUrl = config('app.url') . '/api/newsletter/unsubscribe/' . $subscriber->unsubscribe_token;

            Mail::to($subscriber->email)->send(
                new NewsletterMail(
                    $validatedData['subject'],
                    $validatedData['content'],
                    $unsubscribeUrl
                )
            );
        }

        return response()->json([
            'message' => 'Newsletter envoyée avec succès.'
        ], 200);
    }

    // ========================
    // SEND POST NEWSLETTER
    // ========================
    public function sendPost(Request $request)
    {
        $validatedData = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:10000',
        ]);

        $post = Post::findOrFail($validatedData['post_id']);

        $subscribers = Subscriber::where('is_active', true)->get();

        if ($subscribers->isEmpty()) {
            return response()->json([
                'message' => 'Aucun abonné actif pour recevoir cet envoi.'
            ], 422);
        }

        $frontendUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/');
        $postUrl = $frontendUrl . '/posts/' . $post->slug;

        $subject = !empty($validatedData['subject'])
            ? $validatedData['subject']
            : 'Nouveau post en ligne : ' . $post->title;

        $message = !empty($validatedData['message'])
            ? $validatedData['message']
            : ($post->description ?: 'Un nouvel article est disponible sur le site.');

        foreach ($subscribers as $subscriber) {
            $unsubscribeUrl = config('app.url') . '/api/newsletter/unsubscribe/' . $subscriber->unsubscribe_token;

            Mail::to($subscriber->email)->send(
                new SharePostMail(
                    $subject,
                    $message,
                    $post->title,
                    $post->description,
                    $postUrl,
                    $post->image,
                    $unsubscribeUrl
                )
            );
        }

        return response()->json([
            'message' => 'Le mail de partage du post a bien été envoyé aux abonnés actifs.'
        ], 200);
    }
}