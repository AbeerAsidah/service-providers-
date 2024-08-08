<?php

namespace App\Http\Controllers\Api\Admin\Section;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Section\StoreSectionRequest;
use App\Http\Requests\Api\Admin\Section\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Services\Admin\Section\SectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function __construct(protected SectionService $sectionService)
    {
    }
    /** 
     * @OA\Get(
     *      path="/admin/sections",
     *      operationId="admin/super-sections",
     *      summary="get super sections data",
     *      tags={"Admin", "Admin - Section"},
     *     security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="trash",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             enum={0, 1},
     *             example=1
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *  )
     *  @OA\Get(
     *     path="/admin/sections/{parentSection}",
     *     operationId="admin/sections",
     *     summary="get brands data",
     *     tags={"Admin", "Admin - Section"},
     *     @OA\Parameter(
     *     name="parentSection",
     *     in="path",
     *     description="pass the parent section id ",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *     @OA\Parameter(
     *         name="trash",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             enum={0, 1},
     *             example=1
     *         )
     *     ),
     *    security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     */

     public function index(Request $request, Section $parentSection = null)
    {
        return $this->sectionService->getAll($request->trash, $parentSection);
    }
    /**
     * @OA\Post(
     *       path="/admin/sections",
     *       operationId="post-super-section",
     *      tags={"Admin", "Admin - Section"},
     *       security={{ "bearerAuth": {} }},
     *       summary="Store Super Section data",
     *       description="Store Super Section with the provided information",
     *       @OA\RequestBody(
     *           required=true,
     *           description="Section data",
     *               @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *               required={"ar_name", "en_name", "image"},
     *               @OA\Property(property="ar_name", type="string", example="Arabic section name "),
     *               @OA\Property(property="en_name", type="string", example="English section name "),
     *               @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary",
     *                      description="Image file to upload"
     *                  ),
     *           ),
     *    ),
     *       ),
     *       @OA\Response(
     *           response=201,
     *           description="Section stored successfully",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Section stored successfully"),
     *           )
     *       ),
     *       @OA\Response(
     *           response=422,
     *           description="Validation error",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="The given data was invalid."),
     *           )
     *       ),
     *  )
     * @OA\Post(
     *      path="/admin/sections/{parentSection}/{type}",
     *      operationId="post-store-section",
     *     tags={"Admin", "Admin - Section"},
     *     @OA\Parameter(
     *     name="parentSection",
     *     in="path",
     *     description="pass the parent section id  ",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *     @OA\Parameter(
     *     name="type",
     *     in="path",
     *     description="pass it brands ",
     *     @OA\Schema(
     *         type="string"
     *     )
     *      ),
     *      security={{ "bearerAuth": {} }},
     *      summary="Store brand data",
     *      description="Store brand with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Section data",
     *              @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              required={"ar_name","en_name","image"},
     *              @OA\Property(property="ar_name", type="string", example="Arabic brand name "),
     *              @OA\Property(property="en_name", type="string", example="English brand name "),
     *              @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *          ),
     *   ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Section stored successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Section udpated successfully"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     * )
     */
    public function store(StoreSectionRequest $request, Section $parentSection = null, $type = null)
    {
        try {
            return success($this->sectionService->store($request, $parentSection, $type), 201);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }

    }


    /**
     * @OA\Post(
     *      path="/admin/sections/{id}",
     *      operationId="store-section",
     *     tags={"Admin", "Admin - Section"},
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        description="section id to update ",
     *        required=false,
     *        @OA\Schema(
     *            type="integer"
     *        )
     *      ),
     *     @OA\Parameter(
     *         name="_method",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", enum={"PUT"}, default="PUT")
     *     ),
     *      security={{ "bearerAuth": {} }},
     *      summary="Update Section data",
     *      description="Update Section with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Section data",
     *              @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              @OA\Property(property="ar_name", type="string", example="Arabic section name "),
     *              @OA\Property(property="en_name", type="string", example="English section name "),
     *              @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *          ),
     *   ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Section updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Section udpated successfully"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     * )
     */
    //todo validate the type of the course in sections tree rules

    public function update(UpdateSectionRequest $request, Section $section)
    {
        try {
            return success($this->sectionService->update($request, $section));
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }

    }

    /**
     * @OA\Delete(
     *     path="/admin/sections/{id}/force",
     *     tags={"Admin", "Admin - Section"},
     *     summary="Delete an section or brand",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the section or brand to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     * 
     * * @OA\Delete(
     *     path="/admin/sections/{id}",
     *     tags={"Admin", "Admin - Section"},
     *     summary="Delete an section or brand",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the section or brand to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function delete($id, $force = null)
    {
        try {
            return success($this->sectionService->delete($id, $force));
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }
    /**
     * @OA\Get(
     *     path="/admin/sections/{id}/restore",
     *     tags={"Admin", "Admin - Section"},
     *     summary="Restore a soft-deleted section or brand",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function restore($id)
    {
        try {
            return success($this->sectionService->restore($id));
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }
}
