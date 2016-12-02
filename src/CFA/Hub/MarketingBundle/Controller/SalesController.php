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
    public function indexAction(Request $request)
    {
        $startDate = $request->request->get('startDate');
        $endDate   = $request->request->get('endDate');

        $sales = null;

        if ($startDate !== null && $endDate !== null) {
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
        }

        if ($startDate > $endDate) {
            $this->addFlash('error', 'End date must be after start date.');
            $startDate = null;
            $endDate   = null;
        }

        return $this->render('CFAHubMarketingBundle:Sales:search.html.twig', [
            'sales'      => $sales,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }

    /**
     * @Route("/{sale}/show", name="cfa_hub_marketing_sales_show")
     */
    public function showAction(Sale $sale)
    {
        $orderForm    = $this->createForm('cfa_marketing_order');
        $customerForm = $this->createForm('cfa_customer', $sale->getCustomer());
        $saleForm     = $this->createForm('cfa_marketing_sale');

        return $this->render('CFAHubMarketingBundle:Sales:show.html.twig', [
            'sale'          => $sale,
            'order_form'    => $orderForm->createView(),
            'customer_form' => $customerForm->createView(),
            'sale_form'     => $saleForm->createView(),
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
        $order->setSpecialRequest(($parameters['specialRequest'] == "" ? null : $parameters['specialRequest']));
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

        /* generate url to remove order from sale */
        $removeOrderUrl = $this->generateUrl('cfa_hub_marketing_sales_remove_from_sale', [
            'sale'  => $sale->getId(),
            'order' => $order->getId(),
        ]);

        return new JsonResponse([
            'success' => true,
            'data'    => [
                'productName'    => (string)$order->getProduct(),
                'productPrice'   => $order->getProduct()->getPrice(),
                'qty'            => $order->getQty(),
                'total'          => $order->getProduct()->getPrice() * $order->getQty(),
                'comments'       => $order->getSpecialRequest(),
                'removeOrderUrl' => $removeOrderUrl,
            ],
        ], 200);
    }

    /**
     * @Route("/{sale}/{order}/remove-from-sale", name="cfa_hub_marketing_sales_remove_from_sale")
     */
    public function removeFromSaleAction(Sale $sale, Order $order)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        $this->addFlash('warning', 'Order removed from sale.');
        return $this->redirectToRoute('cfa_hub_marketing_sales_show', [
            'sale' => $sale->getId(),
        ]);
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

        if (! $customer) {
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

        $this->addFlash('success', 'Contact saved.');

        return $this->redirectToRoute('cfa_hub_marketing_sales_show', [
            'sale' => $sale->getId(),
        ]);
    }

    /**
     * @Route("/{sale}/sale-details", name="cfa_hub_marketing_sales_sale_details")
     */
    public function saleDetailsAction(Sale $sale, Request $request)
    {
        $validator = $this->get('validator');

        $parameters = $request->request->get('cfa_marketing_sale');

        $pickupDate = new \DateTime($parameters['pickupDate']);
        $pickupTime = new \DateTime($parameters['pickupTime']);

        $sale->setPickupDate($pickupDate);
        $sale->setPickupTime($pickupTime);
        $sale->setComments($parameters['comments']);

        $errors = $validator->validate($sale);

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

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('success', 'Sale details saved.');

        return $this->redirectToRoute('cfa_hub_marketing_sales_show', [
            'sale' => $sale->getId(),
        ]);
    }

    /**
     * @Route("/{sale}/remove", name="cfa_hub_marketing_sales_remove")
     */
    public function removeAction(Sale $sale)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($sale);
        $em->flush();

        $this->addFlash('warning', 'Sale removed.');
        return $this->redirectToRoute('cfa_hub_marketing_index');
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
