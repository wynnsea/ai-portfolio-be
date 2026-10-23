<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMessageStatusRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('perPage', ContentController::PER_PAGE), 1), 100);

        $messages = Message::query()
            ->adminListing([...$request->query(), 'sort' => $request->query('sort', '-created_at')])
            ->paginate($perPage)
            ->withQueryString();

        return MessageResource::collection($messages);
    }

    /**
     * Opening a new message marks it as read.
     */
    public function show(Message $message): MessageResource
    {
        $message->markAsRead();

        return new MessageResource($message);
    }

    public function update(UpdateMessageStatusRequest $request, Message $message): MessageResource
    {
        $message->update($request->validated());

        return new MessageResource($message);
    }

    public function destroy(Message $message): Response
    {
        $message->delete();

        return response()->noContent();
    }
}
