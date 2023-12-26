<?php

/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/blog/resizing-images-with-php/
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/

declare(strict_types=1);

namespace App\Lodocio\Application\Helper;

class SimpleImage
{
    private function __construct(private \GdImage $image, readonly int $image_type)
    {
    }

    /**
     * @throws \Exception
     */
    public static function load($filename): self
    {
        $image_info = getimagesize($filename);
        $_image_type = $image_info[2];

        if ($_image_type == IMAGETYPE_JPEG) {
            $_image = imagecreatefromjpeg($filename);
        } elseif ($_image_type == IMAGETYPE_GIF) {
            $_image = imagecreatefromgif($filename);
        } elseif ($_image_type == IMAGETYPE_PNG) {
            $_image = imagecreatefrompng($filename);
        } else {
            throw new \Exception('Unknown image file extension.');
        }

        return new self($_image, $_image_type);
    }

    public function save($filename, $image_type = IMAGETYPE_PNG, $compression = 75, $permissions = null): void
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }

    public function output($image_type = IMAGETYPE_JPEG): void
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }

    public function getWidth(): int
    {
        return (int)imagesx($this->image);
    }

    public function getHeight(): int
    {
        return (int)imagesy($this->image);
    }

    public function resizeToHeight(int $height): void
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize((int)$width, (int)$height);
    }

    public function resizeToWidth(int $width): void
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize((int)$width, (int)$height);
    }

    public function scale(float $scale): void
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize((int)$width, (int)$height);
    }

    public function resize(int $width, int $height): void
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

}
