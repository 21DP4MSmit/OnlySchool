<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\User;

class ConversationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $conversations = Conversation::with('participants', 'messages')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

        $unreadMessageCount = $this->getUnreadMessageCount($user);

        return inertia('Conversations/Index', compact('conversations', 'unreadMessageCount'));
    }

    public function show(Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->participants->contains($user->id)) {
            abort(403, 'Unauthorized access to this conversation.');
        }


        $this->markMessagesAsRead($conversation, $user);

        $conversation->load('messages.user', 'participants');

        return inertia('Conversations/Show', compact('conversation'));
    }




    private function getUnreadMessageCount(User $user)
    {
        $conversationsWithUnread = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereHas('messages', function ($query) use ($user) {
            $query->where('is_read', false)
                ->where('user_id', '!=', $user->id);
        })
        ->with(['messages' => function ($query) use ($user) {
            $query->where('is_read', false)
                ->where('user_id', '!=', $user->id);
        }])
        ->get();

        return $conversationsWithUnread->count();
    }


    private function markMessagesAsRead(Conversation $conversation, User $user)
    {
        $unreadMessages = $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        cache()->forget('unread_message_count_' . $user->id);
    }






    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string|max:255',
            'recipients' => 'required|array',
            'recipients.*' => 'exists:users,id',
            'initialMessage' => 'required|string|max:5000',
        ]);
    
        $conversation = Conversation::create([
            'subject' => $request->subject,
        ]);
    
        $participants = array_merge($request->recipients, [auth()->id()]);
        $conversation->participants()->attach($participants);
    
        $conversation->messages()->create([
            'user_id' => auth()->id(),
            'text' => $request->initialMessage,
        ]);
    
        return redirect()->route('conversations.show', $conversation);
    }
    
    public function leave(Request $request)
    {
        $user = auth()->user();
        cache()->forget('unread_message_count_'.$user->id);

        return redirect()->route('dashboard');
    }



    public function create()
    {
    $users = User::where('id', '!=', auth()->id())->get();
    
    return inertia('Conversations/Create', [
        'users' => $users
    ]);
    }
}

