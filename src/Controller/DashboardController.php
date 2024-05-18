<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CommandeRepository $commandeRepository, DetailCommandeRepository $detailCommandeRepository): Response
    {
        // Fetch data for charts
        $topSellingBooks = $detailCommandeRepository->findTopSellingBooks();
        $orderCounts = $commandeRepository->findOrderCounts();

        return $this->render('dashboard/index.html.twig', [
            'topSellingBooks' => $topSellingBooks,
            'orderCounts' => $orderCounts,
        ]);
    }
}
