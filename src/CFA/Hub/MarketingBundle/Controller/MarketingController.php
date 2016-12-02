<?php

namespace CFA\Hub\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/")
 * @Security("has_role('ROLE_CFA_MARKETING')")
 */
class MarketingController extends Controller
{
    /**
     * @Route("/", name="cfa_hub_marketing_index")
     */
    public function indexAction()
    {
        $now    = new \DateTime("now");
        $future = new \DateTime("+3 day");

        $em             = $this->getDoctrine()->getManager();
        $repository     = $em->getRepository('CFAHubSharedBundle:Sale');
        $upcommingSales = $repository->createQueryBuilder('s')
            ->where('s.pickupDate >= :now')
            ->andWhere('s.pickupDate < :futureTime')
            ->setParameter('now', $now)
            ->setParameter('futureTime', $future)
            ->getQuery()
            ->getResult();

        $recentSales = $repository->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();


        /**
         *  select c.id, c.first_name, c.last_name, c.company_name, SUM(p.price * o.qty) as totalPrice from customer c
         *  join sale s on s.customer_id = c.id
         *  join `order` o on o.sale_id = s.id
         *  join product p on o.product_id = p.id
         *  group by c.id
         *  order by totalPrice;
         */
        $repository = $em->getRepository('CFAHubSharedBundle:Customer');
        $topCustomers = $repository->createQueryBuilder('c')
            ->select('c as customer, SUM(p.price * o.qty) as totalPrice')
            ->join('CFAHubSharedBundle:Sale', 's', 'WITH', 's.customer = c')
            ->join('s.orders', 'o')
            ->join('o.product', 'p')
            ->groupBy('c.id')
            ->orderBy('totalPrice', 'DESC')
            ->getQuery()
            ->getResult();

        $topCustomersArray = [];
        foreach ($topCustomers as $topCustomer) {
            $topCustomersArray[(string)$topCustomer['customer']] = $topCustomer['totalPrice'];
        }

        return $this->render('CFAHubMarketingBundle:Marketing:index.html.twig', [
            'upcomming_sales' => $upcommingSales,
            'recent_sales'    => $recentSales,
            'top_customers'   => $topCustomersArray,
        ]);
    }
}
