<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class LivresController extends AbstractController
{
    #[Route('/livres', name: 'admin_livres')]
    public function index(LivresRepository $rep, CategoriesRepository $categoriesRepository, AuthorizationCheckerInterface $authChecker): Response
{
    $livres = $rep->findAll();
    $categories = $categoriesRepository->findAll(); // Fetch categories
    if ($authChecker->isGranted('ROLE_ADMIN')) {
        return $this->render('Livres/index.html.twig', ['livres' => $livres, 'categories' => $categories]);
    } else {
        return $this->render('Livres/showBookUser.html.twig', ['livres' => $livres, 'categories' => $categories]);
    }
}

    #[Route('/livres/show/{id}', name: 'admin_livres_show')]
    public function show(Livres $livre): Response
    {

        return $this->render('Livres/show.html.twig', ['livre' => $livre]);
    }
    #[Route('/admin/livres/create', name: 'app_admin_livres_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $livre1 = new Livres();
        $livre1->setAuteur('auteur 1')
            ->setEditedAt(new \DateTimeImmutable('01-01-2023'))
            ->setTitre('Titre 4')
            ->setQte(100)
            ->setResume('jhgkjhkjhlhdjfjfdgpghkgmgbkmgblkgm')
            ->setSlug('titre-4')
            ->setPrix(200)
            ->setEditeur('Eni')
            ->setISBN('111.1111.1111.1115')
            ->setImage('https://picsum.photos/300');
        $livre2 = new Livres();
        $livre2->setAuteur('auteur 3')
            ->setEditedAt(new \DateTimeImmutable('01-01-2023'))
            ->setTitre('Titre 4')
            ->setQte(100)
            ->setResume('jhgkjhkjhlhdjfjfdgpghkgmgbkmgblkgm')
            ->setSlug('titre-4')
            ->setPrix(200)
            ->setEditeur('Eni')
            ->setISBN('111.1111.1111.1115')
            ->setImage('https://picsum.photos/300');
        $em->persist($livre1);
        $em->persist($livre2);
        $em->flush();
        dd($livre1);
    }
    #[Route('/admin/livres/delete/{id}', name: 'app_admin_livres_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(EntityManagerInterface $em, Livres $livre): Response
    {

        $em->remove($livre);
        $em->flush();
        dd($livre);
    }
    #[Route('/admin/livres/add', name: 'admin_livres_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $livre = new Livres();
        //construction de l'objet formulaire
        $form = $this->createForm(LivreType::class, $livre);
        // recupéretaion et traitement de données
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($livre);
            $em->flush();
            return $this->redirectToRoute('admin_livres');
        }

        return $this->render('livres/add.html.twig', [
            'f' => $form

        ]);
    }

    #[Route('/livres/recherche', name: 'user_livres_recherche')]
    public function recherche(Request $request, LivresRepository $livresRepository, CategoriesRepository $categoriesRepository): Response
    {
        // Get the search parameters from the request
        $searchTerm = $request->query->get('titre') ?? ''; // Title
        $categoryId = $request->query->get('categorie') ?? null; // Category ID
        $author = $request->query->get('auteur') ?? ''; // Author
    
        // Call the repository method to fetch categories
        $categories = $categoriesRepository->findAll();
    
        // Call the repository method with the search criteria
        $livres = $livresRepository->findBySearchCriteria($searchTerm, $categoryId, $author);
    
        // Return the search results and categories to the appropriate template
        return $this->render('Livres/recherche.html.twig', [
            'livres' => $livres,
            'categories' => $categories,
        ]);
    }
    
    
    
    



    
}
