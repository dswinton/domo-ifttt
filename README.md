# domo-ifttt

Provides a relatively quick and easy way to voice control multiple devices in Domoticz using IFTTT.

## Instructions:
1. Whack this PHP file on a web server running on the same box as Domoticz
2. Edit the values at the top of the file - password domoticz URL must be set
3. Forward a port from your intertubes connection to the web server
4. Give it a bit of a test with a URL like this...  https://yourserver.whatever/domo-ifttt.php?passkey=superSecretPasswordOnlyIFTTknows&devName=Thing+to+Test+With&devState=1
5. Have a squizz in Syslog to see how it went
6. If it's working testing locally, it's time to set up IFTTT - check "README - IFTTT Setup.pdf" for instructions
7. ???
8. PROFIT!!

## About
I tried a couple of half-assed solutions for this problem out there... 
- I used to use HA bridge, which was awesome but doesn't seem to work these days
- I tried a commercial offering for this..  that charges money for the privellege but in my experience just didnt work.
- I started following the Domoticz instructions for IFTTT integration but it was going to take about 100 years for me to add all the devices is wanted in there.

When I found that everything else out there either didn't work or wasn't practical, I made this script mostly out of frustration.
The code is bare minimum and not exactly pretty, but it does the job.  Feel free to improve on it and push anything you'd like to.