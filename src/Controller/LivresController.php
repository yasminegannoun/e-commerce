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
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
  


    #[Route('/admin/livres/delete/{id}', name: 'app_admin_livres_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_livres', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/admin/livres/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_livres', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
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
    
    #[Route('/admin/livres/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_livres', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }
    
    



    
}
