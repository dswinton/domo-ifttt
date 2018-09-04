# domo-ifttt

Provides a relatively quick and easy way to voice control multiple devices in Domoticz using IFTTT.
Control *ALL* Domotics switches, scenes, groups with just 2 simple ifttt applets.

## Instructions:
1. Install this PHP file on a web server (prefered running on the same box as Domoticz).
2. Edit the values at the top of the file - password domoticz URL must be set
3. Forward a port to the (domoticz) web server
4. Give it a bit of a test with a URL like this...  https://yourserver.whatever/domo-ifttt.php?passkey=<Password_Set_in_Php_script>&devName=<Exact_Domoticz_Device_Name>&devState=1
5. Have a look in Syslog to see how it went
6. If it's working locally, it's time to set up IFTTT - check "README - IFTTT Setup.pdf" for instructions
7. Repeat IFTTT step by creating a 2nd applet but now with "Off" and devState=0
8. Have Fun

## About
I tried a couple of half-assed solutions for this problem out there... 
- I started following the Domoticz instructions for IFTTT integration but it was going to take about 100 years for me to add all the devices is wanted in there.

I found this script mostly out of frustration.
Now forked this (Thnx DSwinton) and trying to improve it wherever i can.
