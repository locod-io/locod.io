<?php

namespace App\Lodocio\Application\Helper;

use Symfony\Component\String\Slugger\AsciiSlugger;

class SlugFunctions
{
    /**
     * @throws \Exception
     */
    public static function generateRandomSlug(int $length = 12): string
    {
        $randomBytes = random_bytes($length);
        return bin2hex($randomBytes);
    }

    public static function slugify(string $input): string
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($input);
    }

}
