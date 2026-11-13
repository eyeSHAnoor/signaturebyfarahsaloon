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
        // 1. Comprehensive Catalog from Rate List
        // Format: [Name, Description, Duration (mins), Price, Featured]
        $catalog = [
            'Facial' => [
                ['Herbal Deep Cleansing', 'A refreshing deep cleanse using natural herbal extracts.', 45, 1000, false],
                ['Janseen Whitening Deep Cleansing', 'Deep cleansing treatment to brighten and clarify the skin.', 60, 1500, false],
                ['Anti Acne Cleansing', 'Targeted cleansing to soothe and treat acne-prone skin.', 45, 1500, false],
                ['Signature Brightening Facial', 'Our signature facial designed to illuminate and even out skin tone.', 60, 5000, true],
                ['Detox Facial', 'A purifying facial to draw out impurities and refresh the skin.', 60, 3500, false],
                ['Deluxe Gold Facial', 'A luxurious gold-infused facial for ultimate radiance.', 75, 3500, false],
                ['Diamond Facial', 'Advanced facial treatment for deep hydration and glow.', 75, 4000, false],
                ['Acne Reduction Facial', 'Specialized treatment to calm breakouts and reduce redness.', 60, 4500, false],
                ['Janseen 3D Facial', 'Advanced 3D facial for lifting and firming.', 75, 5000, false],
                ['Janseen Face Lifting with LED', 'Facial lifting treatment enhanced with LED light therapy.', 75, 6000, true],
                ['Collagen Facial', 'Replenishing collagen treatment for youthful, plump skin.', 60, 5000, false],
                ['Charcoal Glow Facial', 'Detoxifying charcoal treatment for a clear, glowing complexion.', 60, 4500, false],
                ['Express Vitamin E Facial', 'A quick, nourishing boost of Vitamin E.', 30, 3500, false],
                ['Herbal Glowing Facial', 'Natural herbal treatment for a radiant finish.', 45, 2500, false],
                ['Hydra Facial with 3D Light', 'Deep hydration combined with 3D light therapy for skin rejuvenation.', 80, 8000, true],
            ],
            'Skin Treatment' => [
                ['Open Pores Treatment', 'Treatment designed to minimize the appearance of open pores.', 45, 3000, false],
                ['Anti Acne Treatment', 'Intensive treatment to clear and prevent acne.', 60, 3000, false],
                ['Face Lifting Treatment', 'Non-surgical lifting treatment for a more youthful appearance.', 60, 3500, false],
                ['High Frequency Treatment', 'High frequency therapy to promote healing and clear skin.', 45, 3000, false],
                ['Lontophoresis Treatment', 'Advanced treatment for deep product penetration.', 45, 3500, false],
                ['Collagen Thread Filler Treatment', 'Per thread treatment for targeted volume and lifting.', 30, 8000, true], // Base price per thread
            ],
            'Body Massage' => [
                ['Face Massage', 'Relaxing facial massage.', 15, 500, false],
                ['Full Legs Massage', 'Soothing massage for full legs.', 30, 1000, false],
                ['Feet Massage', 'Reflexology and relaxation for tired feet.', 20, 500, false],
                ['Hand Massage', 'Hydrating and relaxing hand massage.', 15, 500, false],
                ['Shoulder Massage', 'Tension-relieving shoulder massage.', 15, 1000, false],
                ['Head Massage', 'Stress-relieving head and scalp massage.', 20, 500, false],
                ['Back Massage', 'Relaxing back massage.', 30, 500, false],
                ['Full Body Massage', 'Full body relaxation massage.', 60, 4000, true],
            ],
            'Hair Treatment' => [
                ['Root Touching 1"inch (Natural)', 'Touch up for natural hair color roots.', 30, 2500, false],
                ['Root Touching 1"inch (Fashion)', 'Touch up for fashion color roots.', 30, 3000, false],
                ['Color Apply', 'Application of hair color.', 45, 1500, false],
                ['Natural Hair Dye', 'Natural hair coloring (according to length).', 45, 10000, false],
                ['Fashion Hair Dye', 'Fashion hair coloring (according to length).', 60, 10000, false],
                ['Hair Glossing with Color', 'Gloss treatment to add shine and tone (according to length).', 45, 5000, false],
                ['Cap Streaks', 'Highlighting technique using a cap (according to length).', 90, 8000, false],
                ['Foil Highlights', 'Traditional foil highlighting (according to length).', 120, 15000, true],
                ['Crown Area Highlights', 'Highlights focused on the crown area.', 60, 5000, false],
                ['Chunks (Each Layer)', 'Funky color chunks (each layer).', 60, 2000, false],
                ['Ombre Hair Dye', 'Graduated hair color (according to length).', 120, 9000, true],
                ['Balayage Hair Dye', 'Hand-painted, natural-looking highlights (according to length).', 180, 10000, true],
            ],
            'Haircuts & Styling' => [
                ['Child Hair Cut', 'Haircut for children aged 1-5 years.', 20, 800, false],
                ['Signature Hair Cut', 'Our signature precision haircut.', 45, 2000, true],
                ['Signature Hair Cut with Wash', 'Signature haircut including a relaxing wash.', 50, 2500, true],
                ['Hair Trimming', 'Basic trim to maintain hair health.', 20, 800, false],
                ['Split End Trimming', 'Trimming to remove damaged split ends.', 30, 2500, false],
                ['Blow Dry Setting', 'Professional blow dry (according to length).', 30, 2000, false],
                ['Simple Hair Straight or Curls', 'Simple straightening or curling (according to length).', 30, 2000, false],
                ['Party Hair Styling with Extension Fixing', 'Party-ready styling with extensions (according to length).', 60, 2500, true],
            ],
            'Hair Spa' => [
                ['Hair Wash (Shampoo+Conditioner)', 'Basic hair wash and conditioning.', 15, 500, false],
                ['Herbal Hair Wash', 'Hair wash using herbal products.', 20, 800, false],
                ['Hot Oiling Head Massage', 'Warm oil scalp massage.', 20, 1000, false],
                ['Hair Wash with Mask', 'Deep conditioning hair mask treatment.', 30, 1000, false],
                ['Loreal Hair Spa Treatment', 'Professional Loreal hair spa.', 45, 3000, true],
                ['Keratin Hair Treatment', 'Smoothing keratin treatment.', 120, 4000, false], // Base price
                ['Protein Hair Treatment', 'Strengthening protein treatment.', 60, 3000, false],
                ['Hair Detox Treatment', 'Cleansing treatment to remove buildup.', 45, 3500, false],
                ['Hair Glossing Treatment', 'Treatment to add intense shine.', 45, 4500, false],
            ],
            'Threading' => [
                ['Eye Brow Threading', 'Eyebrow shaping.', 10, 200, true],
                ['Upper Lips', 'Upper lip threading.', 5, 50, false],
                ['Chin', 'Chin threading.', 5, 50, false],
                ['Forehead', 'Forehead threading.', 5, 100, false],
                ['Full Face Threading', 'Full face threading.', 20, 1000, false],
                ['Chin+Neck', 'Chin and neck threading.', 10, 500, false],
            ],
            'Honey Wax' => [
                ['Half Arms Wax', 'Honey wax for half arms.', 15, 800, false],
                ['Full Arms Wax', 'Honey wax for full arms.', 30, 1000, false],
                ['Half Legs Wax', 'Honey wax for half legs.', 20, 1000, false],
                ['Full Legs Wax', 'Honey wax for full legs.', 40, 1800, false],
                ['Under Arms Wax', 'Honey wax for underarms.', 10, 400, false],
                ['Upper Legs Wax', 'Honey wax for upper legs.', 20, 1500, false],
                ['Back Neck', 'Honey wax for back neck.', 10, 500, false],
                ['Back Full Wax', 'Honey wax for full back.', 30, 800, false],
                ['Belly Wax', 'Honey wax for belly.', 15, 6000, false], // Note: High price as per PDF
                ['Full Body Wax', 'Full body honey wax.', 90, 6000, true],
            ],
            'Fruit Wax' => [
                ['Eye Brow Wax', 'Fruit wax for eyebrows.', 10, 300, false],
                ['Upper Lips Wax', 'Fruit wax for upper lip.', 5, 100, false],
                ['Chin Wax', 'Fruit wax for chin.', 5, 200, false],
                ['Forehead Wax', 'Fruit wax for forehead.', 5, 200, false],
                ['Nose Wax', 'Fruit wax for nose.', 5, 300, false],
                ['Face Side Wax', 'Fruit wax for face sides.', 10, 500, false],
                ['Half Face Wax', 'Fruit wax for half face.', 15, 800, false],
                ['Full Face Wax', 'Fruit wax for full face.', 20, 1200, false],
                ['Full Face Wax with Mask', 'Fruit wax and face mask.', 30, 1500, false],
                ['Chin+Neck', 'Fruit wax for chin and neck.', 10, 600, false],
                ['Half Arms Wax', 'Fruit wax for half arms.', 15, 1000, false],
                ['Full Arms Wax', 'Fruit wax for full arms.', 30, 2000, false],
                ['Half Legs Wax', 'Fruit wax for half legs.', 20, 1500, false],
                ['Full Legs Wax', 'Fruit wax for full legs.', 40, 1800, false],
                ['Under Arms Wax', 'Fruit wax for underarms.', 10, 500, false],
                ['Under Arms Glowing Polisher + Exfoliation', 'De-tanning treatment for underarms.', 20, 1000, true],
                ['Full Body Bleach', 'Full body bleaching treatment.', 60, 3500, false],
                ['Full Body Glowing Polisher', 'Full body polishing for glow.', 60, 5000, true],
                ['Full Body Exfoliation', 'Full body scrub and exfoliation.', 60, 4500, false],
            ],
            'Polisher' => [
                ['Simple Face Polisher', 'Basic face polishing.', 20, 400, false],
                ['Face Glow Polisher', 'Polisher for a glowing complexion.', 25, 500, false],
                ['Signature Brightening Skin Polish', 'Our signature brightening treatment.', 30, 2000, true],
                ['Herbal Skin Polish', 'Natural herbal polishing.', 25, 1500, false],
                ['Janseen Glowing Skin Polish', 'Advanced Janseen polishing treatment.', 30, 2500, false],
                ['Hand Polisher', 'Polishing treatment for hands.', 15, 350, false],
                ['Foot Polisher', 'Polishing treatment for feet.', 15, 350, false],
                ['Half Arms Polisher', 'Polisher for half arms.', 15, 500, false],
                ['Full Arms Polisher', 'Polisher for full arms.', 25, 1000, false],
                ['Half Legs Polisher', 'Polisher for half legs.', 20, 800, false],
                ['Full Legs Polisher', 'Polisher for full legs.', 30, 1800, false],
                ['Under Arms Polisher', 'Polisher for underarms.', 10, 500, false],
            ],
            'Signature Makeover' => [
                ['Signature Glowing Party Makeover', 'Radiant makeup look for parties and events.', 60, 5000, true],
                ['Signature Glossy Engagement Makeover', 'Glossy, elegant makeup for engagements.', 90, 15000, true],
                ['Signature Glossy Nikha Makeover', 'Radiant makeup for Nikha ceremonies.', 90, 20000, true],
                ['Signature Mayo Makeover', 'Elegant makeup for Mayo events.', 60, 8000, false],
                ['Signature Mehandi Makeover', 'Festive makeup for Mehandi functions.', 90, 12000, false],
                ['Signature Bridal Makeover', 'Our premium, comprehensive bridal makeup.', 180, 25000, true],
                ['Signature Walima Makeover', 'Elegant makeup for Walima receptions.', 120, 25000, true],
                ['Signature Model Makeover', 'High-fashion makeup for models and shoots.', 60, 8000, false],
                ['Signature Nude Makeover', 'Natural, minimalistic "no-makeup" makeup look.', 60, 5000, false],
                ['Jewellery Setting', 'Assistance with setting jewellery.', 15, 1500, false],
                ['Duppatta Setting', 'Professional draping and setting of dupattas.', 20, 3000, false],
                ['Saree Setting', 'Professional saree draping.', 30, 1000, false],
            ],
            'Nails' => [
                ['Simple Nail Paint', 'Application of basic nail polish.', 15, 200, false],
                ['French Nail Paint', 'Classic French manicure style.', 25, 800, false],
                ['French Nail Extension with Nail Paint [1 Day Extension]', 'French nail extensions.', 60, 1000, false],
                ['15 Days Nail Extension with Nail Paint', 'Temporary nail extensions.', 60, 3000, false],
                ['15 Days Nail Extension with Gel Nail Paint', 'Temporary nail extensions with gel polish.', 75, 3000, false],
                ['Gel Nail Paint', 'Long-lasting gel polish application.', 45, 1500, true],
                ['Acrylic Remove with Nail Buffing', 'Safe removal of acrylics and buffing.', 30, 3000, false],
                ['Gel Nail Paint Remove', 'Safe removal of gel polish.', 20, 3000, false],
            ],
            'Mehandi' => [
                ['Per Side Mehandi', 'Henna application on one side.', 15, 500, false],
                ['Hand to Elbow Mehandi', 'Henna up to the elbow.', 30, 1500, false],
                ['Bridal Mehandi (Hand+Feet) Normal', 'Intricate bridal henna for hands and feet.', 120, 5000, true],
                ['Bridal Mehandi (Hand+Feet) Heavy', 'Detailed, heavy bridal henna for hands and feet.', 180, 5000, true], // Assuming 5000 based on normal
            ],
            'Eye Brow' => [
                ['Eye Brow Tint Color', 'Eyebrow tinting.', 15, 1500, false],
                ['Eye Brow Tint & Lash Tint Color', 'Tinting for both brows and lashes.', 25, 3000, false],
                ['Eye Lash Extension', 'Application of eyelash extensions.', 60, 10000, true],
                ['Eye Lash Perming', 'Perming treatment for lashes.', 45, 5000, false],
                ['Eye Lash Extension Re-Touching', 'Maintenance for lash extensions.', 30, 2500, false],
            ],
            'Bridal Packages' => [
                ['Bridal Package A', 'Eye Brow + Upper lips, Janseen glowing skin polish, Signature Bridal makeup, 3D lashes, 1Day nail extension, Hair styling + Dupatta setting.', 180, 25000, true],
                ['Bridal Package B', 'Signature Brightening Facial, Signature Delux mani&pedi, 1Day nail extension, 3D lashes, Signature Glossy Bridal Makeover.', 180, 30000, true],
                ['Bridal Package C', 'HYDRA Facial with Led treatment, GOLD mani&pedi, Full arms +Full legs+under arms wax, E.B+Upper lips, The Royal Bridal makeover, 3Dlashes+ 1Day nail extension.', 240, 35000, true],
                ['Bridal Package D', 'Janseen 3D Facial, VLCC mani&pedi, Full body wax, E.B+Upper lips, Signature MEHANDI makeover, 3D lashes+1Day nail extension.', 240, 45000, true],
                ['Bridal Package E', 'HYDRA Facial with led treatment, Signature Delux meni & pedi, Signature Brightening Facial, Full body polisher, Full body wax, Bridal mehandi, Signature Bridal makeover, 3D lashes +ONE day nail extention.', 300, 50000, true],
                ['Bridal Package F', 'SIGNATURE MEHANDI MAKEOVER, SIGNATURE BRIDAL MAKEOVER, SIGNATURE WALIMA MAKEOVER, SIGNATURE PARTY MAKEOVER, WITH ONE DAY NAIL+3D LASHES.', 300, 50000, true],
            ],
            'Waxing (Half Legs)' => [
                ['Full legs wax (Half Legs section)', 'Waxing for full legs.', 40, 2500, false],
                ['Under arms wax (Half Legs section)', 'Underarm waxing.', 10, 800, false],
                ['Upper legs wax', 'Waxing for upper legs.', 20, 2000, false],
                ['Back neck (Half Legs section)', 'Waxing for back neck.', 10, 800, false],
                ['Back full wax (Half Legs section)', 'Waxing for full back.', 30, 1000, false],
                ['Belly wax (Half Legs section)', 'Waxing for belly.', 15, 800, false],
                ['Full body wax (Half Legs section)', 'Full body waxing.', 90, 8000, false],
            ]
        ];

        foreach ($catalog as $categoryName => $services) {
            $category = ServiceCategory::query()->updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'description' => 'Professional treatments tailored to you.'],
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
                        'image_url' => $this->serviceImage($categoryName, $name),
                        'featured' => $featured,
                        'active' => true,
                    ],
                );
            }
        }

        // 2. Stylists
        foreach ([
            ['Farah', 'Founder & Lead Makeup Artist', 'Specializing in bridal transformations and signature glowing looks.'],
            ['Amelia Rose', 'Creative Director & Colourist', 'Known for natural-looking colour and cuts that feel like you.'],
            ['Sofia Bennett', 'Skin & Beauty Specialist', 'A thoughtful approach to skin, brows and restorative beauty rituals.'],
            ['Maya Ellis', 'Senior Stylist', 'Specialising in modern bridal styling and effortless occasion hair.'],
        ] as [$name, $title, $bio]) {
            Stylist::query()->updateOrCreate(
                ['name' => $name],
                ['title' => $title, 'bio' => $bio, 'image_url' => $this->portrait($name), 'active' => true],
            );
        }

        // 3. Gallery
        SalonGalleryItem::query()->where('title', 'The signature cut')->update([
            'before_image_url' => 'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=900&q=85',
            'after_image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=900&q=85',
        ]);

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

        // 4. Testimonials
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

        // 5. Settings
        foreach ([
            'phone' => '03336601912',
            'email' => 'Signaturebyfarah@gmail.com',
            'address' => 'Signature by Farah, Shop number 04 Main Road Baker Chowk Opposite UBL Bank Peoples Colony No 2 District Faisalabad',
            'instagram' => 'https://instagram.com/',
            'home_heading' => 'Feel like yourself, only softer.',
            'home_description' => 'Thoughtful hair, skin and beauty rituals in a calm little corner of the city. Come as you are. Leave feeling quietly radiant.',
            'offer_title' => 'A little something extra',
            'offer_description' => 'New to Signature by Farah? Mention this page when you book and we’ll include a complimentary consultation with your first visit.',
        ] as $key => $value) {
            SalonSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Assign relevant images based on category and service name
     */
    private function serviceImage(string $category, string $service): string
    {
        // Unsplash & Pexels image IDs mapped to categories
        $images = [
            'Makeover' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=1000&q=85',
            'Bridal' => 'https://images.pexels.com/photos/3460676/pexels-photo-3460676.jpeg?auto=compress&cs=tinysrgb&w=1000',
            'Facial' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&w=1000&q=85',
            'Skin' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=1000&q=85',
            'Massage' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=1000&q=85',
            'Hair Treatment' => 'https://images.unsplash.com/photo-1560869713-7d0a29430803?auto=format&fit=crop&w=1000&q=85',
            'Haircuts' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=85',
            'Hair Spa' => 'https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=1000&q=85',
            'Threading' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=1000&q=85',
            'Wax' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=1000&q=85',
            'Polisher' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=1000&q=85',
            'Nails' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=1000&q=85',
            'Mehandi' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?auto=format&fit=crop&w=1000&q=85',
            'Eye Brow' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=1000&q=85',
        ];

        // 1. Check exact matches for specific treatments first
        if (str_contains($service, 'Bridal Package') || str_contains($category, 'Bridal')) {
            return $images['Bridal'];
        }
        if (str_contains($service, 'Makeover') || str_contains($category, 'Makeover')) {
            return $images['Makeover'];
        }

        // 2. Match by Category
        foreach ($images as $key => $url) {
            if (str_contains($category, $key)) {
                return $url;
            }
        }

        // 3. Default Fallback
        return 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1000&q=85';
    }

    /**
     * Get stylist portraits
     */
    private function portrait(string $name): string
    {
        $photos = [
            'Farah' => 'photo-1580618672591-eb180b1a973f',
            'Amelia Rose' => 'photo-1534528741775-53994a69daeb',
            'Sofia Bennett' => 'photo-1524504388940-b1c1722653e1',
            'Maya Ellis' => 'photo-1531123897727-8f129e1688ce',
        ];

        $photo = $photos[$name] ?? 'photo-1580618672591-eb180b1a973f';

        return 'https://images.unsplash.com/'.$photo.'?auto=format&fit=crop&w=900&q=85';
    }
}