<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    
    /**
     * @Route("/", name="homepage")
     * @Route("/{period}", name="time_report", defaults={"period"="month"}, requirements={
     *     "period"="today|yesterday|7days|current_week|last_week|30days|last_month|current_month"
     * })
     * @Template("default/index.html.twig")
     */
    public function index($period = '30days')
    {
        switch ($period) {
            case 'today':
                $startDate = new \DateTime();
                $endDate   = new \DateTime();
                break;

            case 'yesterday':
                $startDate = new \DateTime('1 day ago');
                $endDate   = new \DateTime();
                break;

            case '7days':
                $startDate = new \DateTime('7 days ago');
                $endDate   = new \DateTime();
                break;

            case 'last_week':
                $startDate = new \DateTime('last week monday');
                $endDate   = new \DateTime('last week sunday');
                break;

            case 'current_week':
                $startDate = new \DateTime('monday this week');
                $endDate   = new \DateTime('sunday this week');
                break;

            case 'last_month':
                $startDate = new \DateTime('first day of last month');
                $endDate   = new \DateTime('last day of last month');
                break;

            case 'current_month':
                $startDate = new \DateTime('first day of this month');
                $endDate   = new \DateTime('last day of this month');
                break;
            
            default:
                $startDate = new \DateTime('1 month ago');
                $endDate   = new \DateTime();
                break;
        }
        
        $numberOfWorkingDays = $this->numberOfWorkingDays($startDate, $endDate);
        
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($startDate, $interval, $endDate);
        
        $timeTable = $this->getDoctrine()->getRepository('App:Jira\Worklog')->getTimeTable($startDate, $endDate);
        
        $persons = $this->process($timeTable);
        $totals  = $this->findTotal($persons);
        
        return [
            'period'    => $period,
            'persons'   => $persons,
            'totals'    => $totals,
            'workingTime'  => $numberOfWorkingDays,
            'workingTimeinMs'  => $numberOfWorkingDays*8*60*60,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ];
    }
    
    private function process($timeTable)
    {
        $persons = [];
        foreach ($timeTable as $obj) {
            $username = $obj['username'];
            if (!isset($persons[$username])) {
                $persons[$username] = [];
            }
            $persons[$username][$obj['created']] = $obj['timeSpent'];
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
