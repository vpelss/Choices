<?
include('common.php');

$record_count = last_record_number();

#is voting disabled
if (! $enabled){print "Voting has been disabled by administrator";exit;}

#see if voting has started yet
if ($start_date > Time())
        {
        Print "Voting start date is set for ";
		print date('d-M-Y',$start_date);
        exit;
        };

$record = intval($record);
if ($record == 0)
        {
        #if $record in vars.php is bad, use the daily,weeky,monthly option!
        $record = get_todays_record($start_date,$mode,$record_count);
        }

if (file_exists($file_name="./data/{$record}.php"))
        {
        include($file_name);
        #show vote form and allow voting if not ( ($cookiedays!=0) and ($_COOKIE['CookieStatus'] == $question))
        #show vote form and allow voting : if cookies are turned off or there are no cookies for the current question
        if ( ($cookiedays == 0) or ($_COOKIE['CookieStatus'] != $question) )
                {
                $display = get_vote_form($record,$question,$choices); #display vote form
                if ("voting" == getvar('action')) #we are voting
                                {
                                 if ($cookiedays == 0) setcookie("CookieStatus", $question , time());  #zeroize cookie if cookies are turned off
                                else setcookie("CookieStatus", $question , time()+60*60*24*$cookiedays , "/");  #expire in month days
                                $vote = getvar('vote');
                                $record = getvar('record');
                                vote($record ,$vote);
                                include($file_name); #update data
                                $vote_total = get_vote_total($votes);
                                $display = get_after_vote_display($record,$question,$choices,$votes,$vote_total);
                        		}
                }
        else
                {
                $vote_total = get_vote_total($votes);
                $display = get_formatted_results($record,$question,$choices,$votes,$vote_total); #show vote results             
                };

        #if ($cookiedays == 0) {setcookie("CookieStatus", $question , time());};  #zeroize cookie if cookies are turned off
        }
else
        {
        $display = "No record available. Add them using the admin panel.$file_name ";
        };
print $display;
?>
