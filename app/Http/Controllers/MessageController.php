<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'text' => 'required|string',
            'attachments.*' => 'file|max:5120',
        ]);

        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->user_id = auth()->id();
        $message->text = $request->text;

        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $filePath = $file->store('attachments', 'public');
                $attachments[] = $filePath;
            }
            $message->attachments = json_encode($attachments);
        }

        $message->save();

        // Broadcast the MessageSent event
        broadcast(new MessageSent($message))->toOthers();

        if ($request->wantsJson()) {
            return response()->json($message);
        }

        return redirect()->route('conversations.show', $conversation->id);
    }
}
