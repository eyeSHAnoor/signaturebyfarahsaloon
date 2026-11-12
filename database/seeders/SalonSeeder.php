<?php

namespace Database\Seeders;

use App\Models\SalonGalleryItem;
use App\Models\SalonSetting;
use App\Models\SalonTestimonial;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Stylist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalonSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Haircuts & Styling' => [
                ['Signature Cut & Finish', 'A considered cut, tailored consultation and polished blow-dry.', 60, 5500, true],
                ['Blowout & Style', 'A luminous, long-lasting finish for any occasion.', 45, 3800, true],
            ],
            'Hair Colour' => [
                ['Balayage & Gloss', 'Dimensional, hand-painted colour finished with a nourishing gloss.', 180, 19500, true],
                ['Root Refresh', 'Seamless root coverage and a fresh, glossy finish.', 90, 8500, false],
            ],
            'Hair Treatments' => [
                ['Restorative Hair Ritual', 'A bespoke scalp and bond-repair treatment with a relaxing massage.', 60, 7200, false],
            ],
            'Facial & Skincare' => [
                ['Signature Glow Facial', 'A skin consultation and radiance-boosting facial tailored to you.', 75, 11000, true],
            ],
            'Makeup' => [
                ['Soft Glam Makeup', 'Effortless, camera-ready makeup with a personalised finish.', 60, 9500, false],
            ],
            'Nails' => [
                ['Manicure & Pedicure', 'A restorative nail and cuticle treatment with your choice of finish.', 75, 6800, false],
            ],
            'Bridal & Special Occasion' => [
                ['Bridal Beauty Preview', 'A relaxed preview for your wedding-day hair and makeup look.', 120, 16500, true],
            ],
            'Waxing & Beauty' => [
                ['Brow Shape & Tint', 'Expert brow shaping and tinting to frame your features.', 30, 3500, false],
            ],
        ];

        foreach ($catalog as $categoryName => $services) {
            $category = ServiceCategory::query()->updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'description' => 'Thoughtful treatments, tailored to you.'],
            );

            foreach ($services as [$name, $description, $duration, $price, $featured]) {
                Service::query()->updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'service_category_id' => $category->id,
                        'name' => $name,
                        'description' => $description,
                        'duration_minutes' => $duration,
                        'price' => $price,
                        'image_url' => $this->serviceImage($name),
                        'featured' => $featured,
                        'active' => true,
                    ],
                );
            }
        }

        foreach ([
            ['Amelia Rose', 'Creative Director & Colourist', 'Known for natural-looking colour and cuts that feel like you.'],
            ['Sofia Bennett', 'Skin & Beauty Specialist', 'A thoughtful approach to skin, brows and restorative beauty rituals.'],
            ['Maya Ellis', 'Senior Stylist', 'Specialising in modern bridal styling and effortless occasion hair.'],
        ] as [$name, $title, $bio]) {
            Stylist::query()->updateOrCreate(
                ['name' => $name],
                ['title' => $title, 'bio' => $bio, 'image_url' => $this->portrait($name), 'active' => true],
            );
        }
        SalonGalleryItem::query()->where('title', 'The signature cut')->update([
            'before_image_url' => 'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=900&q=85',
            'after_image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=900&q=85',
        ]);

        foreach ([
            ['Olivia Hart', 'Colour client', 'I left feeling like the most polished version of myself. The team really listened, and the colour is beautiful.', 5],
            ['Amara Lewis', 'Regular guest', 'The calmest hour of my month. Every detail feels considered, from the welcome to the finished look.', 5],
            ['Isabel Morgan', 'Bridal client', 'My wedding hair felt effortless and completely me. I could not have asked for a more lovely experience.', 5],
        ] as [$name, $title, $quote, $rating]) {
            SalonTestimonial::query()->updateOrCreate(
                ['customer_name' => $name],
                ['customer_title' => $title, 'quote' => $quote, 'rating' => $rating, 'active' => true],
            );
        }

        foreach ([
            ['The signature cut', 'Hair', 'photo-1522337360788-8b13dee7a37e'],
            ['A little golden hour', 'Colour', 'photo-1524504388940-b1c1722653e1'],
            ['Everyday radiance', 'Skin', 'photo-1487412720507-e7ab37603c6f'],
            ['The finishing touch', 'Beauty', 'photo-1515886657613-9f3515b0c78f'],
        ] as [$title, $category, $photo]) {
            SalonGalleryItem::query()->updateOrCreate(
                ['title' => $title],
                [
                    'category' => $category,
                    'image_url' => "https://images.unsplash.com/{$photo}?auto=format&fit=crop&w=1000&q=85",
                    'alt_text' => $title.' at Signature by Farah',
                    'active' => true,
                ],
            );
        }

        foreach ([
            'phone' => '03336601912',
            'email' => 'Signaturebyfarah@gmail.com',
            'address' => 'Signature by Farah, 887/B, Peoples Colony No. 1, near Al-Fateh Sports Complex, Chen One Road, Faisalabad, Pakistan',
            'instagram' => 'https://instagram.com/',
            'home_heading' => 'Feel like yourself, only softer.',
            'home_description' => 'Thoughtful hair, skin and beauty rituals in a calm little corner of the city. Come as you are. Leave feeling quietly radiant.',
            'offer_title' => 'A little something extra',
            'offer_description' => 'New to Signature by Farah? Mention this page when you book and we’ll include a complimentary consultation with your first visit.',
        ] as $key => $value) {
            SalonSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    private function serviceImage(string $service): string
    {
        if (str_contains($service, 'Bridal')) {
            return 'https://images.pexels.com/photos/3460676/pexels-photo-3460676.jpeg?auto=compress&cs=tinysrgb&w=1000';
        }

        $photo = str_contains($service, 'Facial') ? 'photo-1512290923902-8a9f81dc236c' : 'photo-1522337360788-8b13dee7a37e';

        return "https://images.unsplash.com/{$photo}?auto=format&fit=crop&w=1000&q=85";
    }

    private function portrait(string $name): string
    {
        $photos = [
            'Amelia Rose' => 'photo-1534528741775-53994a69daeb',
            'Sofia Bennett' => 'photo-1524504388940-b1c1722653e1',
            'Maya Ellis' => 'photo-1531123897727-8f129e1688ce',
        ];

        return 'https://images.unsplash.com/'.$photos[$name].'?auto=format&fit=crop&w=900&q=85';
    }
}
