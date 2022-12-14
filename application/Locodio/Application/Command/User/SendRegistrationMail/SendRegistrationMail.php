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

namespace App\Locodio\Application\Command\User\SendRegistrationMail;

class SendRegistrationMail
{
    // ————————————————————————————————————————————————————————————————————————
    // Constructor
    // ————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected string $locale,
        protected string $linkUuid,
        protected string $host
    ) {
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
