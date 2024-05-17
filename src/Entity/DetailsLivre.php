<?php

namespace App\Entity;

use App\Repository\DetailsLivreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsLivreRepository::class)]
class DetailsLivre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'details')]
    private ?Panier $panier = null;

    #[ORM\ManyToOne(targetEntity: Livres::class , inversedBy: 'details')]
    private ?Livres $livre = null;

    #[ORM\Column]
    private int $Qte = 1;

    #[ORM\Column]
    private float $prix;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;

        return $this;
    }

    public function getLivre(): ?Livres
    {
        return $this->livre;
    }

    public function setLivre(?Livres $livre): static
    {
        $this->livre = $livre;

        return $this;
    }

    public function getQuantite(): int
    {
        return $this->Qte;
    }

    public function setQuantite(int $Qte): static
    {
        $this->Qte = $Qte;

        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getdate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setdate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
