<?php

namespace CFA\Hub\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use CFA\Hub\SharedBundle\Entity\Product;
use CFA\Hub\SharedBundle\Entity\ProductGroup;

/**
 * @Route("/product")
 * @Security("has_role('ROLE_CFA_MARKETING')")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="cfa_hub_marketing_product_index")
     */
    public function indexAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Product');
        $products   = $repository->findBy([], ['category' => 'asc']);

        return $this->render('CFAHubMarketingBundle:Product:index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/add", name="cfa_hub_marketing_product_add")
     */
    public function addAction(Request $request)
    {
        $product = new Product();

        $form = $this->createForm('cfa_marketing_product', $product);

        $form->handleRequest($request);

        if ($form->isValid()) {/* upload photo */
            if ($form['photo']->getData() !== null) {
                $photo        = $form['photo']->getData();
                $extension    = $photo->guessExtension();
                $orgName      = str_replace(" ", "", $photo->getClientOriginalName());
                $orgName      = str_replace("#", "", $orgName);
                $picture_name = str_replace('.'.$extension, "", $orgName);
                $picture_path = $picture_name.'-'.time().'.'.$extension;
                $photo->move('uploads/product', $picture_path);
                $product->setPhoto($picture_path);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product added.');
            return $this->redirectToRoute('cfa_hub_marketing_product_category_show', [
                'category' => $product->getCategory(),
            ]);
        }

        return $this->render('CFAHubMarketingBundle:Product:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{category}/{product}/show-product")
     */
    public function showAction(Product $product, $category)
    {
        return $this->render('CFAHubMarketingBundle:Product:show.html.twig', [
            'product'  => $product,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{category}/show", name="cfa_hub_marketing_product_category_show")
     */
    public function showCategoryAction($category)
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Product');
        $products   = $repository->findBy(['category' => $category]);

        return $this->render('CFAHubMarketingBundle:Product:showCategory.html.twig', [
            'products'    => $products,
            'category'    => $category,
            'new_product' => new Product(),
        ]);
    }

    /**
     * @Route("/{category}/{group}/show", name="cfa_hub_marketing_product_category_group_show")
     */
    public function showCategoryGroupAction($category, $group)
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAHubSharedBundle:Product');
        $products   = $repository->findBy([
            'category' => $category,
            'group' => $group
        ], [
            'group' => 'asc',
        ]);

        return $this->render('CFAHubMarketingBundle:Product:showCategoryGroup.html.twig', [
            'products'    => $products,
            'category'    => $category,
            'group'       => $group,
            'new_product' => new Product(),
        ]);
    }
}
