<?php

namespace Database\Seeders;

use App\Models\Info;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SiteInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siteInfo = [
            'image1_slider1'=> [
                'super_key' => 'image1 for slider1' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage1.png' ,
                    'ar' => 'SiteFiles/sliderImage1.png' ,
                ],
            ] ,
            'image2_slider1'=> [
                'super_key' => 'image2 for slider1' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage1.png' ,
                    'ar' => 'SiteFiles/sliderImage1.png' ,
                ],
            ] ,
            'image3_slider1'=> [
                'super_key' => 'image3 for slider1' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage1.png' ,
                    'ar' => 'SiteFiles/sliderImage1.png' ,
                ],
            ] ,
            'image4_slider1'=> [
                'super_key' => 'image4 for slider1' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage1.png' ,
                    'ar' => 'SiteFiles/sliderImage1.png' ,
                ],
            ] ,
            'image_logo'=> [
                'super_key' => 'logo' ,
                'value' => [
                    'en' => 'SiteFiles/logo.png' ,
                    'ar' => 'SiteFiles/logo.png' ,
                ],
            ] ,
            'image_about'=> [
                'super_key' => 'image for about' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage1.png' ,
                    'ar' => 'SiteFiles/sliderImage1.png' ,
                ],
            ] ,
            'image1_slider2'=> [
                'super_key' => 'image1 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage2.png' ,
                    'ar' => 'SiteFiles/sliderImage2.png' ,
                ],
            ] ,
            'image2_slider2'=> [
                'super_key' => 'image2 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage2.png' ,
                    'ar' => 'SiteFiles/sliderImage2.png' ,
                ],
            ] ,
            'image3_slider2'=> [
                'super_key' => 'image3 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage2.png' ,
                    'ar' => 'SiteFiles/sliderImage2.png' ,
                ],
            ] ,
            'image4_slider2'=> [
                'super_key' => 'image4 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage2.png' ,
                    'ar' => 'SiteFiles/sliderImage2.png' ,
                ],
            ] ,
            'image5_slider2'=> [
                'super_key' => 'image5 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage2.png' ,
                    'ar' => 'SiteFiles/sliderImage2.png' ,
                ],
            ] ,
            'image6_slider2'=> [
                'super_key' => 'image6 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage3.png' ,
                    'ar' => 'SiteFiles/sliderImage3.png' ,
                ],
            ] ,
            'image7_slider2'=> [
                'super_key' => 'image7 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage3.png' ,
                    'ar' => 'SiteFiles/sliderImage3.png' ,
                ],
            ] ,
            'image8_slider2'=> [
                'super_key' => 'image8 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage3.png' ,
                    'ar' => 'SiteFiles/sliderImage3.png' ,
                ],
            ] ,
            'image9_slider2'=> [
                'super_key' => 'image9 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage3.png' ,
                    'ar' => 'SiteFiles/sliderImage3.png' ,
                ],
            ] ,
            'image10_slider2'=> [
                'super_key' => 'image10 for slider2' ,
                'value' => [
                    'en' => 'SiteFiles/sliderImage3.png' ,
                    'ar' => 'SiteFiles/sliderImage3.png' ,
                ],
            ] ,
            'email' => [
                'super_key' => 'header' ,
                'value' => [
                    'en' => 'IdeaCodeReality.ICR@gmail.com' ,
                    'ar' => 'IdeaCodeReality.ICR@gmail.com' ,
                ],
            ] ,
            'phone1' => [
                'super_key' => 'header' ,
                'value' => [
                    'en' => '+963 963 155 238' ,
                    'ar' => '+963 963 155 238' ,
                ],
            ] ,
            'phone2' => [
                'super_key' => 'header' ,
                'value' => [
                    'en' => '+963 963 155 238' ,
                    'ar' => '+963 963 155 238' ,
                ],
            ] ,
            'instagram' => [
                'super_key' => 'header' ,
                'value' => [
                    'ar' => 'instagram.insta',
                    'en' => 'instagram.insta'
                ]
            ] ,
            'facebook' => [
                'super_key' => 'header' ,
                'value' => [
                    'ar' => 'facebook.face',
                    'en' => 'facebook.face'
                ]
            ] ,
            'twitter' => [
                'super_key' => 'header' ,
                'value' => [
                    'ar' => 'twitter.twit',
                    'en' => 'twitter.twit'
                ]
            ] ,
            'pinterest' => [
                'super_key' => 'header' ,
                'value' => [
                    'ar' => 'pinterest.pin',
                    'en' => 'pinterest.pin'
                ]
            ] ,
            'whatsapp' => [
                'super_key' => 'header' ,
                'value' => [
                    'ar' => 'whatsapp.whats',
                    'en' => 'whatsapp.whats'
                ]
            ] ,
            'about' => [
                'super_key' => 'footer' ,
                'value' => [
                    'ar' => 'هم روّاد المواقع الإلكترونية المتخصصة في مجال الإعلانات المبوبة والتي تمكّن المستخدمين سواء كانوا بائعين أم مشترين من بيع وشراء مختلف السلع والمنتجات',
                    'en' => 'They are the pioneers of websites specialized in the field of classified ads, which enable users, whether sellers or buyers, to buy and sell various goods and products.'
                ]
            ] ,
            'hero_description' => [
                'super_key' => 'about' ,
                'value' => [
                    'ar' => 'في بعض الأحيان ، يكون من الصعب معرفة ما إذا كُنتَ تحصل بالفعل على صفقة جيدة لشيء ما. هناك عدد كبير جدًا من مواقع الويب للتحقق منها ؛ لن تعرف أبدًا إذا كُنت قد فوت سعرًا أقل. إذا كنت تبحث عن أفضل الصفقات على الإلكترونيات ، فإن TechBargains هو الجواب. حيث ينتقل من خلال مئات المتاجر الإلكترونية ، ومواقع الخصومات ، وتجار التجزئة من الأطراف الثالثة ليقدم لك بعضًا من أرخص الصفقات التي ستجدها. قسم الحوسبة شامل. يُقدم موقع الويب أجهزة الكمبيوتر المحمولة والمكتبية والطابعات وأجهزة التوجيه والمزيد. ستجد أيضًا الأجهزة القابلة للارتداء وأجهزة المنزل الذكي وأجهزة الألعاب وأجهزة الصوت. بعيدًا عن التكنولوجيا ، يُقدم موقع الويب بعض الفئات الأصغر الأخرى مثل المنزل والحديقة ، لكن الصفقات ليست ملفتة للنظر. للحصول على صفقات أفضل ، اشترك في النشرة الإخبارية لـ TechBargains.الإلكترونية ، ومواقع الخصومات ، وتجار التجزئة من الأطراف الثالثة ليقدم لك بعضًا من أرخص الصفقات التي ستجدها. قسم الحوسبة شامل. يُقدم موقع الويب أجهزة الكمبيوتر المحمولة والمكتبية والطابعات وأجهزة التوجيه والمزيد. ستجد أيضًا بعيدًا عن التكنولوجيا ، يُقدم موقع الويب بعض الفئات الأصغر الأخرى مثل المنزل والحديقة ، لكن الصفقات ليست ملفتة للنظر. للحصول على صفقات أفضل ، اشترك في النشرة الإخبارية لـ TechBargains',
                    'en' => 'Sometimes, it is hard to know if you are actually getting a good deal on something. There are too many websites to check out; You will never know if you have missed out on a lower price. If you are looking for the best deals on electronics, TechBargains is the answer. It combs through hundreds of online stores, discount sites, and third-party retailers to bring you some of the cheapest deals you will find. Comprehensive computing department. The website offers laptops, desktops, printers, routers, and more. You will also find wearables, smart home devices, gaming consoles, and audio devices. Beyond technology, the website does offer some other smaller categories like home and garden, but the deals are not as eye-catching. For even better deals, sign up for TechBargains newsletter, discount sites, and third-party retailers to offer you some of the cheapest deals you will find. Comprehensive computing department. The website offers laptops, desktops, printers, routers, and more. You will also find beyond technology, the website offers some other smaller categories like home and garden, but the deals are not as eye-catching. For even better deals, sign up for the TechBargains newsletter'
                ]
            ] ,
            'search' => [
                'super_key' => 'header' ,
                'value' => [
                    'ar' => 'مهما كان نوع بحثك أو عرضك أو طلبك فيما يخص نطاق الأجهزة والإلكترونيات اللازمة للمنزل والمكتب؛ هُنا ستجد الفرصة الأمثل للقيام بالتسويق الإلكتروني',
                    'en' => 'Whatever your search, offer or request regarding the range of appliances and electronics needed for home and office; Here you will find the best opportunity to do electronic marketing'
                ]
            ] ,
            'contact-us' => [
                'super_key' => 'header' ,
                'value' => [
                    'ar' => 'لا تتردد في التواصل معنا و الاستفسار عن ما تريد نحن هنا لخدمتك',
                    'en' => 'Do not hesitate to contact us and inquire about what you want. We are here to serve you'
                ]
            ] ,
            'rights' => [
                'super_key' => 'footer' ,
                'value' => [
                    'ar' => 'icr جميع الحقوق محفوظة لموقع بهج الضياء ©2023 تم التطوير بواسطة فريق ',
                    'en' => 'All rights reserved to Bahj Al-Diaa website ©2023 Developed by icr team'
                ]
            ] ,
        ];
        foreach ($siteInfo as $key => $info) {
            Info::create([
                    'super_key' => $info['super_key'],
                    'key' => $info['key'],
                    'value' => $info['value']
                ]);
        }
        
    }
}
