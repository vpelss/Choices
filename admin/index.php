<?
include('../common.php');

#if password and or user name exist, insist they are provided first!
if (!(! $admin and ! $password))
        {
        #see if we have loged in yet
        $adm = getvar("admin");
        $pas = getvar("password");
        if (($admin != $adm) or ($password != $pas))
            {
?>
            <form action="" method="post">
            Login: <input type="text" name="admin" value=""><br>
            Password: <input type="text" name="password" value=""><br>
            <input type="submit" value="Login">
            </form>
<?
            exit;
            }
        };

#set $start_date if there is not one
if ($start_date == 0)
        {
        $start_date = strtotime(date("d M Y" , time())); #rounded off to 0:00 and in seconds format
        };

$action = getvar("action");
if ($action == "update_settings") update_settings(); #update vars.php
if ($action == "delete_record") delete_record(getvar(record)); #delete_record
if ($action == "add_record_form") {add_record_form();exit;}; #show add_record_form
if ($action == "add_record") add_record(); #add_record
if ($action == "edit_record_form") {edit_record_form(getvar(record));exit;}; #edit_record_form()
if ($action == "edit_record") edit_record(); #edit_record
?>

<form action="" method="post">
<input type="hidden" name="admin" value="<? print $admin;?>">
<input type="hidden" name="password" value="<? print $password;?>">
<input type="hidden" name="action" value="update_settings">
<input type="radio" value=0 <? if ($enabled == 0){print "checked";}?> name="enabled">Disable Script<br>
<input type="radio" value=1 <? if ($enabled == 1){print "checked";}?> name="enabled">Enable Script
<p>
Date you wish voting to start:<br>
(dd-mm-yyyy)<br>
<input type="text" name="start_date" size="10" value="<? print date("d-M-Y",$start_date);?>">
<p>
Choose when you want questions to change:<br>
<input type="radio" value="daily" <? if ($mode == "daily"){print "checked";}?> name="mode">Daily<br>
<input type="radio" value="weekly" <? if ($mode == "weekly"){print "checked";}?> name="mode">Weekly<br>
<input type="radio" value="monthly" <? if ($mode == "monthly"){print "checked";}?> name="mode">Monthly
<br>
(OR)<br>Use Question Number: <input type="text" name="record" size="10" value="<? print $record;?>">
<br>
(overrides all other modes)(0 disables)
<p>
Days before user can vote per question: <input type="text" name="cookiedays" size="10" value="<? print $cookiedays;?>">
<br>
(0 allows unlimited voting.)
<p>
<input type="submit" value="Update Settings">
</form>
--------------------------------------------------------
<p>
<form action="" method="post">
<input type="hidden" name="admin" value="<? print $admin;?>">
<input type="hidden" name="password" value="<? print $password;?>">
<input type="hidden" name="action" value="add_record_form">
<input type="submit" value="Add a Question">
</form>
<?
#print all questions and answers
$question_count = 1;
while (file_exists($file_name="../data/{$question_count}.php"))
    {
    include($file_name);
    print "<p>Question {$question_count}: {$question}<br>";
    foreach($choices as $key => $choice)
        {
        $vote = $votes[$key];
        print "Choice {$key}: $choice Votes: {$vote}<br>";
        }
?>
    <form action="" method="post">
        <input type="hidden" name="admin" value="<? print $admin;?>">
        <input type="hidden" name="password" value="<? print $password;?>">
        <input type="hidden" name="action" value="delete_record">
    <input type="hidden" name="record" value="<? print $question_count;?>">
    <input type="submit" value="Delete this Record">
    </form>

    <form action="" method="post">
        <input type="hidden" name="admin" value="<? print $admin;?>">
        <input type="hidden" name="password" value="<? print $password;?>">
        <input type="hidden" name="action" value="edit_record_form">
    <input type="hidden" name="record" value="<? print $question_count;?>">
    <input type="submit" value="Edit this Record">
    </form>
--------------------------------------------------------
<?
    $question_count += 1;
    };
?>

<p>
<form action="" method="post">
<input type="hidden" name="admin" value="<? print $admin;?>">
<input type="hidden" name="password" value="<? print $password;?>">
<input type="hidden" name="action" value="add_record_form">
<input type="submit" value="Add a Question">
</form>


                  


