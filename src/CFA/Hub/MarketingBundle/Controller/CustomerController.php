<?php

namespace CFA\Hub\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use CFA\Hub\SharedBundle\Entity\Customer;

/**
 * @Route("/customer")
 * @Security("has_role('ROLE_CFA_MARKETING')")
 */
class CustomerController extends Controller
{
    /**
     * @Route("/", name="cfa_hub_marketing_customer_index")
     */
    public function indexAction()
    {
        $em              = $this->getDoctrine()->getManager();
        $repository      = $em->getRepository('CFAHubSharedBundle:Customer');
        $personCustomers = $repository->createQueryBuilder('c')
            ->where('c.companyName IS NULL')
            ->orderBy('c.lastName', 'ASC')
            ->getQuery()
            ->getResult();

        $companyCustomers = $repository->createQueryBuilder('c')
            ->where('c.companyName IS NOT NULL')
            ->orderBy('c.companyName', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('CFAHubMarketingBundle:Customer:index.html.twig', [
            'person_customers'  => $personCustomers,
            'company_customers' => $companyCustomers,
        ]);
    }

    /**
     * @Route("/{customer}/show", name="cfa_hub_marketing_customer_show")
     */
    public function showAction(Customer $customer)
    {
        return $this->render('CFAHubMarketingBundle:Customer:show.html.twig', [
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/add", name="cfa_hub_marketing_customer_add")
     */
    public function addAction(Request $request)
    {
        $customer = new Customer();

        $form = $this->createForm('cfa_customer', $customer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->addFlash('success', 'Customer added');
            return $this->redirectToRoute('cfa_hub_marketing_sales_add', [
                'customer' => $customer->getId(),
            ]);
        }

        return $this->render('CFAHubMarketingBundle:Customer:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
