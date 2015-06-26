<?php

namespace CFA\Hub\PeopleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="cfa_hub_people_dashboard")
     */
    public function indexAction()
    {
        $calendar = $this->getCalendar();

        $em              = $this->getDoctrine()->getManager();
        $repository      = $em->getRepository('CFAHubPeopleBundle:Task');
        $unfinishedTasks = $repository->findByCompleted(false);

        return $this->render('CFAHubPeopleBundle:Dashboard:index.html.twig', [
            'calendar'         => $calendar,
            'unfinished_tasks' => $unfinishedTasks,
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
