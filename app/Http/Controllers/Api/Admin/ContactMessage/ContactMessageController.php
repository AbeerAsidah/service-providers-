<?php

namespace App\Http\Controllers\Api\Admin\ContactMessage;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Services\Admin\ContactMessage\ContactMessageService as AdminContactMessageService;

class ContactMessageController extends Controller
{
    public function __construct(protected AdminContactMessageService $contactMessagesService)
    {
    }

    /**
     * @OA\Get(
     *     path="/admin/contact-messages",
     *     @OA\Parameter(
     *      name="trash",
     *      in="query",
     *      description="pass is to get the trashed objects ",
     *      required=false,
     *      @OA\Schema(
     *          type="integer"
     *      )
     *       ),
     *     security={{ "bearerAuth": {} }},
     *     summary="Get a list of contact messages",
     *     tags={"Admin" , "Admin - ContactMessage"},
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
    public function index(Request $request)
    {
        return $this->contactMessagesService->getAll($request->trash);
    }


    /**
     * @OA\Delete(
     *      path="/admin/contact-messages/{id}/force",
     *      summary="force delete a contact message",
     *      tags={"Admin" , "Admin - ContactMessage"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the contact message to delete",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Contact message soft deleted",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="soft deleted"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      )
     *  )
     *
     * @OA\Delete(
     *     path="/admin/contact-messages/{id}",
     *     summary="Soft delete a contact message",
     *     tags={"Admin" , "Admin - ContactMessage"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the contact message to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{ "bearerAuth": {} }},
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
    public function delete(ContactMessage $contactMessage, $force = null)
    {
        if ($force) {
            $contactMessage->forceDelete();
        } else {
            $contactMessage->deleteOrFail();
        }
        return success();
    }


    /**
     * @OA\Patch(
     *     path="/admin/contact-messages/{id}/restore",
     *     summary="Restore a soft-deleted contact message",
     *     tags={"Admin" , "Admin - ContactMessage"},
     *     security={{ "bearerAuth": {} }},
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

    public function restore(ContactMessage $contactMessage)
    {
        $contactMessage->restore();
        return success(ContactMessageResource::make($contactMessage));
    }
}
