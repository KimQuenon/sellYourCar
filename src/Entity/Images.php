<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message:"Url invalide")]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:10, max:250, minMessage:"La légende doit faire plus de 10 caractères.", maxMessage:"La légende ne doit pas faire plus de 250 caractères.")]
    private ?string $caption = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): static
    {
        $this->caption = $caption;

        return $this;
    }
}
