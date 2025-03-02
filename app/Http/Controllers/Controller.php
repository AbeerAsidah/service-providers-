<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Quick Fix Apis ",
 *     version="1.0.0",

 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api/v1",
 *     description="local Base URL"
 * )
 * @OA\Server(
 *     url="https://quickfix-back.icrcompany.net/api/v1",
 *     description="Develop Base URL"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 * @OA\Components(
 *         @OA\Header(
 *             header="Accept",
 *     description="Header indicating the expected response format. Should be set to 'application/json'.",
 *     required=true,
 *     @OA\Schema(type="string", default="application/json"),
 *
 *         ),
 *
 *     )
 *
 */
//App Apis
//SectionController
/*
* @OA\Get(
*     path="/sections",
*     summary="Get super sections ",
*     operationId="app/super-sections",
*     summary="get sections data",
*     tags={"App", "App - Sections"},
*     @OA\Response(
*         response=200,
*         description="Successful response",
*     )
* )
*
*@OA\Get(
*     path="/admin/sections/{parentSection}",
*     operationId="app/sections",
*     summary="get sections data",
*     tags={"App", "App - Sections"},
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


//Admin apis
abstract class Controller
{
    //
}
