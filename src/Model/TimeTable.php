<?php
    
namespace App\Model;

class TimeTable implements \IteratorAggregate
{
    
    private $startDate;
    private $endDate;
    private $offdays;
    private $holidayDays;
    
    private $data;
    
    public function __construct()
    {
        $this->data = [];
    }
    
    public function setInterval($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }
    
    public function setOffDays($offdays)
    {
        $this->offdays = $offdays;
    }
    
    public function setHolidays($holidayDays)
    {
        $this->holidayDays = $holidayDays;    
    }
    
    public function add($date, $value)
    {
        $this->data[$date] = $value;
    }
    
    public function getWorkingInMs()
    {
        return $this->numberOfWorkingDays()*8*60*60;
    }
    
    public function total()
    {
        $sum = 0;
        foreach ($this as $key => $value) {
            $sum+=$value;
        }
        return $sum;
    }
    
    public function getByDate($dateTime)
    {
        foreach ($this->offdays as $offday) {
            if ( ($offday->getStartDate()<=$dateTime) && ($offday->getEndDate() >= $dateTime) ) {
                // off day
            }
        }
        $date = $dateTime->format('Y-m-d');
        if (isset($this->data[$date])) {
            return $this->data[$date];
        }
        return null;
    }

    public function getByDateInHours($dateTime)
    {
        return $this->getByDate($dateTime)/3600;
    }

    
    public function getIterator() {
        return new \ArrayIterator($this->data);
    }
    
    public function isHoliday($dateTime)
    {
        if (in_array($dateTime->format('Y-m-d'), $this->holidayDays)) {
            return true;
        }
        return false;
        
    }
    
    public function isOff($dateTime)
    {
        foreach ($this->offdays as $offday) {
            if ( ($offday->getStartDate()<=$dateTime) && ($offday->getEndDate() >= $dateTime) ) {
                return true;
            }
        }
        return false;
    }
    
    public function numberOfWorkingDays() {
        $days = 0;
        
        $from = clone($this->startDate);
        $to   = clone($this->endDate);
        
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)

        // $to->modify('+0 day');
        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $this->holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $this->holidayDays)) continue;
            
            $flag = false;
            foreach ($this->offdays as $offday) {
                if ( ($offday->getStartDate()<=$period) && ($offday->getEndDate() >= $period) ) {
                    $flag = true;
                    break;
                }
            }
            
            if ($flag) {
                continue;
            }
            
            $days++;
        }
        
        return $days;
    }
    
}