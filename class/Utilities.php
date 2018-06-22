<?php

class Utilities{

    private $dt;
    
    public function getPaging($page, $total_rows, $records_per_page, $page_url){
 
        // paging array
        $paging_arr=array();
 
        // button for first page
        $paging_arr["first"] = $page>1 ? "{$page_url}page=1" : "";
 
        // count all content in the database to calculate total pages
        $total_pages = ceil($total_rows / $records_per_page);
 
        // range of links to show
        $range = 2;
 
        // display links to 'range of pages' around 'current page'
        $initial_num = $page - $range;
        $condition_limit_num = ($page + $range)  + 1;
 
        $paging_arr['pages']=array();
        $page_count=0;
         
        for($x=$initial_num; $x<$condition_limit_num; $x++){
            // be sure '$x is greater than 0' AND 'less than or equal to the $total_pages'
            if(($x > 0) && ($x <= $total_pages)){
                $paging_arr['pages'][$page_count]["page"]=$x;
                $paging_arr['pages'][$page_count]["url"]="{$page_url}page={$x}";
                $paging_arr['pages'][$page_count]["current_page"] = $x==$page ? "yes" : "no";
 
                $page_count++;
            }
        }
 
        // button for last page
        $paging_arr["last"] = $page<$total_pages ? "{$page_url}page={$total_pages}" : "";
 
        // json format
        return $paging_arr;
    }

    public function defaultTimeZone() {

        $tz = 'Asia/Manila';
        $timestamp = time();
        $this->dt = new DateTime("now"); 
        $this->dt->setTimestamp($timestamp); 
        $this->dt->setTimezone(new DateTimeZone($tz));

        //return date and time based on timezone
        return $this->dt->format('Y-m-d H:i:s');        

    }
    
    public function get_time_ago($time_stamp){

        $now = 

        $time_difference = strtotime($this->defaultTimeZone()) - $time_stamp;

        if ($time_difference >= 60 * 60 * 24 * 365.242199)
        {
            /*
            * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 365.242199 days/year
            * This means that the time difference is 1 year or more
            */
            return $this->get_time_ago_string($time_stamp, 60 * 60 * 24 * 365.242199, 'year');
        }
        elseif ($time_difference >= 60 * 60 * 24 * 30.4368499)
        {
            /*
            * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 30.4368499 days/month
            * This means that the time difference is 1 month or more
            */
            return $this->get_time_ago_string($time_stamp, 60 * 60 * 24 * 30.4368499, 'month');
        }
        elseif ($time_difference >= 60 * 60 * 24 * 7)
        {
            /*
            * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 7 days/week
            * This means that the time difference is 1 week or more
            */
            return $this->get_time_ago_string($time_stamp, 60 * 60 * 24 * 7, 'week');
        }
        elseif ($time_difference >= 60 * 60 * 24)
        {
            /*
            * 60 seconds/minute * 60 minutes/hour * 24 hours/day
            * This means that the time difference is 1 day or more
            */
            return $this->get_time_ago_string($time_stamp, 60 * 60 * 24, 'day');
        }
        elseif ($time_difference >= 60 * 60)
        {
            /*
            * 60 seconds/minute * 60 minutes/hour
            * This means that the time difference is 1 hour or more
            */
            return $this->get_time_ago_string($time_stamp, 60 * 60, 'hour');
        }
        else
        {
            /*
            * 60 seconds/minute
            * This means that the time difference is a matter of minutes
            */
            return $this->get_time_ago_string($time_stamp, 60, 'minute');
        }
    }

    public function get_time_ago_string($time_stamp, $divisor, $time_unit) {

        $time_difference = strtotime($this->defaultTimeZone()) - $time_stamp;
        $time_units      = floor($time_difference / $divisor);

        settype($time_units, 'string');

        if ($time_units === '0')
        {
            return 'less than 1 ' . $time_unit . ' ago';
        }
        elseif ($time_units === '1')
        {
            return '1 ' . $time_unit . ' ago';
        }
        else
        {
            /*
            * More than "1" $time_unit. This is the "plural" message.
            */
            // TODO: This pluralizes the time unit, which is done by adding "s" at the end; this will not work for i18n!
            return $time_units . ' ' . $time_unit . 's ago';
        }
    }
 
}

$core = new Utilities();
print_r($core->get_time_ago(strtotime('now')));
echo ' '.date("Y-m-d h:i:s");
echo '<br />';
print_r($core->defaultTimeZone());