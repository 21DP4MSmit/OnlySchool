<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;


class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->user_id = auth()->id();
        $message->text = $request->text;
        $message->save();

        return redirect()->back();
    }

    public function markAsRead(Conversation $conversation)
    {
        $user = auth()->user();
        $conversation->messages()->where('user_id', '!=', $user->id)->update(['is_read' => true]);

        return redirect()->back();
    }
}