<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Livres;
use App\Entity\DetailsLivre;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;




class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier_index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $detailsLivres = [];
        if ($user) {
            $panier = $user->getPanier();
            if ($panier) {
                $detailsLivres = $panier->getDetails();
            }
        }

        return $this->render('panier/index.html.twig', [
            'detailsLivres' => $detailsLivres,
        ]);
    }

    #[Route('/panier/ajouter-livre/{id}', name: 'panier_ajouter_livre')]
    public function ajouterLivre(Request $request, EntityManagerInterface $entityManager, Livres $livre): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $panier = $user->getPanier();
    
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $entityManager->persist($panier);
            $entityManager->flush(); // Vous devez flush avant de récupérer l'id du panier
        }
    
        // Assurez-vous de récupérer le prix du livre
        $prixLivre = $livre->getPrix();
    
        $detailsLivre = new DetailsLivre();
        $detailsLivre->setPanier($panier);
        $detailsLivre->setLivre($livre);
    
        // Assignez le prix du livre au détail du livre
        $detailsLivre->setPrix($prixLivre);
    
        $entityManager->persist($detailsLivre);
        $entityManager->flush(); // Flush après l'ajout du détail du livre
    
        return $this->redirectToRoute('panier_index');
    
    }

    #[Route('/panier/supprimer-livre/{id}', name: 'panier_supprimer_livre')]
    public function supprimerLivre(Livres $livre, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) { 
            return $this->redirectToRoute('app_login');
        }

        $panier = $user->getPanier();
        if (!$panier) {
            return $this->redirectToRoute('panier_index');
        }

        $detailsLivres = $panier->getDetails();
        foreach ($detailsLivres as $detailLivre) {
            if ($detailLivre->getLivre() === $livre) {
                $panier->removeDetail($detailLivre);
                $entityManager->remove($detailLivre);
                break;
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('panier_index');
    
    }

    #[Route('/panier/ajouter-quantite/{id}', name: 'panier_ajouter_quantite', methods: ['POST'])]
public function ajouterQuantite(Request $request, EntityManagerInterface $entityManager, Livres $livre): JsonResponse
{
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['message' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
    }

    $panier = $user->getPanier();

    if (!$panier) {
        $panier = new Panier();
        $panier->setUser($user);
        $entityManager->persist($panier);
        $entityManager->flush();
    }

    $quantiteRestante = $livre->getQte(); 

    $quantiteAjoutee = $request->request->getInt('quantite', 1); // Utilisation de getInt pour récupérer la quantité directement

    if ($quantiteAjoutee < 1) {
        return new JsonResponse(['message' => 'La quantité doit être supérieure à 0'], Response::HTTP_BAD_REQUEST);
    }

    // Vérifier que la quantité ajoutée ne dépasse pas la quantité restante dans la base
    if ($quantiteAjoutee > $quantiteRestante) {
        return new JsonResponse(['message' => 'La quantité ajoutée dépasse la quantité restante dans la base'], Response::HTTP_BAD_REQUEST);
    }

    $panier->ajouterLivreQuantite($livre, $quantiteAjoutee);

    $entityManager->flush();

    return new JsonResponse(['message' => 'Quantité ajoutée avec succès']);
}

    #[Route('/panier/diminuer-quantite/{id}', name: 'panier_diminuer_quantite', methods: ['POST'])]
    public function diminuerQuantite(Request $request, EntityManagerInterface $entityManager, Livres $livre): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $panier = $user->getPanier();

        if (!$panier) {
            return new JsonResponse(['message' => 'Panier introuvable'], Response::HTTP_NOT_FOUND);
        }

        $quantiteActuelle = $panier->getQuantitePourLivre($livre);

        $quantiteDiminuee = $request->request->getInt('quantite', 1); // Utilisation de getInt pour récupérer la quantité directement

        if ($quantiteDiminuee < 1) {
            return new JsonResponse(['message' => 'La quantité doit être supérieure à 0'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier que la quantité actuelle dans le panier est suffisante pour diminuer
        if ($quantiteActuelle < $quantiteDiminuee) {
            return new JsonResponse(['message' => 'La quantité à diminuer dépasse la quantité actuelle dans le panier'], Response::HTTP_BAD_REQUEST);
        }

        // Diminuer la quantité dans le panier
        $panier->diminuerLivreQuantite($livre, $quantiteDiminuee);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        return new JsonResponse(['message' => 'Quantité diminuée avec succès']);
    }

    
}
