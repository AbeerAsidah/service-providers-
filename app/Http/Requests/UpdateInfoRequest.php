<?php

namespace App\Http\Requests;

use App\Models\Info;
use App\Rules\OneOrNone;
use Illuminate\Validation\Rule;
use App\Constants\InfoValidationRules;
use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="UpdateInfoRequest",
 *     type="object",
 *     title="Update Info Request",
 *     description="Request body for updating information",
 *     @OA\Property(
 *         property="hero-description-en",
 *         type="string",
 *         description="Hero description in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="hero-description-ar",
 *         type="string",
 *         description="Hero description in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="hero-hours_count",
 *         type="string",
 *         description="Number of hours",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="hero-students_count",
 *         type="string",
 *         description="Number of students",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="hero-courses_count",
 *         type="string",
 *         description="Number of courses",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="sections-header-en",
 *         type="string",
 *         description="Section header in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="sections-header-ar",
 *         type="string",
 *         description="Section header in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="courses-header-en",
 *         type="string",
 *         description="Courses header in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="courses-header-ar",
 *         type="string",
 *         description="Courses header in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-description-en",
 *         type="string",
 *         description="Overview description in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-description-ar",
 *         type="string",
 *         description="Overview description in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-online_degrees-en",
 *         type="string",
 *         description="Online degrees overview in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-online_degrees-ar",
 *         type="string",
 *         description="Online degrees overview in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-short_courses-en",
 *         type="string",
 *         description="Short courses overview in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-short_courses-ar",
 *         type="string",
 *         description="Short courses overview in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-professional_instructors-en",
 *         type="string",
 *         description="Professional instructors overview in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-professional_instructors-ar",
 *         type="string",
 *         description="Professional instructors overview in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="overview-image",
 *         type="string",
 *         format="binary",
 *         description="Image overview",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="instructors-header-en",
 *         type="string",
 *         description="Instructors header in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="instructors-header-ar",
 *         type="string",
 *         description="Instructors header in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="footer-email",
 *         type="string",
 *         format="email",
 *         description="Footer email",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="footer-phone",
 *         type="string",
 *         description="Footer phone number",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="application-description-en",
 *         type="string",
 *         description="Application description in English",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="application-description-ar",
 *         type="string",
 *         description="Application description in Arabic",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="application-app_store",
 *         type="string",
 *         description="Application app store link",
 *         nullable=true,
 *         default="http://www.google.com",
 *     ),
 *     @OA\Property(
 *         property="application-google_play",
 *         type="string",
 *         description="Application Google Play link",
 *         nullable=true,
 *         default="http://www.google.com",
 *     )
 * )
 */
class UpdateInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request-
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request-
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hero-description-ar' => ['string'],
            'hero-description-en' => ['string'],
            'hero-hours_count' => ['string'],
            'hero-students_count' => ['string'],
            'hero-courses_count' => ['string'],
            'sections-header-ar' => ['string'],
            'sections-header-en' => ['string'],
            'courses-header-ar' => ['string'],
            'courses-header-en' => ['string'],
            'overview-description-en' => ['string'],
            'overview-description-ar' => ['string'],
            'overview-online_degrees-en' => ['string'],
            'overview-online_degrees-ar' => ['string'],
            'overview-short_courses-en' => ['string'],
            'overview-short_courses-ar' => ['string'],
            'overview-professional_instructors-en' => ['string'],
            'overview-professional_instructors-ar' => ['string'],
            'overview-image' => ['image'],
            'instructors-header-ar' => ['string'],
            'instructors-header-en' => ['string'],
            'footer-email' => ['string', 'email'],
            'footer-phone' => ['string'],
            'application-description-en' => ['string'] , 
            'application-description-ar' => ['string'] , 
            'application-app_store' => ['string' , 'url'] , 
            'application-google_play' => ['string' , 'url'] , 
        ];
    }
}
