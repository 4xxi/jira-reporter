<?php

namespace App\Twig;

class JiraTimeFormatExtension extends \Twig_Extension
{
    /**
     * @var int
     */
    private $hoursInDay;

    /**
     * @var int
     */
    private $daysInWeek;

    /**
     * JiraTimeFormatExtension constructor.
     *
     * @param int $houresInDay
     * @param int $daysInWeek
     */
    public function __construct()
    {
        $this->hoursInDay = 8;
        $this->daysInWeek = 5;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('jiraFullTime', array($this, 'jiraFullFormatFilter')),
            new \Twig_SimpleFilter('jiraHours', array($this, 'jiraHoursFilter'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('jiraStatus', array($this, 'jiraStatusFilter')),
            new \Twig_SimpleFilter('priority', array($this, 'priority')),
        );
    }

    /**
     * @param $status
     * @param bool $reverse
     * @param array $delimiters
     * @return string
     */
    public function priority($status, $reverse = false, $delimiters = [20, 40, 60])
    {
        if ($status > 100) {
            $status = 100;
        }
        if ($reverse) {
            $status = 100-$status;
        }
        if ($status <= $delimiters[0]) {
            $class = 'bg-success';
        } elseif ($status < $delimiters[1]) {
            $class = 'bg-info';
        } elseif ($status < $delimiters[2]) {
            $class = 'bg-warning';
        } else {
            $class = 'bg-danger';
        }
        return $class;
    }
    
    /**
     * @param string $status
     *
     * @return string
     */
    public function jiraStatusFilter($status)
    {
        switch ($status) {
            case 'In Progress':
                return '<span class="label label-warning">'.$status.'</span>';
                break;
            case 'Resolved':
            case 'Closed':
                return '<span class="label label-success">'.$status.'</span>';
                break;
            default:
                return '<span class="label label-primary">'.$status.'</span>';
                break;

        }
        return '<span class="label label-Primary">'.$status.'</span>';
    }

    /**
     * @param int $number
     *
     * @return int|string
     */
    public function jiraFullFormatFilter($number)
    {
        if ($number === 0) {
            return 0;
        }

        $secondsInWeek = 60 * 60 * $this->hoursInDay * $this->daysInWeek;

        $weeks = floor($number / $secondsInWeek);

        $secondsInDay = 60 * 60 * $this->hoursInDay;

        $days = floor(($number - $weeks * $secondsInWeek) / $secondsInDay);

        $secondsInHour = 60 * 60;

        $hours = floor(($number - $weeks * $secondsInWeek - $days * $secondsInDay) / $secondsInHour);

        $secondsInMinute = 60;

        $minutes = floor(($number - $weeks * $secondsInWeek - $days * $secondsInDay - $hours * $secondsInHour) / $secondsInMinute);

        $result = [];

        if ($weeks > 0) {
            $result[] = $weeks.'w';
        }

        if ($days > 0) {
            $result[] = $days.'d';
        }

        if ($hours > 0) {
            $result[] = $hours.'h';
        }

        if ($minutes > 0) {
            $result[] = $minutes.'m';
        }

        return implode(' ', $result);
    }

    /**
     * @param $number
     *
     * @return int|string
     */
    public function jiraHoursFilter($number)
    {
        if ($number === 0) {
            return 0;
        }

        $secondsInHour = 60 * 60;

        $hours = floor($number / $secondsInHour);

        $secondsInMinute = 60;

        $minutes = floor(($number - $hours * $secondsInHour) / $secondsInMinute);

        $result = [];

        if ($hours > 0) {
            $result[] = $hours.'h';
        }

        if ($minutes > 0) {
            $result[] = $minutes.'m';
        }

        return implode(" ", $result);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jira_time_format_extension';
    }
}
