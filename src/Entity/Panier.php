<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Livres;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'panier', cascade: ['persist', 'remove'])]
    private ?User $user = null;
   

    #[ORM\OneToMany(targetEntity: DetailsLivre::class, mappedBy: 'panier', cascade: ['persist', 'remove'])]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, DetailsLivre>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(DetailsLivre $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setPanier($this);
        }

        return $this;
    }

    public function removeDetail(DetailsLivre $detail): static
    {
        if ($this->details->removeElement($detail)) {
            if ($detail->getPanier() === $this) {
                $detail->setPanier(null);
            }
        }

        return $this;
    }

    public function ajouterLivreQuantite(Livres $livre, int $quantite): void
    {
        foreach ($this->details as $detail) {
            if ($detail->getLivre() === $livre) {
                $detail->setQuantite($detail->getQuantite() + $quantite);
                return;
            }
        }

        $newDetail = new DetailsLivre();
        $newDetail->setLivre($livre);
        $newDetail->setPanier($this);
        $newDetail->setQuantite($quantite);
        $this->addDetail($newDetail);
    }

    public function diminuerLivreQuantite(Livres $livre, int $quantite): void
    {
        foreach ($this->details as $detail) {
            if ($detail->getLivre() === $livre) {
                $quantiteRestante = $detail->getQuantite() - $quantite;
                if ($quantiteRestante > 0) {
                    $detail->setQuantite($quantiteRestante);
                } else {
                    $this->removeDetail($detail);
                }
                return;
            }
        }
    }
}

