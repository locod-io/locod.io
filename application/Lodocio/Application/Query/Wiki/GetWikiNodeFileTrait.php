<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Query\Wiki;

use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeFileReadModel;

trait GetWikiNodeFileTrait
{
    /**
     * @throws \Exception
     */
    public function getWikiFileById(int $id): WikiNodeFileReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeFileId($id);
        $query = new GetWikiFile(
            $this->wikiNodeFileRepository,
        );
        return $query->ById($id);
    }

}
