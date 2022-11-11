<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Application\Command\User\SendResetPasswordMail;

class SendResetPasswordLinkMail
{
    public string $locale;
    public string $linkUuid;
    public string $host;

    // ————————————————————————————————————————————————————————————————————————
    // Constructor
    // ————————————————————————————————————————————————————————————————————————

    public function __construct(string $locale, string $linkUuid, string $host)
    {
        $this->locale = $locale;
        $this->linkUuid = $linkUuid;
        $this->host = $host;
    }

    // ————————————————————————————————————————————————————————————————————————
    // Getters
    // ————————————————————————————————————————————————————————————————————————

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getLinkUuid(): string
    {
        return $this->linkUuid;
    }

    public function getHost(): string
    {
        return $this->host;
    }
}
