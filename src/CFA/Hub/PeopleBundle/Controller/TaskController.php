<?php

namespace CFA\Hub\PeopleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use CFA\PeopleBundle\Entity\Task;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/add")
     */
    public function addAction(Request $request)
    {
        $task = new Task();

        $form = $this->createForm('task_form', $task);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Task added');
        }

        return $this->render('CFAHubSharedBundle:Task:addEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
