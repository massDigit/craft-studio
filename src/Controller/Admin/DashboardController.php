<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\ProjectRequest;

class DashboardController extends AbstractController
{
    public function renderProjectRequestsWidget(Request $request, EntityManagerInterface $em): Response
    {
        $period = $request->query->get('period', 'all');
        
        $startDate = null;
        switch ($period) {
            case '2_weeks':
                $startDate = new \DateTime('-2 weeks');
                break;
            case '1_month':
                $startDate = new \DateTime('-1 month');
                break;
            case '3_months':
                $startDate = new \DateTime('-3 months');
                break;
            case '6_months':
                $startDate = new \DateTime('-6 months');
                break;
            case '1_year':
                $startDate = new \DateTime('-1 year');
                break;
        }

        $qb = $em->createQueryBuilder()
            ->select('r.status, COUNT(r.id) as total')
            ->from(ProjectRequest::class, 'r')
            ->groupBy('r.status');

        if ($startDate) {
            $qb->andWhere('r.createdAt >= :startDate')
               ->setParameter('startDate', $startDate);
        }

        $results = $qb->getQuery()->getArrayResult();

        $stats = [
            ProjectRequest::STATUS_NEW => 0,
            ProjectRequest::STATUS_PROCESSING => 0,
            ProjectRequest::STATUS_ARCHIVED => 0,
        ];

        foreach ($results as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = (int) $row['total'];
            }
        }

        // Toujours afficher les 5 dernières peu importe le filtre de date (ou on le filtre aussi, généralement on le laisse pour avoir une vue rapide)
        $latestRequests = $em->getRepository(ProjectRequest::class)->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->render('admin/dashboard/project_requests_widget.html.twig', [
            'stats' => $stats,
            'latest_requests' => $latestRequests,
            'current_period' => $period,
        ]);
    }
}
