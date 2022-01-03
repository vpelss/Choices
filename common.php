<?
include("vars.php");

#modify to encode other characters

function EncodeApostrophe($st){
 // escape all characters that are not word \W characters
#$st = preg_replace_callback("/([\W])/", create_function(
#$st = preg_replace_callback("/([$\(\)\;=<>\/])/", create_function(
$escapecharacters = "$\(\)\;=<>\/'\"";
$escapecharacters = "\W";
$st = preg_replace_callback("/([$escapecharacters])/", create_function(
            // single quotes are essential here,
            // or alternative escape all $ as \$
            '$matches',
            '$rr = ord($matches[1]); return "&#$rr;";'
        ) , $st);
#preg_replace($pattern, $replacement, $string);

return $st;
}

function get_after_vote_display($record,$question,$choices,$votes,$vote_total)
{
if (file_exists($file_name="./template_after_vote.php"))
        {
        $display = get_formatted_results($record,$question,$choices,$votes,$vote_total);
        $results = file_get_contents($file_name);
        $results = str_replace('{$results}' , $display , $results); #replace $question
        }
else
        print "<p>Where is $file_name?";
return $results;
};

function vote($record ,$vote)
{
$file_name="./data/{$record}.php";
include($file_name);
$votes[$vote]++;
$choices_text = implode("','",$choices);
$choices_text = "'{$choices_text}'";
$votes_text = implode(",",$votes);

$filedata = "<?php
\$question = '{$question}';
\$choices = array({$choices_text});
\$votes = array({$votes_text});
?>
";

$fres=fopen($file_name,"w+");
fwrite($fres,$filedata);
fclose($fres);
};

function get_vote_form($record,$question,$choices)
{
global $scripturi;
 
if (file_exists($file_name="./template_vote.php"))
        {
        $results = file_get_contents($file_name);
        $results = str_replace('{$question}' , $question , $results); #replace $question
        $results = str_replace('{$scripturi}' , $scripturi , $results); #replace $scripturi
        }
else
       print "<p>Where is $file_name?";

if (file_exists($file_name="./template_vote_line.php"))
    $template_result_line = file_get_contents($file_name);
else
     print "<p>Where is $file_name?";

$temp ='';
foreach ($choices as $key => $choice)
        {
    $temp_line = str_replace('{$choice}' , $choice , $template_result_line); #replace choice in line template
    $temp_line = str_replace('{$key}' , $key , $temp_line); #replace $key in line template
    #add to growing list of lines
    $temp = "{$temp}{$temp_line}";
    };

#add block of choices to template
$results = str_replace('{$choices}' , $temp , $results); #replace $question
$results = str_replace('{$record}' , $record , $results); #replace $record variable in line template
return $results;
};

function get_formatted_results($record,$question,$choices,$votes,$vote_total)
{
if (file_exists($file_name="./template_results.php"))
                {
        $results = file_get_contents($file_name);
        $results = str_replace('{$question}' , $question , $results); #replace $question
        }
else
        print "<p>Where is $file_name?";

if (file_exists($file_name="./template_result_line.php"))
    $template_result_line = file_get_contents($file_name);
else
    print "<p>Where is $file_name?";

foreach ($choices as $key => $choice)
        {
    $temp_line = str_replace('{$choice}' , $choice , $template_result_line); #replace choice in line template
    $temp_line = str_replace('{$key}' , $key , $temp_line); #replace $key in line template
    $vote = $votes[$key];
    $temp_line = str_replace('{$vote}' , $vote , $temp_line); #replace vote in line template
    #calc percentage
    if ($vote_total == 0) $percent = 0;
           else $percent = round(100*($vote/$vote_total));
    $temp_line = str_replace('{$percent}' , $percent , $temp_line); #replace % in line template
    #add to growing list of lines
    $temp = "{$temp}{$temp_line}";
    };

#add block of choices to template
$results = str_replace('{$choices}' , $temp , $results); #replace $question
return $results;
};

function get_vote_total($vote_array)
{
$b=0;
$vote_total = 0;
while (list($key, $val) = each($vote_array))
                            {
                               $vote_array[$key]=$val;
                               $vote_total = $vote_total + $val; #calculate sum of all votes
                            }
return $vote_total;
};

function get_todays_record($start_date,$mode,$record_count)
{
#input $startdate from startdate.php and $mode from vars.php
#output $count
#based on when we set the startdate and the mode we decide what record we should be working on.

switch ($mode)
{
case "daily":
        $seconds_diff = time() - $start_date;
        $days_diff= floor($seconds_diff /60 /60 /24);
        $count = ($days_diff % $record_count) + 1; #record for the day
            break;
case "weekly":
        $w_array = Array(0=>6,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5); #to  adjust for Monday as first day of week
            $w = date("w" ,$start_date); #day of week sun = 0
            $week_start_date = $start_date - ($w_array[$w] * 24 * 60 * 60); #subtract # of days of week value to restet to start of week (in seconds of course)
            $seconds_diff = time() - $week_start_date;
        $week_diff= floor($seconds_diff/60/60/24/7);
            $count = ($week_diff % $record_count) + 1; #record for the week
        break;
case "monthly":
        $m = date("m" ,$start_date); # month
        $Y = date("Y" ,$start_date);
        $m_today = date("m" ,time()); # month
        $Y_today = date("Y" ,time() );
        $count = (12*($Y_today-$Y)+($m_today-$m))+1;
         break;
        }
return $count;
};

function last_record_number()
{
$record = 1;
while(file_exists("../data/{$record}.php") or file_exists("./data/{$record}.php")) #so works with admin.php and show.php
        {
        $record ++;
    };
$record--;
return $record;
};

function edit_record()
{
$record = getvar("record");
$file_name="../data/{$record}.php";
$question = EncodeApostrophe(getvar("question"));
$choices = array();
$votes = array();
$last_record = getvar("last_record");
for ($a=0 ; $a<=$last_record ; $a++)
        {
    $choice = EncodeApostrophe(getvar("{$a}"));
    if ($choice)
            {
            array_push($choices,$choice);
            $vote = getvar("v_{$a}");
            if ($vote == 0) $vote=0;
            array_push($votes,$vote);
        };
    };
$choices_text = implode("','",$choices);
$choices_text = "'{$choices_text}'";
$votes_text = implode(",",$votes);

$filedata = "<?php
\$question = '{$question}';
\$choices = array({$choices_text});
\$votes = array({$votes_text});
?>";

$fres=fopen($file_name,"w+");
fwrite($fres,$filedata);
fclose($fres);
print "Record {$record} edited.<p>";
};

function edit_record_form($record)
{
$file_name="../data/{$record}.php";
include($file_name);
$admin = getvar("admin");
$password = getvar("password");
?>

<form action="" method="post">
        <input type="hidden" name="admin" value="<?php print $admin;?>">
        <input type="hidden" name="password" value="<?php print $password;?>">
        <input type="hidden" name="action" value="edit_record">
    <input type="hidden" name="record" value="<?php print $record;?>">
    Question: <input type="text" name="question" value='<?php print $question;?>' size="80"><p>
<?
foreach($choices as $key => $choice)
        {
        $vote = $votes[$key];
        print "Choice {$key}: <input type='text' name='{$key}' value='{$choice}' size='80'>
        Votes: <input type='text' name='v_{$key}' value='{$vote}' size='4'>
        <br>\n";
            }
$key++;
print "Choice {$key}: <input type='text' name='{$key}' value='' size='80'>
        Votes: <input type='text' name='v_{$key}' value='' size='4'>
        <br>\n";
?>
    <input type="hidden" name="last_record" value="<?php print $key;?>">
    <input type="submit" value="Edit this Record">
    </form>
<?
};

function add_record()
{
$record = last_record_number();
$record++;
$file_name="../data/{$record}.php";
$question = EncodeApostrophe(getvar("question"));
$choices = array();
$votes = array();
$a=1;
while ($choice = EncodeApostrophe(getvar("{$a}")))
        {
    array_push($choices,$choice);
    array_push($votes,0);
    $a++;
    };
$choices_text = implode("','",$choices);
$choices_text = "'{$choices_text}'";
$votes_text = implode(",",$votes);

$filedata = "<?php
\$question = '{$question}';
\$choices = array({$choices_text});
\$votes = array({$votes_text});
?>";

$fres=fopen($file_name,"w+");
fwrite($fres,$filedata);
fclose($fres);
print "Record {$record} added.$file_name<p>";
};

function add_record_form()
{
$admin = getvar("admin");
$password = getvar("password");
?>
<form action="" method="post">
<input type="hidden" name="admin" value="<?php print $admin;?>">
<input type="hidden" name="password" value="<?php print $password;?>">
<input type="hidden" name="action" value="add_record">
Question: <input type="text" name="question" value="" size="80"><p>
<?
for ($a=1;$a<20;$a++)
        {
        print "Choice {$a}: <input type='text' name='{$a}' value='' size='80'><br>\n";
        };
?>
<br>
<input type="submit" value="Submit">
<?
};

function update_settings()
{
global $admin,$password,$mode,$record,$enabled,$start_date,$cookiedays;

$mode = EncodeApostrophe(getvar("mode"));
$record = EncodeApostrophe(getvar("record"));
$enabled = EncodeApostrophe(getvar("enabled"));
$cookiedays = EncodeApostrophe(getvar("cookiedays"));
$start_date = EncodeApostrophe(strtotime(getvar("start_date")));
$output="<?php
\$admin='{$admin}';
\$password='{$password}';
\$enabled = $enabled;
\$mode='$mode';
\$record='$record';
\$cookiedays='$cookiedays';
\$start_date='$start_date';
?>";

$fres=fopen("../vars.php","w+");
fwrite($fres,$output);
fclose($fres);
$time = date("r" , time());
print "Updated {$time}<p>";
};

function delete_record($record)
{
print 'Deleting record, please wait<p>';
if (! unlink ("../data/{$record}.php")) {print 'Error deleting record'; exit;};
$record2 = $record + 1;
while (file_exists($file_name="../data/{$record2}.php"))
        {
        rename("../data/{$record2}.php", "../data/{$record}.php");
    $record = $record2;
        $record2 = $record + 1;
    };
print 'Done<p>';
};

function getvar($name){
    global $_GET, $_POST;
    if (isset($_GET[$name])) return $_GET[$name];
    else if (isset($_POST[$name])) return $_POST[$name];
    else return false;}
?>
