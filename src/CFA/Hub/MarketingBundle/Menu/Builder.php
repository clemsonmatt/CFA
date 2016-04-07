<?php

namespace CFA\Hub\MarketingBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function marketingMenu(FactoryInterface $factory, array $options)
    {
        $securityContext = $this->container->get('security.context');

        $menu = $factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'tabs tabs-left tabs-inline',
            ]
        ]);

        $menu->addChild('Sales', [
            'route' => 'cfa_hub_marketing_sales_index',
        ]);

        $menu->addChild('Customers', [
            'route' => 'cfa_hub_marketing_customer_index',
        ]);

        $menu->addChild('Products', [
            'route' => 'cfa_hub_marketing_product_index',
        ]);

        return $menu;
    }
}
