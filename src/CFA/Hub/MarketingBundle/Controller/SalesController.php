<?php

namespace CFA\Hub\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use CFA\Hub\SharedBundle\Entity\Customer;
use CFA\Hub\SharedBundle\Entity\Order;
use CFA\Hub\SharedBundle\Entity\Product;
use CFA\Hub\SharedBundle\Entity\Sale;

/**
 * @Route("/sales")
 * @Security("has_role('ROLE_CFA_MARKETING')")
 */
class SalesController extends Controller
{
    /**
     * @Route("/", name="cfa_hub_marketing_sales_index")
     */
    public function indexAction()
    {
        $now    = new \DateTime("now");
        $future = new \DateTime("+3 day");

        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Sale');
        $sales      = $repository->createQueryBuilder('s')
            ->where('s.pickupDate >= :now')
            ->andWhere('s.pickupDate < :futureTime')
            ->setParameter('now', $now)
            ->setParameter('futureTime', $future)
            ->getQuery()
            ->getResult();

        return $this->render('CFAHubMarketingBundle:Sales:index.html.twig', [
            'sales' => $sales,
        ]);
    }

    /**
     * @Route("/{customer}/add", name="cfa_hub_marketing_sales_add")
     */
    public function addSaleAction(Customer $customer, Request $request)
    {
        $sale = new Sale();

        $form = $this->createForm('cfa_sale', $sale);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $sale->setCustomer($customer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($sale);

            /* convert time */
            $time = $sale->getPickupTime();
            $time = new \DateTime($time);
            $sale->setPickupTime($time);

            /* add the orders */
            foreach ($form['orders']->getData() as $order) {
                $newOrder = new Order();
                $newOrder->setCustomer($customer);
                $newOrder->setProduct($order);
                $newOrder->setSale($sale);

                $newOrder->setQty($request->request->get('orders_'.$order->getId().'_qty'));

                $specialRequest = ($request->request->get('orders_'.$order->getId().'_special') == "" ? null : $request->request->get('orders_'.$order->getId().'_special'));
                $newOrder->setSpecialRequest($specialRequest);

                $em->persist($newOrder);
            }

            $em->flush();

            $this->addFlash('success', 'Sale added');
            return $this->redirectToRoute('cfa_hub_marketing_customer_show', [
                'customer' => $customer->getId(),
            ]);
        }

        return $this->render('CFAHubMarketingBundle:Sales:add.html.twig', [
            'form'        => $form->createView(),
            'customer'    => $customer,
            'new_product' => new Product(),
        ]);
    }

    /**
     * @Route("/{customer}/{sale}/edit", name="cfa_hub_marketing_sales_edit")
     */
    public function editSaleAction(Customer $customer, Sale $sale, Request $request)
    {
        $sale->setPickupTime($sale->getPickupTime()->format('h:i A'));

        $form = $this->createForm('cfa_sale', $sale);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /* remove existing orders */
            foreach ($sale->getOrders() as $order) {
                $em->remove($order);
            }
            $em->flush();

            /* convert time */
            $time = $sale->getPickupTime();
            $time = new \DateTime($time);
            $sale->setPickupTime($time);

            /* add the orders */
            foreach ($form['orders']->getData() as $order) {
                $newOrder = new Order();
                $newOrder->setCustomer($customer);
                $newOrder->setProduct($order);
                $newOrder->setSale($sale);

                $newOrder->setQty($request->request->get('orders_'.$order->getId().'_qty'));

                $specialRequest = ($request->request->get('orders_'.$order->getId().'_special') == "" ? null : $request->request->get('orders_'.$order->getId().'_special'));
                $newOrder->setSpecialRequest($specialRequest);

                $em->persist($newOrder);
            }

            $em->flush();

            $this->addFlash('success', 'Sale saved.');
            return $this->redirectToRoute('cfa_hub_marketing_customer_show', [
                'customer' => $customer->getId(),
            ]);
        }

        return $this->render('CFAHubMarketingBundle:Sales:add.html.twig', [
            'form'        => $form->createView(),
            'customer'    => $customer,
            'new_product' => new Product(),
            'sale'        => $sale,
        ]);
    }

    /**
     * @Route("/{sale}/show", name="cfa_hub_marketing_sales_show")
     */
    public function showAction(Sale $sale)
    {
        return $this->render('CFAHubMarketingBundle:Sales:show.html.twig', [
            'sale' => $sale,
        ]);
    }

    /**
     * @Route("/search-date", name="cfa_hub_marketing_sales_date_search")
     */
    public function dateSearchAction()
    {
        $startDate = $this->get('request')->request->get('startDate');
        $endDate   = $this->get('request')->request->get('endDate');

        $startDate = new \DateTime($startDate);
        $endDate   = new \DateTime($endDate);

        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Sale');
        $sales      = $repository->createQueryBuilder('s')
            ->where('s.pickupDate >= :startDate')
            ->andWhere('s.pickupDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('s.pickupDate', 'asc')
            ->orderBy('s.pickupTime', 'asc')
            ->getQuery()
            ->getResult();

        return $this->render('CFAHubMarketingBundle:Sales:search.html.twig', [
            'sales'      => $sales,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }

    /**
     * @Route("/{startDate}/{endDate}/print-date-range", name="cfa_hub_marketing_sales_printable_date_range")
     */
    public function printDateRangeAction($startDate, $endDate)
    {
        $startDate = new \DateTime($startDate);
        $endDate   = new \DateTime($endDate);

        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Sale');
        $sales      = $repository->createQueryBuilder('s')
            ->where('s.pickupDate >= :startDate')
            ->andWhere('s.pickupDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('s.pickupDate', 'asc')
            ->orderBy('s.pickupTime', 'asc')
            ->getQuery()
            ->getResult();

        return $this->render('CFAHubMarketingBundle:Sales:printDateRange.html.twig', [
            'sales'      => $sales,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }
}
