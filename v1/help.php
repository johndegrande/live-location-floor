<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>webFloor Help</title>
</head>
<style>
body {
	font-family: Arial, Helvetica, sans-serif;
}
li {
	text-align: justify;
	vertical-align: middle;
}
img {
	text-align: center;
	vertical-align: top;
}
h2 {
	text-align: center;
	vertical-align: middle;
}
</style>
<body bgcolor="#000000">
<table width="990px" border="0" cellspacing="5px" cellpadding="5px" style="color: #FFFFFF;" align="center">
  <tr>
    <td><a href="webFloor.php"><img src="images/btn_return.png" border="0" /></a></td>
  </tr>
  <tr>
    <td>
    	<h2>Top Button Panel</h2>
        <table width="100%" border="0" cellspacing="5px" cellpadding="0px">
          <tr>
            <td align="center"><img src="images/help_buttons.jpg" /></td>
          </tr>
          <tr>
            <td>
            	<ul>
                	<li><b>Log Out</b> – Log Out of webFloor</li>
                	<li><b>Turn AutoRefresh On/Off</b> – With AutoRefresh enabled, the page will automatically reload itself every minute to provide the most up to date information on all of your games. If this is turned off, the page will not refresh and you will be given only a snapshot from the time the page was initially loaded.</li>
                    <li><b>Help</b> – You’re looking at this button’s function.</li>
                    <li><b>Sort By</b> – Choose the location you would like webFloor to display.</li>
                </ul>
            </td>
          </tr>
        </table>
        <hr />
    </td>
  </tr>
  <tr>
    <td>
    	<h2>Vendor Header</h2>
    	<table width="100%" border="0" cellspacing="5px" cellpadding="0px">
          <tr>
            <td align="center" colspan="2"><img src="images/help_header.jpg" /></td>
          </tr>
          <tr>
            <td width="50%">
            	<ul>
                	<li><b>Location Name</b> – Name of the Location you are viewing</li>
                    <li><b>Game Count</b> – Number of Games on the floor of the specified location.</li>
                    <li><b>Last Updated</b> – Lists the time the page was last refresh (customizes to your time zone)</li>
                </ul>
            </td>
            <td width="50%">
            	<ul>
                	<li><b>Last Connected</b> – Date and time of the last update received from this location</li>
                    <li><b>Internet Connection Status</b> – Determines, based on "Last Connected" whether we are "Connected" or "Disconnected"</li>
                    <li><b>Server ID</b> – Server ID number (mainly for office use)</li>
                    <li><b>Map Button</b> – Click to open a Google Map with Location info such as address and phone number.</li>
                </ul>
            </td>
          </tr>
        </table>
        <hr />
    </td>
  </tr>
  <tr>
    <td>
    	<h2>The Floor</h2>
    	<table width="100%" border="0" cellspacing="5px" cellpadding="5px">
          <tr>
            <td>
            	<ul>
            		<li><b>Game Box</b> – This contains all of the information relating to the status of the game itself.
                    	<ul>
                        	<li><b>Game Type</b> – A logo of the Game Title (Bankshot, Farmer’s Market, etc.)</li>
                            <li><b>Game Name</b> – This is an abbreviation of the Location Name and Game Number. If the text color is <span style="color: #00FF00;">green</span> instead of white, it means that there is active credits on the machine and someone is most likely playing.</li>
                            <li><b>Game Status Color</b> – The background of the Game Box reflects the status of the game. Ex: Black = Normal, Red = Disconnected from the Internet, Blue = Game is in Out of Service Mode, Green = Jackpot, etc. There are others, refer to the Expanded Information Box for more detail on the game’s status.</li>
                        </ul>
                    </li>
                </ul>
                <ul>
                    <li><b>Expanded Information Box</b> – by rolling over (or clicking on a mobile device) the Game Box, a box containing more detailed information will appear:
                    	<ul>
                            <li><b>Game Name</b> - This is an abbreviation of the Location Name and Game Number.</li>
                            <li><b>Game Type</b> - Corresponds to the logo above but in written form.</li>
                            <li><b>Status</b> – A description of the Game’s Status mode. This corresponds to the color of the Game Box and presents more detailed information of what’s happening with the game itself.</li>
                        </ul>
                    </li>
				</ul>	
            </td>
            <td valign="top"><img src="images/help_floor.jpg" /></td>
          </tr>
        </table>
        <hr />
    </td>
  </tr>
</table>
</body>
</html>
