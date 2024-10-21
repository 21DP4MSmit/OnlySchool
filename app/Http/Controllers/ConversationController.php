<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\User;

class ConversationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get all conversations for the user
        $conversations = Conversation::with('participants', 'messages')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

        // Calculate unread messages count
        $unreadMessageCount = Conversation::whereHas('messages', function ($query) use ($user) {
            $query->where('is_read', false)->where('user_id', '!=', $user->id);
        })->count();

        return inertia('Conversations/Index', compact('conversations', 'unreadMessageCount'));
    }

    public function show(Conversation $conversation) 
    {
        $conversation->load('messages.user');

        // Update messages to mark them as read
        foreach ($conversation->messages as $message) {
            if ($message->user_id != auth()->id() && !$message->is_read) {
                $message->update(['is_read' => true]);
            }
        }

        return inertia('Conversations/Show', compact('conversation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string|max:255',
            'recipients' => 'required|array',
            'recipients.*' => 'exists:users,id',
            'initialMessage' => 'required|string|max:5000', // Validate the initial message
        ]);

        // Create the new conversation
        $conversation = Conversation::create([
            'subject' => $request->subject,
        ]);

        // Add all selected recipients and the current user to the conversation
        $participants = array_merge($request->recipients, [auth()->id()]);
        $conversation->participants()->attach($participants);

        // Add the initial message
        $conversation->messages()->create([
            'user_id' => auth()->id(),
            'text' => $request->initialMessage,
        ]);

        return redirect()->route('conversations.index');
    }

    public function create()
    {
    $users = User::where('id', '!=', auth()->id())->get();
    
    return inertia('Conversations/Create', [
        'users' => $users
    ]);
    }
}

