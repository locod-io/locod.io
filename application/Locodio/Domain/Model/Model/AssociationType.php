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

namespace App\Locodio\Domain\Model\Model;

enum AssociationType: string
{
    case MTOU = 'Many-To-One_Unidirectional';
    case OTOU = 'One-To-One_Unidirectional';
    case OTOB = 'One-To-One_Bidirectional';
    case OTOS = 'One-To-One_Self-referencing';
    case OTMB = 'One-To-Many_Bidirectional';
    case OTMS = 'One-To-Many_Self-referencing';
    case MTMU = 'Many-To-Many_Unidirectional';
    case MTMB = 'Many-To-Many_Bidirectional';
    case MTMS = 'Many-To-Many_Self-referencing';
}

/*

Doctrine relations

— Many-To-One, Unidirectional
— One-To-One, Unidirectional
— One-To-One, Bidirectional
— One-To-One, Self-referencing
— One-To-Many, Bidirectional

— One-To-Many, Self-referencing
— Many-To-Many, Unidirectional
— Many-To-Many, Bidirectional
— Many-To-Many, Self-referencing

 */
