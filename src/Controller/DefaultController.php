<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\Form\TimeTableIntervalType;

/**
 * @todo This controller has a lot of magic, so refactoring is required.
 */
class DefaultController extends Controller
{
    
    /**
     * @Route("/", name="homepage")
     * @Route("/{period}", name="time_report", defaults={"period"="month"}, requirements={
     *     "period"="today|yesterday|7days|current_week|last_week|30days|last_month|current_month"
     * })
     * @Template("default/index.html.twig")
     */
    public function index(Request $request, $period = '7days')
    {
        switch ($period) {
            case 'today':
                $startDate = new \DateTime('today 00:00:00');
                $endDate   = new \DateTime('today 23:59:59');
                break;

            case 'yesterday':
                $startDate = new \DateTime('1 day ago 00:00');
                $endDate   = new \DateTime('today 00:00');
                break;

            case '7days':
                $startDate = new \DateTime('7 days ago 00:00');
                $endDate   = new \DateTime('today 00:00');
                break;

            case 'last_week':
                $startDate = new \DateTime('last week monday');
                $endDate   = new \DateTime('this week monday');
                break;

            case 'current_week':
                $startDate = new \DateTime('monday this week');
                $endDate   = new \DateTime('monday next week');
                break;

            case 'last_month':
                $startDate = new \DateTime('first day of last month');
                $endDate   = new \DateTime('first day of this month');
                break;

            case 'current_month':
                $startDate = new \DateTime('first day of this month');
                $endDate   = new \DateTime('first day of next month');
                break;
            
            default:
                $startDate = new \DateTime('1 month ago');
                $endDate   = new \DateTime('today 00:00');
                break;
        }
        
        $form = $this->createForm(TimeTableIntervalType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $form->get('startDate')->getData();
            $endDate   = $form->get('endDate')->getData();
        } elseif (!$form->isSubmitted()) {
            $form->get('startDate')->setData($startDate);
            $form->get('endDate')->setData($endDate);
        }
        
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($startDate, $interval, $endDate);
        
        $timeTable = $this->getDoctrine()->getRepository('App:Jira\Worklog')->getTimeTable($startDate, $endDate);
        
        $persons = $this->process($timeTable, $startDate, $endDate);
        $totals  = $this->findTotal($persons);
        
        return [
            'period'    => $period,
            'persons'   => $persons,
            'totals'    => $totals,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'form'      => $form->createView(),
        ];
    }
    
    private function process($timeTable, $startDate, $endDate)
    {
        $holidayDays = $this->getDoctrine()->getRepository('App:Config')->getValueAsArray('holidays');
        
        $persons = [];
        foreach ($timeTable as $obj) {
            $username = $obj['username'];
            if (!isset($persons[$username])) {
                $user = $this->getDoctrine()->getRepository('App:Jira\User')->findByUsernameWithOffDays($username);
                if ($user) {
                    $user->setTimeTable($startDate, $endDate, $holidayDays);
                    $persons[$username] = $user;
                }
            }
            if (isset($persons[$username])) {
                $persons[$username]->addToTimeTable($obj['created'], $obj['timeSpent']);
            }
        }
        
        return $persons;
    }
    
    private function findTotal($persons)
    {
        $totals = [];
        foreach ($persons as $key => $person) {
            $sum = 0;
            foreach ($person as $time) {
                $sum+=$time;
            }
            $totals[$key] = $sum;
        }
        return $totals;
    }
    
    private function numberOfWorkingDays(\DateTime $from, \DateTime $to) {
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        $holidayDays = $this->getDoctrine()->getRepository('App:Config')->getValueAsArray('holidays');

        $to->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $holidayDays)) continue;
            $days++;
        }
        
        return $days;
    }
    
}
