<?php

namespace CFA\EventRegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use CFA\EventRegisterBundle\Entity\Event;
use CFA\EventRegisterBundle\Entity\Menu;

/**
 * @Route("/manage")
 */
class ManageController extends Controller
{
    /**
     * @Route("/", name="cfa_event_manage_index")
     */
    public function indexAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAEventRegisterBundle:Event');
        $events     = $repository->findAll();

        $repository = $em->getRepository('CFAEventRegisterBundle:Menu');
        $menuItems  = $repository->findAll();

        return $this->render('CFAEventRegisterBundle:Manage:index.html.twig', [
            'events'     => $events,
            'menu_items' => $menuItems,
        ]);
    }

    /**
     * @Route("/event/{event}/show", name="cfa_event_manage_event_show")
     */
    public function showAction(Event $event)
    {
        $totalCost   = 0;
        $totalSales  = 0;
        $lowestSale  = 0;
        $highestSale = 0;

        foreach ($event->getTransactions() as $transaction) {
            $totalCost += $transaction->getTotal();
            $totalSales += $transaction->getMoneyRecieved();

            if ($transaction->getTotal() > $highestSale) {
                $highestSale = $transaction->getTotal();
            }
            if ($lowestSale == 0 || $transaction->getTotal() < $lowestSale) {
                $lowestSale = $transaction->getTotal();
            }
        }

        $itemBreakdown = [];
        foreach ($event->getMenuItems() as $item) {
            $itemBreakdown[$item] = 0;
        }

        $itemCount        = 0;
        $lowestItemCount  = 0;
        $highestItemCount = 0;
        foreach ($event->getTransactions() as $transaction) {
            foreach ($transaction->getItems() as $item) {
                $itemBreakdown[$item->getTitle()] += 1;
                $itemCount++;
            }

            $transactionItemCount = count($transaction->getItems());
            if ($transactionItemCount > $highestItemCount) {
                $highestItemCount = $transactionItemCount;
            }
            if ($lowestItemCount == 0 || $transactionItemCount < $lowestItemCount) {
                $lowestItemCount = $transactionItemCount;
            }
        }

        return $this->render('CFAEventRegisterBundle:Manage:show.html.twig', [
            'event'              => $event,
            'total_cost'         => $totalCost,
            'total_sales'        => $totalSales,
            'lowest_sale'        => $lowestSale,
            'highest_sale'       => $highestSale,
            'item_breakdown'     => $itemBreakdown,
            'avg_item_count'     => (count($event->getTransactions()) == 0 ? 0 : ($itemCount / count($event->getTransactions()))),
            'lowest_item_count'  => $lowestItemCount,
            'highest_item_count' => $highestItemCount,
        ]);
    }

    /**
     * @Route("/event/add", name="cfa_event_manage_event_add")
     */
    public function addEventAction(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAEventRegisterBundle:Menu');
        $menuItems  = $repository->findAll();

        $event = new Event();
        $event->setDate(new \DateTime('now'));
        $form  = $this->createForm('event', $event, [
            'menu_items' => $menuItems,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Event created');
            return $this->redirectToRoute('cfa_event_manage_index');
        }

        return $this->render('CFAEventRegisterBundle:Manage:addEditEvent.html.twig', [
            'form'     => $form->createView(),
            'add_edit' => 'Add',
        ]);
    }

    /**
     * @Route("/event/{event}/edit", name="cfa_event_manage_event_edit")
     */
    public function editEventAction(Event $event, Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAEventRegisterBundle:Menu');
        $menuItems  = $repository->findAll();

        $form  = $this->createForm('event', $event, [
            'menu_items' => $menuItems,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Event saved');
            return $this->redirectToRoute('cfa_event_manage_index');
        }

        return $this->render('CFAEventRegisterBundle:Manage:addEditEvent.html.twig', [
            'form'     => $form->createView(),
            'add_edit' => 'Edit',
            'event'    => $event,
        ]);
    }

    /**
     * @Route("/menu/add", name="cfa_event_manage_menu_add")
     */
    public function addMenuAction(Request $request)
    {
        $menuItem = new Menu();
        $form     = $this->createForm('menu', $menuItem);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /* upload image */
            if ($form['picture']->getData() != null) {
                $picture     = $form['picture']->getData();
                $extension   = $picture->guessExtension();
                $pictureName = str_replace('.'.$extension, "", $picture->getClientOriginalName());
                $picturePath = $pictureName.'-'.time().'.'.$extension;
                $picture->move('uploads', $picturePath);
                $menuItem->setImage($picturePath);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($menuItem);
            $em->flush();

            $this->addFlash('success', 'Menu item created');
            return $this->redirectToRoute('cfa_event_manage_index');
        }

        return $this->render('CFAEventRegisterBundle:Manage:addEditMenuItem.html.twig', [
            'form'     => $form->createView(),
            'add_edit' => 'Add',
        ]);
    }

    /**
     * @Route("/menu/{menu}/edit", name="cfa_event_manage_menu_edit")
     */
    public function editMenuAction(Menu $menu, Request $request)
    {
        $form = $this->createForm('menu', $menu);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /* upload image */
            if ($form['picture']->getData() != null) {
                $picture     = $form['picture']->getData();
                $extension   = $picture->guessExtension();
                $pictureName = str_replace('.'.$extension, "", $picture->getClientOriginalName());
                $picturePath = $pictureName.'-'.time().'.'.$extension;
                $picture->move('uploads', $picturePath);
                $menu->setImage($picturePath);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Menu item created');
            return $this->redirectToRoute('cfa_event_manage_index');
        }

        return $this->render('CFAEventRegisterBundle:Manage:addEditMenuItem.html.twig', [
            'form'     => $form->createView(),
            'add_edit' => 'Edit',
        ]);
    }
}
