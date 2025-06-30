<?php

namespace App\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\AppelsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppelsRepository::class)]
#[Gedmo\TranslationEntity(class: "App\Entity\Translation")]
class Appels
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Gedmo\Translatable]    
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Gedmo\Translatable]    
    #[ORM\Column(type: Types::TEXT)]
    private ?string $texte = null;

    #[Gedmo\Locale] // Cette propriété ne sera pas en BDD
    private ?string $locale = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;


    public function setTranslatableLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
