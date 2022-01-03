<FORM ACTION="{$scripturi}show.php" METHOD="get" TARGET="_blank">
		<INPUT TYPE="hidden" NAME="action" VALUE="voting">
		<INPUT TYPE="hidden" NAME="record" VALUE="{$record}"> 
		<TABLE CELLPADDING="5" CELLSPACING="2" BGCOLOR="#000000" width="400"> 
		  <TR> 
			 <TD BGCOLOR="#cc0000">{$question}</TD> 
		  </TR> 
		  <TR> 
			 <TD BGCOLOR="#D6D3CE">{$choices}</TD> 
		  </TR> 
		  <TR> 
			 <TD BGCOLOR="#FFFF00"><INPUT TYPE="submit" VALUE="Vote"></TD> 
		  </TR> 
		</TABLE> </FORM>