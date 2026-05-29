<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping ResourceSeeder.');
            return;
        }

        $resources = [
            // ── STEM ──────────────────────────────────────────────
            [
                'title'        => 'MIT OpenCourseWare — Introduction to Computer Science',
                'description'  => 'Free MIT course covering the fundamentals of computer science and programming using Python.',
                'url'          => 'https://ocw.mit.edu/courses/6-0001-introduction-to-computer-science-and-programming-in-python-fall-2016/',
                'type'         => 'course',
                'area'         => 'programming',
                'target_roles' => ['all'],
                'thumbnail_url'=> 'https://ocw.mit.edu/images/ocw_home_page.jpg',
                'author'       => 'John Guttag',
                'source'       => 'MIT OpenCourseWare',
            ],
            [
                'title'        => 'Khan Academy — STEM Essentials',
                'description'  => 'Khan Academy\'s free interactive exercises and lessons covering math, science, computing, and engineering.',
                'url'          => 'https://www.khanacademy.org/computing',
                'type'         => 'course',
                'area'         => 'stem',
                'target_roles' => ['student'],
                'thumbnail_url'=> 'https://cdn.kastatic.org/images/khan-logo-dark-background-new.png',
                'author'       => 'Khan Academy',
                'source'       => 'Khan Academy',
            ],
            [
                'title'        => 'CS50: Introduction to Computer Science',
                'description'  => 'Harvard\'s iconic free intro to computer science — covers C, Python, SQL, JavaScript and web basics.',
                'url'          => 'https://cs50.harvard.edu/x/',
                'type'         => 'course',
                'area'         => 'programming',
                'target_roles' => ['all'],
                'thumbnail_url'=> 'https://cs50.harvard.edu/x/2024/assets/pdp.jpg',
                'author'       => 'David J. Malan',
                'source'       => 'Harvard / edX',
            ],

            // ── Robotics ─────────────────────────────────────────
            [
                'title'        => 'NASA STEM Robotics Resources',
                'description'  => 'Official NASA resources for robotics, space exploration STEM activities and competitions.',
                'url'          => 'https://www.nasa.gov/learning-resources/stem-engagement/',
                'type'         => 'link',
                'area'         => 'robotics',
                'target_roles' => ['student', 'teacher'],
                'thumbnail_url'=> 'https://www.nasa.gov/wp-content/themes/nasa/assets/images/nasa-logo.svg',
                'author'       => 'NASA',
                'source'       => 'NASA',
            ],
            [
                'title'        => 'Scratch — Learn Programming Through Games',
                'description'  => 'Visual block-based programming environment by MIT. Perfect for beginners and creative projects.',
                'url'          => 'https://scratch.mit.edu/educators/',
                'type'         => 'tool',
                'area'         => 'programming',
                'target_roles' => ['student'],
                'thumbnail_url'=> 'https://scratch.mit.edu/images/logo_sm.png',
                'author'       => 'MIT Media Lab',
                'source'       => 'Scratch / MIT',
            ],

            // ── AI ────────────────────────────────────────────────
            [
                'title'        => 'Google — Introduction to Generative AI',
                'description'  => 'Free Google Cloud course explaining what generative AI is, how it works, and its applications.',
                'url'          => 'https://www.cloudskillsboost.google/paths/118',
                'type'         => 'course',
                'area'         => 'ai',
                'target_roles' => ['all'],
                'thumbnail_url'=> 'https://www.gstatic.com/devrel-devsite/prod/ve76b7b75eb72d6cc57d7f5c60c9d21ae7c3ee3571ff4f36476be66e7de50e614/cloud/images/cloud-logo.svg',
                'author'       => 'Google Cloud',
                'source'       => 'Google Cloud Skills Boost',
            ],
            [
                'title'        => 'Elements of AI — Free Online Course',
                'description'  => 'University of Helsinki\'s accessible intro to AI concepts — no programming required. Available in many languages.',
                'url'          => 'https://www.elementsofai.com/',
                'type'         => 'course',
                'area'         => 'ai',
                'target_roles' => ['all'],
                'thumbnail_url'=> 'https://www.elementsofai.com/assets/elements-of-ai-og.png',
                'author'       => 'University of Helsinki & Reaktor',
                'source'       => 'Elements of AI',
            ],
            [
                'title'        => 'Machine Learning for Kids',
                'description'  => 'Hands-on ML activities designed for children. Train models to recognize text, images, and sounds.',
                'url'          => 'https://machinelearningforkids.co.uk/',
                'type'         => 'tool',
                'area'         => 'ai',
                'target_roles' => ['student'],
                'thumbnail_url'=> 'https://machinelearningforkids.co.uk/assets/images/logo.png',
                'author'       => 'Dale Lane (IBM)',
                'source'       => 'Machine Learning for Kids',
            ],

            // ── Innovation ────────────────────────────────────────
            [
                'title'        => 'IDEO Design Thinking Toolkit for Educators',
                'description'  => 'Free PDF toolkit from IDEO on applying human-centered design in educational settings.',
                'url'          => 'https://www.designthinkingforeducators.com/',
                'type'         => 'book',
                'area'         => 'innovation',
                'target_roles' => ['teacher'],
                'thumbnail_url'=> 'https://www.designthinkingforeducators.com/images/dt-educators-toolkit.jpg',
                'author'       => 'IDEO',
                'source'       => 'Design Thinking for Educators',
            ],
            [
                'title'        => 'Stanford d.school — Design Thinking Bootleg',
                'description'  => 'Stanford\'s open-access design thinking guide with tools, methods, and frameworks for innovation.',
                'url'          => 'https://dschool.stanford.edu/resources/design-thinking-bootleg',
                'type'         => 'book',
                'area'         => 'innovation',
                'target_roles' => ['teacher'],
                'thumbnail_url'=> 'https://dschool.stanford.edu/images/dschool-logo.png',
                'author'       => 'Stanford d.school',
                'source'       => 'Stanford d.school',
            ],
            [
                'title'        => 'FIRST Robotics Competition — Innovation Challenge',
                'description'  => 'FIRST program resources for student innovation challenges, robotics competitions, and STEM leadership.',
                'url'          => 'https://www.firstinspires.org/',
                'type'         => 'link',
                'area'         => 'innovation',
                'target_roles' => ['student', 'teacher'],
                'thumbnail_url'=> 'https://www.firstinspires.org/sites/default/files/uploads/resource_library/brand/first-logo.png',
                'author'       => 'FIRST',
                'source'       => 'FIRST Inspires',
            ],

            // ── Math ──────────────────────────────────────────────
            [
                'title'        => 'Khan Academy — Mathematics (All Levels)',
                'description'  => 'Comprehensive free math courses from arithmetic to calculus, linear algebra, and statistics.',
                'url'          => 'https://www.khanacademy.org/math',
                'type'         => 'course',
                'area'         => 'math',
                'target_roles' => ['student'],
                'thumbnail_url'=> null,
                'author'       => 'Khan Academy',
                'source'       => 'Khan Academy',
            ],
            [
                'title'        => 'Desmos — Interactive Graphing Calculator',
                'description'  => 'Free browser-based graphing calculator and classroom activities for algebra, geometry, and more.',
                'url'          => 'https://www.desmos.com/calculator',
                'type'         => 'tool',
                'area'         => 'math',
                'target_roles' => ['all'],
                'thumbnail_url'=> 'https://www.desmos.com/assets/img/social/share.png',
                'author'       => 'Desmos',
                'source'       => 'Desmos',
            ],

            // ── Science ───────────────────────────────────────────
            [
                'title'        => 'PhET Interactive Simulations — Physics & Chemistry',
                'description'  => 'University of Colorado\'s free interactive science simulations for physics, chemistry, biology, and math.',
                'url'          => 'https://phet.colorado.edu/',
                'type'         => 'tool',
                'area'         => 'science',
                'target_roles' => ['all'],
                'thumbnail_url'=> 'https://phet.colorado.edu/images/phet-logo-tm.png',
                'author'       => 'University of Colorado Boulder',
                'source'       => 'PhET',
            ],
            [
                'title'        => 'CK-12 — Free Science & STEM Textbooks',
                'description'  => 'Customizable free digital textbooks covering biology, chemistry, physics, and earth science.',
                'url'          => 'https://www.ck12.org/student/',
                'type'         => 'book',
                'area'         => 'science',
                'target_roles' => ['student', 'teacher'],
                'thumbnail_url'=> 'https://www.ck12.org/media/images/ck12-logo-og.png',
                'author'       => 'CK-12 Foundation',
                'source'       => 'CK-12',
            ],

            // ── Design ────────────────────────────────────────────
            [
                'title'        => 'Figma — Free Collaborative Design Tool',
                'description'  => 'Industry-standard UI/UX design tool available free for students and educators. Great for prototyping and wireframing.',
                'url'          => 'https://www.figma.com/education/',
                'type'         => 'tool',
                'area'         => 'design',
                'target_roles' => ['student', 'teacher'],
                'thumbnail_url'=> 'https://cdn.sanity.io/images/599r6htc/localized/46a76c802176eb17b04e12108de7e7e0f3736dc6-1108x1108.png',
                'author'       => 'Figma',
                'source'       => 'Figma Education',
            ],

            // ── Programming ───────────────────────────────────────
            [
                'title'        => 'freeCodeCamp — Full-Stack Web Development',
                'description'  => '3,000+ hours of free coding curriculum: HTML, CSS, JavaScript, Python, databases, APIs, and more.',
                'url'          => 'https://www.freecodecamp.org/',
                'type'         => 'course',
                'area'         => 'programming',
                'target_roles' => ['all'],
                'thumbnail_url'=> 'https://www.freecodecamp.org/icons/icon-512x512.png',
                'author'       => 'freeCodeCamp',
                'source'       => 'freeCodeCamp',
            ],
            [
                'title'        => 'Code.org — Hour of Code & CS Curriculum',
                'description'  => 'Free computer science curriculum for K-12. Includes Minecraft, Star Wars, and Frozen coding activities.',
                'url'          => 'https://code.org/',
                'type'         => 'course',
                'area'         => 'programming',
                'target_roles' => ['student', 'teacher'],
                'thumbnail_url'=> 'https://code.org/images/logo.png',
                'author'       => 'Code.org',
                'source'       => 'Code.org',
            ],
        ];

        foreach ($resources as $data) {
            Resource::create(array_merge($data, [
                'is_published' => true,
                'created_by'   => $admin->id,
            ]));
        }

        $this->command->info('✅ ResourceSeeder: ' . count($resources) . ' resources seeded.');
    }
}
