<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PanierRepository $panierRepository;

    public function __construct(EntityManagerInterface $entityManager, PanierRepository $panierRepository)
    {
        $this->entityManager = $entityManager;
        $this->panierRepository = $panierRepository;
    }

    #[Route('/create_commande', name: 'create_commande')]
    public function createCommande(Request $request): Response
    {
        $user = $this->getUser();
        $panier = $this->panierRepository->findOneBy(['user' => $user]);

        if (!$panier) {
            throw $this->createNotFoundException('Panier not found for user.');
        }

        $commande = new Commande();
        $commande->setUser($user);
        $commande->setDate(new \DateTime());
        $commande->setStatus('En attente');
        $total = 0;

        foreach ($panier->getDetails() as $detail) {
            $detailCommande = new DetailCommande();
            $detailCommande->setLivre($detail->getLivre());
            $detailCommande->setQuantite($detail->getQuantite());
            $detailCommande->setPrix($detail->getPrix());
            $detailCommande->setCommande($commande);

            $total += $detail->getQuantite() * $detail->getPrix();

            $this->entityManager->persist($detailCommande);
        }

        $commande->setTotal($total);
        $this->entityManager->persist($commande);

        // Sauvegarder toutes les entités en base de données
        $this->entityManager->flush();

        return $this->redirectToRoute('app_commande_history');
    }


    #[Route('/historique_commandes', name: 'app_commande_history')]
    public function commandeHistory(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $commandes = $commandeRepository->findBy(['user' => $user]);

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/admin/commandes', name: 'admin_commandes')]
    #[IsGranted('ROLE_ADMIN')]
    public function commandes(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        return $this->render('commande/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/admin/commande/{id}', name: 'admin_commande_detail')]
    #[IsGranted('ROLE_ADMIN')]
    public function commandeDetail($id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('La commande n\'existe pas');
        }

        return $this->render('commande/commande_detail.html.twig', [
            'commande' => $commande,
        ]);
    }
}
