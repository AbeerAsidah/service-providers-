<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Section\SectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/sections/{parentSection}",
     *     operationId="app/sections",
     *     summary="get sections data",
     *     tags={"Admin", "Admin - Sections"},
     *     @OA\Parameter(
     *     name="parentSection",
     *     in="path",
     *     description="pass the parent section id ",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *    security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     */

    public function index(Section $parentSection = null)
    {
        $sections = Section::where("parent_id", $parentSection->id ?? null)->paginate(config("app.pagination_limit"));
        return success(SectionResource::collection($sections));
    }

    /**
     * @OA\Get(
     *     path="/admin/sections/{section_id}",
     *     operationId="app/section",
     *     summary="get section data ",
     *     tags={"Admin", "Admin - Sections"},
     *      @OA\Parameter(
     *     name="section_id",
     *     in="path",
     *     description="pass the section ",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *    security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Section $section)
    {
        return success(SectionResource::make($section));
    }


    /**
     * @OA\Post(
     *      path="/admin/sections/{parentSection}/{type}",
     *      operationId="post-store-section",
     *     tags={"Admin", "Admin - Sections"},
     *     @OA\Parameter(
     *     name="parentSection",
     *     in="path",
     *     description="pass the parent section id , dont pass it if its super section  ",
     *     required=false,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *     @OA\Parameter(
     *     name="type",
     *     in="path",
     *     description="pass it courses or course_sections  dont pass it if you want to add super section ",
     *     required=false,
     *     @OA\Schema(
     *         type="string"
     *     )
     *      ),
     *      security={{ "bearerAuth": {} }},
     *      summary="Store Section data",
     *      description="Store Section with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Section data",
     *              @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              @OA\Property(property="name", type="string", example="section name "),
     *              @OA\Property(property="is_free", type="integer", example="1"),
     *              @OA\Property(property="description", type="string", example="lorem upsum"),
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

    public function store(SectionRequest $request, Section $parentSection = null, $type = null)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('sections/images', 'public');
        }
        if ($parentSection)
            $data['parent_id'] = $parentSection->id;
        $section = Section::create($data);
        return success(SectionResource::make($section), 201);
    }


    /**
     * @OA\Post(
     *      path="/admin/sections/{id}",
     *      operationId="store-section",
     *     tags={"Admin", "Admin - Sections"},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="section id to update ",
     *     required=false,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *      security={{ "bearerAuth": {} }},
     *      summary="Update Section data",
     *      description="Update Section with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Section data",
     *              @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              @OA\Property(property="name", type="string", example="section name "),
     *              @OA\Property(property="description", type="string", example="lorem upsum"),
     *              @OA\Property(property="is_free", type="integer", example="1"),
     *              @OA\Property(property="_method", type="string", example="PUT"),
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
    public function update(SectionRequest $request, Section $section)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('sections/images', 'public');
            if (Storage::exists("public/$section->image")) {
                Storage::delete("public/$section->image");
            }
        }
        $section->update($data);
        return success(SectionResource::make($section));
    }


    /**
     * @OA\Delete(
     *     path="/admin/sections/{id}",
     *     summary="Delete a Section",
     *     tags={"Admin", "Admin - Sections"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the section to delete",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Section deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Section not found"
     *     )
     * )
     */


    public function destroy(Section $section)
    {
        $section->deleteOrFail();
        return success();
    }
}
