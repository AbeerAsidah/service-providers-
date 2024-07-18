<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Http\Resources\ContactMessageResource;
use App\Http\Requests\StoreContactMessageRequest;

class ContactMessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/contact-messages",
     *     summary="Get a list of contact messages",
     *     tags={"ContactMessage"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of contact messages",
     *          @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ContactMessageResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function index()
    {
        //todo authorize for admin

        $messages = ContactMessage::orderByDesc('created_at')->paginate(20);

        return ContactMessageResource::collection($messages);
    }

    /**
     * @OA\Post(
     *     path="/contact-messages",
     *     summary="Create a new contact message",
     *     tags={"App , ContactMessage"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="The name of the person sending the message",
     *                 example="John Doe"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="The email of the person sending the message",
     *                 example="johndoe@example.com"
     *             ),
     *             @OA\Property(
     *                 property="phone",
     *                 type="string",
     *                 description="The phone number of the person sending the message",
     *                 example="12345678"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="The content of the message",
     *                 example="Hello, I would like to know more about your services."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact message created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ContactMessageResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */

    public function store(StoreContactMessageRequest $request)
    {
        $message = ContactMessage::create($request->validated());

        return success(ContactMessageResource::make($message));
    }

    /**
     * @OA\Delete(
     *     path="/admin/contact-messages/{id}",
     *     summary="Soft delete a contact message",
     *     tags={"ContactMessage"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact message to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact message soft deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="soft deleted"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function delete(ContactMessage $contactMessage)
    {
        //todo authorize for admin
        $contactMessage->deleteOrFail();
        return success(['message' => 'soft deleted'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/admin/contact-messages/{id}/force",
     *     summary="Force delete a contact message",
     *     tags={"ContactMessage"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the trashed contact message to force delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact message force deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="force deleted"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */

    public function forceDelete(ContactMessage $TrashedContactMessage)
    {
        //todo authorize for admin

        $TrashedContactMessage->forceDelete();

        return success(['message' => 'force deleted'], 200);
    }


    /**
     * @OA\Patch(
     *     path="/admin/contact-messages/{id}/restore",
     *     summary="Restore a soft-deleted contact message",
     *     tags={"ContactMessage"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the trashed contact message to restore",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact message restored",
     *         @OA\JsonContent(ref="#/components/schemas/ContactMessageResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */

    public function restore(ContactMessage $TrashedContactMessage)
    {
        //todo authorize for admin
        $TrashedContactMessage->restore();
        return success(ContactMessageResource::make($TrashedContactMessage));
    }
}
