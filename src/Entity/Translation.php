<?php

namespace App\Entity;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "ext_translations")]
class Translation extends AbstractTranslation
{
    // Pas besoin d'ajouter autre chose, tout est hérité
}