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
     *     "period"="month|day|yesterday|week|7days"
     * })
     * @Template("default/index.html.twig")
     */
    public function index($period = 'month')
    {
        switch ($period) {
            case 'day':
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

            case 'week':
                $startDate = new \DateTime('7 days ago');
                $endDate   = new \DateTime();
                break;
            
            default:
                $startDate = new \DateTime('1 month ago');
                $endDate   = new \DateTime();
                break;
        }
        
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($startDate, $interval, $endDate);
        
        $timeTable = $this->getDoctrine()->getRepository('App:Jira\Worklog')->getTimeTable($startDate, $endDate);
        
        $persons = $this->process($timeTable);
        
        return [
            'period'    => $period,
            'persons'   => $persons,
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
    
}
