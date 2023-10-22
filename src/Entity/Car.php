<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CarRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields:['model'], message:"Un tel modèle existe déjà dans notre store, merci de le modifier.")]


class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez renseigner le modèle du véhicule")]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez renseigner la marque du véhicule")]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message:"Url invalide")]
    private ?string $coverImage = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez renseigner le kilométrage du véhicule")]
    private ?int $km = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez renseigner le prix du véhicule")]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez renseigner le nombre de propriétaires du véhicule")]
    private ?int $owners = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez renseigner la cylindrée du véhicule")]
    private ?float $cylinder = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez renseigner la puissance du véhicule")]
    private ?int $power = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez renseigner le type de carburant consommé par le véhicule")]
    private ?string $carburant = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez renseigner l'année de mise en circulation du véhicule")]
    private ?int $year = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez renseigner le type de transmission du véhicule")]
    private ?string $transmission = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 50, minMessage:"La description doit faire plus de 50 caractères.")]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 50, minMessage:"Vos options doivent faire plus de 50 caractères.")]
    private ?string $options = null;

    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Images::class, orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * init "slugification"
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            //slugifier la marque et le modèle pour créer un slug "combiné"
            $brandSlug = $slugify->slugify($this->brand);
            $modelSlug = $slugify->slugify($this->model);

            $this->slug = $brandSlug . '-' . $modelSlug;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getKm(): ?int
    {
        return $this->km;
    }

    public function setKm(int $km): static
    {
        $this->km = $km;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOwners(): ?int
    {
        return $this->owners;
    }

    public function setOwners(int $owners): static
    {
        $this->owners = $owners;

        return $this;
    }

    public function getCylinder(): ?float
    {
        return $this->cylinder;
    }

    public function setCylinder(float $cylinder): static
    {
        $this->cylinder = $cylinder;

        return $this;
    }

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getCarburant(): ?string
    {
        return $this->carburant;
    }

    public function setCarburant(string $carburant): static
    {
        $this->carburant = $carburant;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(string $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setCar($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getCar() === $this) {
                $image->setCar(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
