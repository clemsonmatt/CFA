<?php

namespace CFA\EventRegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use CFA\EventRegisterBundle\Entity\Event;
use CFA\EventRegisterBundle\Entity\Transaction;

/**
 * @Route("/")
 */
class RegisterController extends Controller
{
    /**
     * @Route("/", name="cfa_event_index")
     */
    public function indexAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CFAEventRegisterBundle:Event');
        $events     = $repository->findAll();

        return $this->render('CFAEventRegisterBundle:Register:index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/{event}/show", name="cfa_event_show")
     */
    public function showAction(Event $event, Request $request)
    {
        $em            = $this->getDoctrine()->getManager();
        $repository    = $em->getRepository('CFAEventRegisterBundle:Menu');
        $menuEntres    = $repository->findBy([
            'type'  => ['Entre', 'Combo'],
            'title' => $event->getMenuItems(),
        ]);
        $menuSides     = $repository->findBy([
            'type'  => 'Side',
            'title' => $event->getMenuItems(),
        ]);
        $menuBeverages = $repository->findBy([
            'type'  => 'Beverage',
            'title' => $event->getMenuItems(),
        ]);

        $transaction = new Transaction();
        $transaction->setEvent($event);

        $form = $this->createForm('menuItem', $transaction);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $items = $repository->findByTitle(array_values($transaction->getItems()));

            $itemsList = [];
            foreach ($transaction->getItems() as $item) {
                foreach ($items as $i) {
                    if ($item == $i->getTitle()) {
                        $itemsList[] = $i;
                    }
                }
            }
            $transaction->setItems(array_values($itemsList));

            $total = 0;
            foreach ($itemsList as $item) {
                $total += $item->getPrice();
            }
            $transaction->setTotal($total);

            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('cfa_event_checkout', [
                'event'       => $event->getId(),
                'transaction' => $transaction->getId()
            ]);
        }

        return $this->render('CFAEventRegisterBundle:Register:show.html.twig', [
            'event'          => $event,
            'menu_entres'    => $menuEntres,
            'menu_sides'     => $menuSides,
            'menu_beverages' => $menuBeverages,
            'form'           => $form->createView(),
        ]);
    }

    /**
     * @Route("/{event}/checkout/{transaction}", name="cfa_event_checkout")
     */
    public function checkoutAction(Event $event, Transaction $transaction, Request $request)
    {
        $form = $this->createForm('transaction', $transaction);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('cfa_event_show', ['event' => $event->getId()]);
        }

        return $this->render('CFAEventRegisterBundle:Register:checkout.html.twig', [
            'event'       => $event,
            'transaction' => $transaction,
            'form'        => $form->createView(),
        ]);
    }

    /**
     * @Route("/{transaction}/discard", name="cfa_event_discard")
     */
    public function discardAction(Transaction $transaction)
    {
        $event = $transaction->getEvent();

        $em = $this->getDoctrine()->getManager();
        $em->remove($transaction);
        $em->flush();

        return $this->redirectToRoute('cfa_event_show', ['event' => $event->getId()]);
    }
}
