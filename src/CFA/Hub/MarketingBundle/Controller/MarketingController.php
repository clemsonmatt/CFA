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
        return $this->render('CFAHubMarketingBundle:Marketing:index.html.twig');
    }
}
