<?php

namespace App\Lodocio\Application\Command\Tracker;

enum ProjectDocumentType: string
{
    case TRACKER = 'tracker';
    case GROUP = 'group';
    case NODE = 'node';

}
