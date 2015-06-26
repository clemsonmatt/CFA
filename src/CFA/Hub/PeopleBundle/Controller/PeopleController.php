<?php

namespace CFA\Hub\PeopleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use CFA\Hub\SharedBundle\Entity\Person;

/**
 * @Route("/people")
 */
class PeopleController extends Controller
{
    /**
     * @Route("/", name="cfa_hub_people_index")
     */
    public function indexAction()
    {
        $em          = $this->getDoctrine()->getManager();
        $repository  = $em->getRepository('CFAHubSharedBundle:Person');
        $management  = $repository->findByPosition(['Director', 'Manager', 'Supervisor', 'Team Leader'], ['lastName' => 'ASC']);
        $teamMembers = $repository->findByPosition(['Team Member'], ['lastName' => 'ASC']);


        return $this->render('CFAHubPeopleBundle:People:index.html.twig', [
            'management'   => $management,
            'team_members' => $teamMembers,
        ]);
    }

    /**
     * @Route("/{person}/show", name="cfa_hub_people_show")
     */
    public function showAction(Person $person)
    {
        $calendar = $this->getCalendar();

        return $this->render('CFAHubPeopleBundle:People:show.html.twig', [
            'person'   => $person,
            'calendar' => $calendar,
        ]);
    }

    /**
     * @Route("/add", name="cfa_hub_people_add")
     * @Security("has_role('ROLE_CFA_ADMIN')")
     */
    public function addAction(Request $request)
    {
        $person = new Person();
        $person->setEmailPrivate(true);
        $person->setPhonePrivate(true);
        $person->setActive(true);

        $form = $this->createForm('person_form', $person, [
            'show_all' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /* upload profile picture */
            if ($form['picture']->getData() !== null) {
                $picture      = $form['picture']->getData();
                $extension    = $picture->guessExtension();
                $orgName      = str_replace(" ", "", $picture->getClientOriginalName());
                $orgName      = str_replace("#", "", $orgName);
                $picture_name = str_replace('.'.$extension, "", $orgName);
                $picture_path = $picture_name.'-'.time().'.'.$extension;
                $picture->move('uploads', $picture_path);
                $person->setPicture($picture_path);
            }

            /* encode password with bcrypt */
            $factory  = $this->get('security.encoder_factory');
            $encoder  = $factory->getEncoder($person);
            $password = $encoder->encodePassword($form['password']->getData(), $person->getSalt());
            $person->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            $this->addFlash('success', 'Employee created');
            return $this->redirectToRoute('cfa_hub_people_show', [
                'person' => $person->getId(),
            ]);
        }

        return $this->render('CFAHubPeopleBundle:People:addEdit.html.twig', [
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @Route("/{person}/edit", name="cfa_hub_people_edit")
     * @Security("has_role('ROLE_CFA_ADMIN')")
     */
    public function editAction(Person $person, Request $request)
    {
        $form = $this->createForm('person_form', $person, [
            'show_all' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /* upload profile picture */
            if ($form['picture']->getData() !== null) {
                $picture      = $form['picture']->getData();
                $extension    = $picture->guessExtension();
                $orgName      = str_replace(" ", "", $picture->getClientOriginalName());
                $orgName      = str_replace("#", "", $orgName);
                $picture_name = str_replace('.'.$extension, "", $orgName);
                $picture_path = $picture_name.'-'.time().'.'.$extension;
                $picture->move('uploads', $picture_path);
                $person->setPicture($picture_path);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Employee saved');
            return $this->redirectToRoute('cfa_hub_people_show', [
                'person' => $person->getId(),
            ]);
        }

        return $this->render('CFAHubPeopleBundle:People:addEdit.html.twig', [
            'form'     => $form->createView(),
            'person'   => $person,
        ]);
    }

    private function getCalendar()
    {
        $month = date('n');
        $year  = date('Y');

        /* days and weeks vars now ... */
        $running_day       = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month     = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter       = 0;
        $dates_array       = [];

        /* row for week one */
        $calendar = '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for($x = 0; $x < $running_day; $x++):
            $calendar.= '<td> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for($list_day = 1; $list_day <= $days_in_month; $list_day++):
            $calendar.= '<td date-month="'.$month.'" date-day="'.$list_day.'">';
                /* add in the day number */
                /* add .event and .current-day */
                $calendar.= '<div class="day-number">'.$list_day.'</div>';

                /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
                $calendar.= str_repeat('<p> </p>', 2);

            $calendar.= '</td>';
            if($running_day == 6):
                $calendar.= '</tr>';
                if(($day_counter+1) != $days_in_month):
                    $calendar.= '<tr class="calendar-row">';
                endif;
                $running_day       = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if($days_in_this_week < 8):
            for($x = 1; $x <= (8 - $days_in_this_week); $x++):
                $calendar.= '<td> </td>';
            endfor;
        endif;

        /* final row */
        $calendar.= '</tr>';

        return $calendar;
    }
}
