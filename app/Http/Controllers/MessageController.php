<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    public function index()
    {

    }

    public function userChat($id)
    {
        $userId = auth()->id();
        $receiver = User::findOrFail($id);
        return view('chat', compact('receiver', 'userId'));
    }

    public function fetchMessages(Request $request)
    {
        return Message::where(function ($query) use ($request) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $request->receiver_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('sender_id', $request->receiver_id)
                  ->where('receiver_id', Auth::id());
        })->get();
    }

    public function store(Request $request)
    {
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        // broadcast(new MessageSent($message))->toOthers();
        event(new MessageSent($message));

        return response()->json(['message' => $message]);
    }
}
