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

        // Get all conversations where the current user is a participant
        $conversations = Conversation::with('participants', 'messages')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

        // Calculate unread messages count for conversations where the user is a participant
        $unreadMessageCount = $this->getUnreadMessageCount($user);

        return inertia('Conversations/Index', compact('conversations', 'unreadMessageCount'));
    }

    public function show(Conversation $conversation)
    {
        $user = auth()->user();

        // Ensure that only participants can access the conversation
        if (!$conversation->participants->contains($user->id)) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        // Mark unread messages as read for the current user
        $this->markMessagesAsRead($conversation, $user);

        // Ensure we are loading messages and participants
        $conversation->load('messages.user', 'participants');

        return inertia('Conversations/Show', compact('conversation'));
    }



    // Helper method to calculate unread message count
    private function getUnreadMessageCount(User $user)
    {
        return Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id); // Ensure the user is a participant
        })
        ->whereHas('messages', function ($query) use ($user) {
            $query->where('is_read', false)->where('user_id', '!=', $user->id);
        })->count();
    }

    // Helper method to mark messages as read
    private function markMessagesAsRead(Conversation $conversation, User $user)
    {
        $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
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
    
        // Add the current user and recipients as participants
        $participants = array_merge($request->recipients, [auth()->id()]);
        $conversation->participants()->attach($participants);
    
        // Add the initial message to the conversation
        $conversation->messages()->create([
            'user_id' => auth()->id(),
            'text' => $request->initialMessage,
        ]);
    
        return redirect()->route('conversations.show', $conversation);
    }
    
    public function leave(Request $request)
    {
        $user = auth()->user();

        // Ensure cache is cleared for unread messages
        cache()->forget('unread_message_count_'.$user->id);

        return redirect()->route('dashboard'); // Or any other desired route
    }



    public function create()
    {
    $users = User::where('id', '!=', auth()->id())->get();
    
    return inertia('Conversations/Create', [
        'users' => $users
    ]);
    }
}

