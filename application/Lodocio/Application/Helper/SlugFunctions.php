<?php

namespace App\Lodocio\Application\Helper;

use Symfony\Component\String\Slugger\AsciiSlugger;

class SlugFunctions
{
    /**
     * @throws \Exception
     */
    public static function generateRandomSlug(int $length = 8): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, strlen($characters) - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }

    public static function slugify(string $input): string
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($input);
    }

}
