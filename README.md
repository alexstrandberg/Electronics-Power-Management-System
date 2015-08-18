# Electronics-Power-Management-System

The code for an Electronics Power Management System that uses a Raspberry Pi, a 12V 5A Power Supply, a 4-Channel Relay Board, a PowerSwitch Tail, and an MCP23017 I2C Port Expander to control up to 4 12V DC appliances with a web interface and physical buttons.  It can control appliances such as network switches and USB hubs.

- The Raspberry Pi is powered by a separate 5V power supply.  A web server with MySQL and PHP runs on the Raspberry Pi.  The I2C Port Expander handles button inputs, LED outputs, and relay states.

- The 4 Channel Relay Board closes or opens the circuit for each appliance to turn the appliance on or off.  

- The PowerSwitch Tail turns the 12V power supply for the appliances on or off.  It is also controlled by a hardware button and the web interface.

- By either pressing a physical button or a button on the web interface, appliances can be turned on or off.  I am working on adding a scheduling feature to the web interface so that I can have appliances turn on and off at designated times for each day of the week.

[Fritzing](http://fritzing.org/) is needed to view the schematic file

Check out the video for this project:
http://youtu.be/Y_vUc5mKOMk

The code was written by Alex Strandberg and is licensed under the MIT License, check LICENSE for more information


## Web Server/MySQL Database Setup:
1. sudo apt-get update
2. sudo apt-get install apache2
3. sudo apt-get install php5
4. sudo apt-get install mysql-server mysql-client
5. sudo apt-get install phpmyadmin
 * Select apache2 when prompted for "Web server to reconfigure automatically" by pressing space, then Tab, and then Enter
6. Navigate to phpmyadmin in a web browser
 * Example: http://raspberrypi.local/phpmyadmin
 * Login as root
 * Create a database user with the credentials found at the top of [www/functions.php](https://github.com/alexstrandberg/Electronics-Power-Management-System/blob/master/www/functions.php)
 * Import the database by clicking the "Import" button and selecting power_manager.sql

## Using the Electronics Power Management System:
1. Run the power_manager.py Python file
 * Example: sudo python /home/pi/power_manager.py
2. Navigate to http://raspberrypi.local/ or the Raspberry Pi's IP Address
 * NOTE: Default username: admin
 *       Default password: admin
 * See www/register.php to set up different credentials

## Python Libraries:
- [Adafruit_MCP230xx](https://github.com/adafruit/Adafruit-Raspberry-Pi-Python-Code)
- [RPi.GPIO](https://pypi.python.org/pypi/RPi.GPIO)