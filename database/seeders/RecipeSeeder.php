<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create(['email' => 'admin@savor.ph']);
        }

        $tags = Tag::pluck('id', 'slug');
        $ingredients = Ingredient::pluck('id', 'name');

        $recipes = [
            // === CHICKEN ===
            [
                'title' => 'Classic Chicken Adobo',
                'slug' => 'classic-chicken-adobo',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 10,
                'cook_time' => 40,
                'servings' => 4,
                'description' => 'The quintessential Filipino dish — tender chicken simmered in soy sauce, vinegar, garlic, and bay leaves.',
                'instructions' => "1. Marinate chicken thighs in soy sauce, crushed garlic, and bay leaves for 30 minutes.\n2. Heat oil in a pot. Brown the chicken pieces on all sides. Remove and set aside.\n3. In the same pot, sauté remaining garlic until fragrant.\n4. Pour in the marinade and add vinegar. Do not stir — let it boil for 5 minutes.\n5. Add browned chicken, 1 cup water, and ground pepper. Simmer for 30 minutes until tender.\n6. Season with salt to taste. Serve with steamed rice.",
                'is_featured' => true,
                'tags' => ['filipino-classics', 'budget-meals', 'family-size'],
                'ingredients' => [
                    ['name' => 'Chicken Thigh', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'bone-in'],
                    ['name' => 'Garlic', 'quantity' => 6, 'unit' => 'cloves', 'notes' => 'crushed'],
                    ['name' => 'Soy Sauce (Toyo)', 'quantity' => 6, 'unit' => 'tbsp'],
                    ['name' => 'Vinegar (Suka)', 'quantity' => 4, 'unit' => 'tbsp'],
                    ['name' => 'Bay Leaf (Laurel)', 'quantity' => 3, 'unit' => 'pcs'],
                    ['name' => 'Ground Black Pepper', 'quantity' => 1, 'unit' => 'tsp'],
                    ['name' => 'Cooking Oil', 'quantity' => 2, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Chicken Tinola',
                'slug' => 'chicken-tinola',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 15,
                'cook_time' => 35,
                'servings' => 4,
                'description' => 'A comforting ginger-garlic chicken soup with green papaya and malunggay leaves.',
                'instructions' => "1. Heat oil, sauté ginger, garlic, and onion until aromatic.\n2. Add chicken pieces and cook until lightly browned.\n3. Pour in water or rice wash. Bring to a boil, then simmer for 20 minutes.\n4. Add sayote (chayote) wedges. Cook for 10 more minutes.\n5. Add dahon ng sili or malunggay leaves. Simmer 2 minutes.\n6. Season with patis (fish sauce) and pepper. Serve hot.",
                'is_featured' => true,
                'tags' => ['filipino-classics', '30-minute-meals', 'family-size'],
                'ingredients' => [
                    ['name' => 'Chicken Thigh', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'cut into serving pieces'],
                    ['name' => 'Ginger', 'quantity' => 1, 'unit' => 'thumb-sized', 'notes' => 'julienned'],
                    ['name' => 'Garlic', 'quantity' => 4, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'sliced'],
                    ['name' => 'Sayote (Chayote)', 'quantity' => 2, 'unit' => 'pcs', 'notes' => 'wedged'],
                    ['name' => 'Fish Sauce (Patis)', 'quantity' => 3, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Chicken Curry',
                'slug' => 'chicken-curry',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 15,
                'cook_time' => 40,
                'servings' => 4,
                'description' => 'Creamy Filipino-style chicken curry with coconut milk and vegetables.',
                'instructions' => "1. Season chicken with salt and pepper. Brown in oil, set aside.\n2. Sauté garlic, onion, and ginger.\n3. Add curry powder, stir for 1 minute until fragrant.\n4. Pour coconut milk and 1 cup water. Bring to a boil.\n5. Return chicken to pot. Simmer for 20 minutes.\n6. Add kalabasa and sitaw. Cook 10 more minutes.\n7. Season with patis and pepper. Serve with rice.",
                'is_featured' => false,
                'tags' => ['family-size', 'filipino-classics'],
                'ingredients' => [
                    ['name' => 'Chicken Thigh', 'quantity' => 1, 'unit' => 'kg'],
                    ['name' => 'Garlic', 'quantity' => 4, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'chopped'],
                    ['name' => 'Ginger', 'quantity' => 1, 'unit' => 'thumb-sized', 'notes' => 'julienned'],
                    ['name' => 'Coconut (Gata)', 'quantity' => 2, 'unit' => 'pcs'],
                    ['name' => 'Kalabasa (Squash)', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'cubed'],
                    ['name' => 'Sitaw (String Beans)', 'quantity' => 1, 'unit' => 'bundle', 'notes' => 'cut 2 inches'],
                    ['name' => 'Fish Sauce (Patis)', 'quantity' => 2, 'unit' => 'tbsp'],
                    ['name' => 'Cooking Oil', 'quantity' => 2, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Chicken Afritada',
                'slug' => 'chicken-afritada',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 15,
                'cook_time' => 35,
                'servings' => 4,
                'description' => 'A hearty tomato-based chicken stew with potatoes, carrots, and bell peppers.',
                'instructions' => "1. Season chicken with salt and pepper. Brown in oil. Set aside.\n2. Sauté garlic and onion until soft.\n3. Add tomato sauce and 1 cup water. Bring to a boil.\n4. Return chicken, add potatoes and carrots. Simmer 20 minutes.\n5. Add bell peppers. Cook 5 more minutes.\n6. Season with salt and pepper. Serve with rice.",
                'is_featured' => false,
                'tags' => ['family-size', 'budget-meals'],
                'ingredients' => [
                    ['name' => 'Chicken Thigh', 'quantity' => 1, 'unit' => 'kg'],
                    ['name' => 'Garlic', 'quantity' => 3, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'chopped'],
                    ['name' => 'Tomato', 'quantity' => 3, 'unit' => 'pcs', 'notes' => 'chopped'],
                    ['name' => 'Potato', 'quantity' => 2, 'unit' => 'pcs', 'notes' => 'cubed'],
                    ['name' => 'Carrot', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'cubed'],
                    ['name' => 'Green Bell Pepper', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'sliced'],
                    ['name' => 'Cooking Oil', 'quantity' => 2, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Chicken Inasal',
                'slug' => 'chicken-inasal',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 30,
                'cook_time' => 25,
                'servings' => 4,
                'description' => 'Grilled chicken marinated in calamansi, annatto oil, and lemongrass — a Bacolod classic.',
                'instructions' => "1. Blend garlic, lemongrass, ginger, and calamansi juice.\n2. Mix with annatto oil, patis, and pepper.\n3. Marinate chicken thighs for at least 2 hours.\n4. Grill over hot coals or pan-grill, basting with annatto oil.\n5. Serve with sinamak (spiced vinegar) and steamed rice.",
                'is_featured' => true,
                'tags' => ['filipino-classics', 'party-food'],
                'ingredients' => [
                    ['name' => 'Chicken Thigh', 'quantity' => 1.5, 'unit' => 'kg'],
                    ['name' => 'Garlic', 'quantity' => 6, 'unit' => 'cloves'],
                    ['name' => 'Lemon Grass', 'quantity' => 2, 'unit' => 'stalks'],
                    ['name' => 'Ginger', 'quantity' => 1, 'unit' => 'thumb-sized'],
                    ['name' => 'Soy Sauce (Toyo)', 'quantity' => 3, 'unit' => 'tbsp'],
                    ['name' => 'Cooking Oil', 'quantity' => 4, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Fried Chicken',
                'slug' => 'fried-chicken',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 10,
                'cook_time' => 25,
                'servings' => 4,
                'description' => 'Crispy golden fried chicken — simple, satisfying, and a family favorite.',
                'instructions' => "1. Season chicken with salt, pepper, and garlic powder.\n2. Coat in flour-cornstarch mixture.\n3. Heat oil to 350°F. Fry chicken for 12-15 minutes until golden and cooked through.\n4. Drain on paper towels. Serve hot.",
                'is_featured' => false,
                'tags' => ['budget-meals', 'quick-lunch', 'family-size'],
                'ingredients' => [
                    ['name' => 'Chicken Thigh', 'quantity' => 1, 'unit' => 'kg'],
                    ['name' => 'Garlic', 'quantity' => 3, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Cooking Oil', 'quantity' => 1, 'unit' => 'L', 'notes' => 'for deep frying'],
                    ['name' => 'Cornstarch', 'quantity' => 1, 'unit' => 'cup'],
                    ['name' => 'Eggs', 'quantity' => 2, 'unit' => 'pcs'],
                ],
            ],
            // === PORK ===
            [
                'title' => 'Pork Sinigang',
                'slug' => 'pork-sinigang',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 15,
                'cook_time' => 60,
                'servings' => 4,
                'description' => 'A sour tamarind-based soup with pork belly and vegetables — the ultimate Filipino comfort food.',
                'instructions' => "1. Boil pork belly in 2L water with onion and tomatoes for 45 minutes until tender.\n2. Add sinigang mix (or fresh tamarind paste).\n3. Add daikon radish, sitaw, okra, and eggplant. Cook 5 minutes.\n4. Add kangkong leaves. Turn off heat.\n5. Season with patis and sili (if desired). Serve hot with rice.",
                'is_featured' => true,
                'tags' => ['filipino-classics', 'family-size'],
                'ingredients' => [
                    ['name' => 'Pork Belly', 'quantity' => 500, 'unit' => 'g', 'notes' => 'cubed'],
                    ['name' => 'Sinigang Mix', 'quantity' => 1, 'unit' => 'pack'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'quartered'],
                    ['name' => 'Tomato', 'quantity' => 2, 'unit' => 'pcs', 'notes' => 'quartered'],
                    ['name' => 'Sitaw (String Beans)', 'quantity' => 1, 'unit' => 'bundle'],
                    ['name' => 'Kangkong (Water Spinach)', 'quantity' => 1, 'unit' => 'bundle'],
                    ['name' => 'Okra', 'quantity' => 5, 'unit' => 'pcs'],
                    ['name' => 'Eggplant', 'quantity' => 1, 'unit' => 'pcs'],
                    ['name' => 'Fish Sauce (Patis)', 'quantity' => 2, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Lechon Kawali',
                'slug' => 'lechon-kawali',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 20,
                'cook_time' => 45,
                'servings' => 4,
                'description' => 'Deep-fried crispy pork belly — crunchy skin, tender meat, served with lechon sauce.',
                'instructions' => "1. Boil pork belly with garlic, bay leaves, salt, and pepper for 30-40 minutes until tender.\n2. Drain and pat dry. Refrigerate uncovered for 1 hour (or pat very dry).\n3. Score the skin. Rub with salt.\n4. Deep fry in hot oil until skin is golden and crispy, about 8-10 minutes.\n5. Let rest, chop into pieces. Serve with lechon sauce or vinegar dipping.",
                'is_featured' => true,
                'tags' => ['filipino-classics', 'party-food'],
                'ingredients' => [
                    ['name' => 'Pork Belly', 'quantity' => 1, 'unit' => 'kg'],
                    ['name' => 'Garlic', 'quantity' => 4, 'unit' => 'cloves', 'notes' => 'crushed'],
                    ['name' => 'Bay Leaf (Laurel)', 'quantity' => 3, 'unit' => 'pcs'],
                    ['name' => 'Cooking Oil', 'quantity' => 1, 'unit' => 'L', 'notes' => 'for deep frying'],
                    ['name' => 'Salt', 'quantity' => 1, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Bicol Express',
                'slug' => 'bicol-express',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 15,
                'cook_time' => 30,
                'servings' => 4,
                'description' => 'A spicy pork stew in rich coconut milk — Bicol\'s fiery gift to the world.',
                'instructions' => "1. Sauté garlic, onion, and ginger until fragrant.\n2. Add pork belly slices. Cook until browned.\n3. Add alamang (shrimp paste) and siling haba. Stir.\n4. Pour coconut milk. Simmer for 20 minutes until pork is tender.\n5. Add siling labuyo for extra heat.\n6. Season with salt or patis. Serve with rice.",
                'is_featured' => false,
                'tags' => ['filipino-classics', 'party-food'],
                'ingredients' => [
                    ['name' => 'Pork Belly', 'quantity' => 500, 'unit' => 'g', 'notes' => 'sliced thin'],
                    ['name' => 'Garlic', 'quantity' => 4, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'sliced'],
                    ['name' => 'Ginger', 'quantity' => 1, 'unit' => 'thumb-sized', 'notes' => 'julienned'],
                    ['name' => 'Coconut (Gata)', 'quantity' => 2, 'unit' => 'pcs'],
                    ['name' => 'Fish Sauce (Patis)', 'quantity' => 2, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Pork Adobo',
                'slug' => 'pork-adobo',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 10,
                'cook_time' => 50,
                'servings' => 4,
                'description' => 'Rich and tangy pork belly adobo — slow-cooked to perfection.',
                'instructions' => "1. Season pork with soy sauce, vinegar, garlic, and bay leaves. Marinate 30 min.\n2. Brown pork in oil in a heavy pot.\n3. Pour in marinade plus 1 cup water. Bring to a boil.\n4. Lower heat, cover, and simmer for 45 minutes until pork is very tender.\n5. Uncover and reduce sauce until thick.\n6. Serve with steamed rice.",
                'is_featured' => false,
                'tags' => ['filipino-classics', 'budget-meals', 'family-size'],
                'ingredients' => [
                    ['name' => 'Pork Belly', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'cubed'],
                    ['name' => 'Soy Sauce (Toyo)', 'quantity' => 5, 'unit' => 'tbsp'],
                    ['name' => 'Vinegar (Suka)', 'quantity' => 3, 'unit' => 'tbsp'],
                    ['name' => 'Garlic', 'quantity' => 5, 'unit' => 'cloves', 'notes' => 'crushed'],
                    ['name' => 'Bay Leaf (Laurel)', 'quantity' => 3, 'unit' => 'pcs'],
                    ['name' => 'Ground Black Pepper', 'quantity' => 1, 'unit' => 'tsp'],
                ],
            ],
            [
                'title' => 'Pork Sisig',
                'slug' => 'pork-sisig',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 20,
                'cook_time' => 30,
                'servings' => 4,
                'description' => 'Sizzling chopped pork face and belly — sour, spicy, and utterly addictive.',
                'instructions' => "1. Boil pork belly and pork face in water with garlic and bay leaves until tender.\n2. Grill or pan-fry until charred. Chop finely.\n3. In a hot pan, sauté onions and chili.\n4. Add chopped pork. Season with calamansi juice, soy sauce, and pepper.\n5. Crack an egg on top, mix as it cooks.\n6. Serve sizzling hot on a hot plate.",
                'is_featured' => true,
                'tags' => ['filipino-classics', 'party-food'],
                'ingredients' => [
                    ['name' => 'Pork Belly', 'quantity' => 500, 'unit' => 'g'],
                    ['name' => 'Pork Sisig', 'quantity' => 300, 'unit' => 'g'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'minced'],
                    ['name' => 'Soy Sauce (Toyo)', 'quantity' => 2, 'unit' => 'tbsp'],
                    ['name' => 'Garlic', 'quantity' => 3, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Eggs', 'quantity' => 1, 'unit' => 'pcs'],
                ],
            ],
            [
                'title' => 'Pork Steak',
                'slug' => 'pork-steak',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 10,
                'cook_time' => 25,
                'servings' => 4,
                'description' => 'Tenderized pork chops simmered in soy-calamansi sauce with onions.',
                'instructions' => "1. Marinate pork chops in soy sauce and calamansi for 15 minutes.\n2. Heat oil. Fry pork until browned on both sides. Set aside.\n3. In same pan, sauté garlic and onion rings until soft.\n4. Pour in remaining marinade plus 1/2 cup water. Simmer.\n5. Return pork to pan. Cook 5 minutes.\n6. Season with pepper. Serve with rice.",
                'is_featured' => false,
                'tags' => ['budget-meals', 'quick-lunch'],
                'ingredients' => [
                    ['name' => 'Pork Loin', 'quantity' => 500, 'unit' => 'g', 'notes' => 'thin chops'],
                    ['name' => 'Soy Sauce (Toyo)', 'quantity' => 4, 'unit' => 'tbsp'],
                    ['name' => 'Garlic', 'quantity' => 3, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Onion', 'quantity' => 2, 'unit' => 'pcs', 'notes' => 'cut into rings'],
                    ['name' => 'Cooking Oil', 'quantity' => 2, 'unit' => 'tbsp'],
                ],
            ],
            // === BEEF ===
            [
                'title' => 'Beef Bulalo',
                'slug' => 'beef-bulalo',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 15,
                'cook_time' => 120,
                'servings' => 6,
                'description' => 'A slow-cooked beef shank soup with marrow bones — rich, hearty, and soul-warming.',
                'instructions' => "1. Boil beef shank in 3L water. Skim off scum.\n2. Add onion, peppercorns, and bullion cubes. Simmer for 1.5-2 hours.\n3. Add corn on the cob and cabbage. Cook 10 minutes.\n4. Season with patis and pepper.\n5. Serve hot — don't forget to scoop out the marrow!",
                'is_featured' => true,
                'tags' => ['filipino-classics', 'family-size'],
                'ingredients' => [
                    ['name' => 'Beef Bulalo', 'quantity' => 1.5, 'unit' => 'kg', 'notes' => 'bone-in shank'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'quartered'],
                    ['name' => 'Cabbage', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'quartered'],
                    ['name' => 'Fish Sauce (Patis)', 'quantity' => 3, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Beef Tapa',
                'slug' => 'beef-tapa',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 120,
                'cook_time' => 10,
                'servings' => 4,
                'description' => 'Sweet and garlicky cured beef — the star of the classic Tapsilog breakfast.',
                'instructions' => "1. Slice beef thinly against the grain.\n2. Marinate in soy sauce, calamansi, garlic, sugar, and pepper for 2 hours (or overnight).\n3. Heat oil in a pan. Fry beef slices in batches until caramelized.\n4. Serve with garlic fried rice and fried egg (Tapsilog!).",
                'is_featured' => false,
                'tags' => ['filipino-classics', '30-minute-meals', 'meal-prep'],
                'ingredients' => [
                    ['name' => 'Beef Tapa', 'quantity' => 500, 'unit' => 'g', 'notes' => 'thinly sliced'],
                    ['name' => 'Soy Sauce (Toyo)', 'quantity' => 4, 'unit' => 'tbsp'],
                    ['name' => 'Garlic', 'quantity' => 4, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Sugar (White)', 'quantity' => 2, 'unit' => 'tbsp'],
                    ['name' => 'Cooking Oil', 'quantity' => 3, 'unit' => 'tbsp'],
                ],
            ],
            [
                'title' => 'Beef Kaldereta',
                'slug' => 'beef-kaldereta',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 20,
                'cook_time' => 90,
                'servings' => 6,
                'description' => 'A rich tomato-based beef stew with liver spread, potatoes, and bell peppers.',
                'instructions' => "1. Season beef with salt and pepper. Brown in oil. Set aside.\n2. Sauté garlic, onion, and tomatoes until soft.\n3. Add kaldereta sauce mix, tomato sauce, and 2 cups water.\n4. Return beef to pot. Simmer for 1 hour until tender.\n5. Add liver spread (dissolved in water), potatoes, carrots, and bell peppers.\n6. Simmer 15 more minutes. Season with salt and pepper.",
                'is_featured' => true,
                'tags' => ['filipino-classics', 'party-food', 'family-size'],
                'ingredients' => [
                    ['name' => 'Beef Kaldereta Cut', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'cubed'],
                    ['name' => 'Caldereta Sauce Mix', 'quantity' => 1, 'unit' => 'pack'],
                    ['name' => 'Garlic', 'quantity' => 4, 'unit' => 'cloves', 'notes' => 'minced'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'chopped'],
                    ['name' => 'Tomato', 'quantity' => 3, 'unit' => 'pcs', 'notes' => 'chopped'],
                    ['name' => 'Potato', 'quantity' => 2, 'unit' => 'pcs', 'notes' => 'cubed'],
                    ['name' => 'Carrot', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'cubed'],
                    ['name' => 'Green Bell Pepper', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'sliced'],
                ],
            ],
            [
                'title' => 'Beef Mechado',
                'slug' => 'beef-mechado',
                'category' => 'meat',
                'difficulty' => 'medium',
                'prep_time' => 20,
                'cook_time' => 90,
                'servings' => 6,
                'description' => 'A tomato-based beef stew with potatoes, carrots, and bell peppers — slightly sweet and tangy.',
                'instructions' => "1. Marinate beef in soy sauce and calamansi for 30 minutes.\n2. Brown beef in oil. Set aside.\n3. Sauté garlic and onion. Add tomato sauce and 2 cups water.\n4. Return beef. Add bay leaves. Simmer for 1 hour.\n5. Add potatoes and carrots. Cook 15 minutes.\n6. Add bell peppers. Cook 5 minutes. Season and serve.",
                'is_featured' => false,
                'tags' => ['family-size', 'filipino-classics'],
                'ingredients' => [
                    ['name' => 'Beef Mechado Cut', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'cubed'],
                    ['name' => 'Soy Sauce (Toyo)', 'quantity' => 3, 'unit' => 'tbsp'],
                    ['name' => 'Garlic', 'quantity' => 4, 'unit' => 'cloves'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs'],
                    ['name' => 'Tomato', 'quantity' => 3, 'unit' => 'pcs'],
                    ['name' => 'Potato', 'quantity' => 2, 'unit' => 'pcs'],
                    ['name' => 'Carrot', 'quantity' => 1, 'unit' => 'pcs'],
                    ['name' => 'Green Bell Pepper', 'quantity' => 1, 'unit' => 'pcs'],
                ],
            ],
            [
                'title' => 'Beef Nilaga',
                'slug' => 'beef-nilaga',
                'category' => 'meat',
                'difficulty' => 'easy',
                'prep_time' => 15,
                'cook_time' => 120,
                'servings' => 6,
                'description' => 'A simple but deeply flavorful boiled beef soup with vegetables.',
                'instructions' => "1. Boil beef in 3L water. Skim scum.\n2. Add peppercorns, onion, and bullion cubes. Simmer 1.5 hours.\n3. Add potatoes and sayote. Cook 10 minutes.\n4. Add cabbage and saba bananas (optional). Cook 5 minutes.\n5. Season with patis. Serve hot with rice.",
                'is_featured' => false,
                'tags' => ['filipino-classics', 'family-size', 'budget-meals'],
                'ingredients' => [
                    ['name' => 'Beef Nilaga Cut', 'quantity' => 1, 'unit' => 'kg'],
                    ['name' => 'Onion', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'quartered'],
                    ['name' => 'Potato', 'quantity' => 2, 'unit' => 'pcs', 'notes' => 'quartered'],
                    ['name' => 'Cabbage', 'quantity' => 1, 'unit' => 'pcs', 'notes' => 'quartered'],
                    ['name' => 'Sayote (Chayote)', 'quantity' => 2, 'unit' => 'pcs'],
                    ['name' => 'Fish Sauce (Patis)', 'quantity' => 3, 'unit' => 'tbsp'],
                ],
            ],
        ];

        foreach ($recipes as $data) {
            $recipeTags = [];
            foreach ($data['tags'] as $slug) {
                if (isset($tags[$slug])) {
                    $recipeTags[] = $tags[$slug];
                }
            }

            $recipe = Recipe::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'servings' => $data['servings'],
                'prep_time' => $data['prep_time'],
                'cook_time' => $data['cook_time'],
                'difficulty' => $data['difficulty'],
                'instructions' => $data['instructions'],
                'is_featured' => $data['is_featured'],
            ]);

            // Attach tags
            if (!empty($recipeTags)) {
                $recipe->tags()->attach($recipeTags);
            }

            // Add ingredients with pivot data
            $ingredientPivot = [];
            foreach ($data['ingredients'] as $idx => $item) {
                $ingredient = $ingredients[$item['name']] ?? null;
                if ($ingredient) {
                    $ingredientPivot[$ingredient] = [
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'notes' => $item['notes'] ?? null,
                        'sort_order' => $idx + 1,
                    ];
                }
            }
            if (!empty($ingredientPivot)) {
                $recipe->ingredients()->attach($ingredientPivot);
            }
        }

        $this->command->info('Seeded ' . count($recipes) . ' recipes.');
    }
}
