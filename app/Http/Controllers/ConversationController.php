<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ConversationController extends Controller
{
    use AuthorizesRequests;

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

        // Add `initialParticipantCount` to keep track of the original participant count
        $conversation->initialParticipantCount = $conversation->participants()->count();

        $conversation->load('messages.user', 'participants');

        return inertia('Conversations/Show', [
            'conversation' => $conversation,
            'initialParticipantCount' => $conversation->initialParticipantCount,
        ]);
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
        $conversation->messages()
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

        $recipientIds = $request->recipients;

        // Case 1: Check for an existing one-on-one chat when there's only one recipient
        if (count($recipientIds) === 1) {
            $recipientId = $recipientIds[0];

            // Find an existing one-on-one conversation between the current user and the recipient
            $existingConversation = Conversation::whereHas('participants', function ($query) use ($recipientId) {
                $query->where('user_id', auth()->id())
                    ->orWhere('user_id', $recipientId);
            })
            ->whereDoesntHave('participants', function ($query) use ($recipientId) {
                $query->whereNotIn('user_id', [auth()->id(), $recipientId]);
            })
            ->first();

            // Redirect to the existing one-on-one conversation if it exists
            if ($existingConversation) {
                return redirect()->route('conversations.show', $existingConversation->id);
            }
        }

        // Case 2: Create a new conversation (group or one-on-one if no existing conversation was found)
        $conversation = Conversation::create([
            'subject' => $request->subject,
        ]);

        // Attach all recipients and the current user to the new conversation
        $participants = array_merge($recipientIds, [auth()->id()]);
        $conversation->participants()->attach($participants);

        // Create the initial message
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

    public function removeParticipant(Request $request, Conversation $conversation)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($conversation->participants()->count() > 2 || $conversation->is_group) {
            $conversation->participants()->detach($request->user_id);
        }

        return redirect()->back();
    }

   public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return redirect()->route('conversations.index')->with('success', 'Conversation deleted successfully.');
    }
}
