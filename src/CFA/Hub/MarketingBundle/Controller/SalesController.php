<?php

namespace CFA\Hub\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/{sale}/show", name="cfa_hub_marketing_sales_show")
     */
    public function showAction(Sale $sale)
    {
        $orderForm    = $this->createForm('cfa_marketing_order');
        $customerForm = $this->createForm('cfa_customer', $sale->getCustomer());

        return $this->render('CFAHubMarketingBundle:Sales:show.html.twig', [
            'sale'          => $sale,
            'order_form'    => $orderForm->createView(),
            'customer_form' => $customerForm->createView(),
        ]);
    }

    /**
     * @Route("/add", name="cfa_hub_marketing_sales_add")
     */
    public function addSaleAction(Request $request)
    {
        $sale = new Sale();

        $em = $this->getDoctrine()->getManager();
        $em->persist($sale);
        $em->flush($sale);

        return $this->redirectToRoute('cfa_hub_marketing_sales_show', [
            'sale' => $sale->getId(),
        ]);
    }

    /**
     * @Route("/{sale}/add-to-sale", name="cfa_hub_marketing_sales_add_to_sale")
     */
    public function addItemToSaleAction(Sale $sale, Request $request)
    {
        $validator = $this->get('validator');

        $parameters = $request->request->get('cfa_marketing_order');
        // dump($parameters);

        /* check for errors */
        // $keysArray    = ['something'];
        // $errorStrings = [];

        // foreach ($keysArray as $key) {
        //     if (! array_key_exists($key, $parameters) && $parameters[$key] !== null) {
        //         $errorStrings[] = 'Value for '.$key.' cannot be null';
        //     }
        // }

        // if (count($errorStrings)) {
        //     return new JsonResponse([
        //         'success' => false,
        //         'errors'  => implode(', ', $errorStrings),
        //     ], 400);
        // }

        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Product');
        $product    = $repository->findOneById($parameters['product']);

        $order = new Order();
        $order->setProduct($product);
        $order->setQty($parameters['qty']);
        $order->setSpecialRequest($parameters['specialRequest']);
        $order->setSale($sale);

        /* validate */
        $errors = $validator->validate($order);

        if (count($errors)) {
            $errorStrings = [];

            foreach ($errors as $error) {
                $errorStrings[] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors'  => implode(', ', $errorStrings),
            ], 400);
        }

        $em->persist($order);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'data'    => [
                // 'parameters' => $parameters,
                'productName'  => (string)$order->getProduct(),
                'productPrice' => $order->getProduct()->getPrice(),
                'qty'          => $order->getQty(),
                'total'        => $order->getProduct()->getPrice() * $order->getQty(),
                'comments'     => $order->getSpecialRequest(),
            ],
        ], 200);
    }

    /**
     * @Route("/{sale}/add-customer", name="cfa_hub_marketing_sales_customer_add")
     */
    public function addCustomerAction(Sale $sale, Request $request)
    {
        $validator = $this->get('validator');

        $parameters = $request->request->get('cfa_customer');

        /* try to find an existing customer first */
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Customer');
        $customer   = $repository->findOneByPhone($parameters['phone']);

        if ($customer) {
            $this->addFlash('info', 'Customer was updated.');
        } else {
            $customer = new Customer();
        }

        $customer->setFirstName(($parameters['firstName'] == "" ? null : $parameters['firstName']));
        $customer->setLastName(($parameters['lastName'] == "" ? null : $parameters['lastName']));
        $customer->setCompanyName(($parameters['companyName'] == "" ? null : $parameters['companyName']));
        $customer->setEmail(($parameters['email'] == "" ? null : $parameters['email']));
        $customer->setPhone($parameters['phone']);
        $em->persist($customer);

        $errors = $validator->validate($customer);

        if (count($errors)) {
            $errorStrings = [];

            foreach ($errors as $error) {
                $errorStrings[] = $error->getMessage();
            }

            $this->addFlash('error', implode(", ", $errorStrings));

            return $this->redirectToRoute('cfa_hub_marketing_sales_show', [
                'sale' => $sale->getId(),
            ]);
        }

        $sale->setCustomer($customer);

        $em->flush();

        return $this->redirectToRoute('cfa_hub_marketing_sales_show', [
            'sale' => $sale->getId(),
        ]);
    }

    // /**
    //  * @Route("/{customer}/add", name="cfa_hub_marketing_sales_add")
    //  */
    // public function addSaleAction(Customer $customer, Request $request)
    // {
    //     $sale = new Sale();

    //     $form = $this->createForm('cfa_sale', $sale);

    //     $form->handleRequest($request);

    //     if ($form->isValid()) {
    //         $sale->setCustomer($customer);

    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($sale);

    //         /* convert time */
    //         $time = $sale->getPickupTime();
    //         $time = new \DateTime($time);
    //         $sale->setPickupTime($time);

    //         /* add the orders */
    //         foreach ($form['orders']->getData() as $order) {
    //             $newOrder = new Order();
    //             $newOrder->setCustomer($customer);
    //             $newOrder->setProduct($order);
    //             $newOrder->setSale($sale);

    //             $newOrder->setQty($request->request->get('orders_'.$order->getId().'_qty'));

    //             $specialRequest = ($request->request->get('orders_'.$order->getId().'_special') == "" ? null : $request->request->get('orders_'.$order->getId().'_special'));
    //             $newOrder->setSpecialRequest($specialRequest);

    //             $em->persist($newOrder);
    //         }

    //         $em->flush();

    //         $this->addFlash('success', 'Sale added');
    //         return $this->redirectToRoute('cfa_hub_marketing_customer_show', [
    //             'customer' => $customer->getId(),
    //         ]);
    //     }

    //     return $this->render('CFAHubMarketingBundle:Sales:add.html.twig', [
    //         'form'        => $form->createView(),
    //         'customer'    => $customer,
    //         'new_product' => new Product(),
    //     ]);
    // }

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

    // /**
    //  * @Route("/{sale}/show", name="cfa_hub_marketing_sales_show")
    //  */
    // public function showAction(Sale $sale)
    // {
    //     return $this->render('CFAHubMarketingBundle:Sales:show.html.twig', [
    //         'sale' => $sale,
    //     ]);
    // }

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
