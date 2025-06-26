<?php
// src/Entity/Translation.php
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "ext_translations")]
class Translation extends AbstractTranslation
{
    // Laisse vide, tout est hérité
}