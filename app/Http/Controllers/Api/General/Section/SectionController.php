<?php

namespace App\Http\Controllers\Api\General\Section;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\Section;

class SectionController extends Controller
{
    /**
     * @OA\Get(
     *       path="/sections",
     *       operationId="app/super-sections",
     *       summary="get super sections data",
     *       tags={"App", "App - Sections"},
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *      @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Locale of the branch data (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"none", "ar", "en"})
     *     ),
     *      @OA\Response(response=200, description="Successful operation"),
     *   )
     * @OA\Get(
     *      path="/sections/{parentSection}",
     *      operationId="app/sections",
     *      summary="get brands data",
     *      tags={"App", "App - Sections"},
     *      @OA\Parameter(
     *      name="parentSection",
     *      in="path",
     *      description="pass the parent section id ",
     *      required=true,
     *      @OA\Schema(
     *          type="integer"
     *      )
     *       ),
     *     security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Locale of the branch data (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"none", "ar", "en"})
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *  )
     *
     */

    public function index(Section $parentSection = null)
    {
        $sections = Section::where("parent_id", $parentSection->id ?? null)->paginate(config("app.pagination_limit"));
        return success(SectionResource::collection($sections));
    }

    /**
     * @OA\Get(
     *      path="/sections/detail/{section_id}",
     *      operationId="app/section",
     *      summary="get section data ",
     *      tags={"App", "App - Sections"},
     *       @OA\Parameter(
     *      name="section_id",
     *      in="path",
     *      description="pass the section ",
     *      required=true,
     *      @OA\Schema(
     *          type="integer"
     *      )
     *       ),
     *     security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Locale of the branch data (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"none", "ar", "en"})
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *  )
     *
     * @OA\Get(
     *     path="/admin/sections/detail/{section_id}",
     *     operationId="admin/section",
     *     summary="get section data ",
     *     tags={"Admin", "Admin - Section"},
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
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Locale of the branch data (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"none", "ar", "en"})
     *     ),
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Section $section)
    {
        return success(SectionResource::make($section));
    }

}
