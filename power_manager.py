import time, urllib, urllib2
from Adafruit_MCP230xx import *
import RPi.GPIO as GPIO


# Raspberry Pi Pin Usage
APPLIANCE0_RPI_PIN = 18

# MCP23017 I2C Port Expander Pin Usage
APPLIANCE0_LED_PIN = 10
APPLIANCE1_LED_PIN = 11
APPLIANCE2_LED_PIN = 12
APPLIANCE3_LED_PIN = 13
APPLIANCE4_LED_PIN = 14

APPLIANCE0_BUTTON_PIN = 4
APPLIANCE1_BUTTON_PIN = 5
APPLIANCE2_BUTTON_PIN = 6
APPLIANCE3_BUTTON_PIN = 7
APPLIANCE4_BUTTON_PIN = 8

APPLIANCE1_MOSFET_PIN = 0
APPLIANCE2_MOSFET_PIN = 1
APPLIANCE3_MOSFET_PIN = 15
APPLIANCE4_MOSFET_PIN = 3

# Appliance state is either 0 or 1 once setup is finished
APPLIANCE0_STATE = -1
APPLIANCE1_STATE = -1
APPLIANCE2_STATE = -1
APPLIANCE3_STATE = -1
APPLIANCE4_STATE = -1

# Set up Raspberry PI GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(APPLIANCE0_RPI_PIN, GPIO.OUT)

# URLs for sending/receiving appliance data
get_url = 'http://localhost/get_data.php'
send_url = 'http://localhost/send_data.php'

# Function called during setup or after main power supply is turned on
def setup_mcp():
    global mcp
    # Use busnum = 1 for new Raspberry Pi's (512MB with mounting holes)
    mcp = Adafruit_MCP230XX(busnum = 1, address = 0x20, num_gpios = 16)
    # Set LED and MOSFET pins to outputs
    mcp.config(APPLIANCE0_LED_PIN, mcp.OUTPUT)
    mcp.config(APPLIANCE1_LED_PIN, mcp.OUTPUT)
    mcp.config(APPLIANCE2_LED_PIN, mcp.OUTPUT)
    mcp.config(APPLIANCE3_LED_PIN, mcp.OUTPUT)
    mcp.config(APPLIANCE4_LED_PIN, mcp.OUTPUT)
    
    mcp.config(APPLIANCE1_MOSFET_PIN, mcp.OUTPUT)
    mcp.config(APPLIANCE2_MOSFET_PIN, mcp.OUTPUT)
    mcp.config(APPLIANCE3_MOSFET_PIN, mcp.OUTPUT)
    mcp.config(APPLIANCE4_MOSFET_PIN, mcp.OUTPUT)
    
    # Set button pins to input with the pullup resistor enabled
    mcp.pullup(APPLIANCE0_BUTTON_PIN, 1)
    mcp.pullup(APPLIANCE1_BUTTON_PIN, 1)
    mcp.pullup(APPLIANCE2_BUTTON_PIN, 1)
    mcp.pullup(APPLIANCE3_BUTTON_PIN, 1)
    mcp.pullup(APPLIANCE4_BUTTON_PIN, 1)

# Function called when changes to appliance states occur
def update_all_appliances():
    mcp.output(APPLIANCE0_LED_PIN, APPLIANCE0_STATE)
    mcp.output(APPLIANCE1_LED_PIN, APPLIANCE1_STATE)
    mcp.output(APPLIANCE2_LED_PIN, APPLIANCE2_STATE)
    mcp.output(APPLIANCE3_LED_PIN, APPLIANCE3_STATE)
    mcp.output(APPLIANCE4_LED_PIN, APPLIANCE4_STATE)
    
    mcp.output(APPLIANCE1_MOSFET_PIN, 1 - APPLIANCE1_STATE)
    mcp.output(APPLIANCE2_MOSFET_PIN, 1 - APPLIANCE2_STATE)
    mcp.output(APPLIANCE3_MOSFET_PIN, 1 - APPLIANCE3_STATE)
    mcp.output(APPLIANCE4_MOSFET_PIN, 1 - APPLIANCE4_STATE)

# Function called to get appliance data from MySQL database
def get_data():
    values = {'username' : 'sender', # Key/value pairs
              'p' : '9834db06bfafa141c814c216f8a3b32b6c5812bafbfa750e6d9e10584bcdada8de6d8f0cc037241e12a94b7c9cf45f0ca1e02aeef9277cd37223f5f8dc0cbb68',
             }
    try:
        data = urllib.urlencode(values)          
        req = urllib2.Request(get_url, data)
        response = urllib2.urlopen(req)
        return response.read()
    except Exception, detail:
        print "Err ", detail

# Function called to send appliance data to MySQL database when there is a change (a button is pressed)
def send_data():
    values = {'username' : 'sender', # Key/value pairs
              'p' : '9834db06bfafa141c814c216f8a3b32b6c5812bafbfa750e6d9e10584bcdada8de6d8f0cc037241e12a94b7c9cf45f0ca1e02aeef9277cd37223f5f8dc0cbb68',
              'appliance0' : APPLIANCE0_STATE,
              'appliance1' : APPLIANCE1_STATE,
              'appliance2' : APPLIANCE2_STATE,
              'appliance3' : APPLIANCE3_STATE,
              'appliance4' : APPLIANCE4_STATE,
             }
    try:
        data = urllib.urlencode(values)          
        req = urllib2.Request(send_url, data)
        response = urllib2.urlopen(req)
        return response.read()
    except Exception, detail:
        print "Err ", detail
        
def verify_database_integrity(): # Error will be thrown if any appliances are set to on when Python program starts
    # This function sets all appliances to off just to be safe
    values = {'username' : 'sender', # Key/value pairs
              'p' : '9834db06bfafa141c814c216f8a3b32b6c5812bafbfa750e6d9e10584bcdada8de6d8f0cc037241e12a94b7c9cf45f0ca1e02aeef9277cd37223f5f8dc0cbb68',
              'appliance0' : 0,
              'appliance1' : 0,
              'appliance2' : 0,
              'appliance3' : 0,
              'appliance4' : 0,
             }
    try:
        data = urllib.urlencode(values)          
        req = urllib2.Request(send_url, data)
        response = urllib2.urlopen(req)
        return response.read()
    except Exception, detail:
        print "Err ", detail
        
def turn_appliance0_on(): # Turning on main power supply resets I2C Port Expander - so this function fixes that issue
    global APPLIANCE0_STATE
    APPLIANCE0_STATE = 1
    GPIO.output(APPLIANCE0_RPI_PIN, True)
    time.sleep(.25)
    setup_mcp()
    update_all_appliances()
    
def turn_appliance0_off(): # When main power supply is turned off, all other appliances need to be shut as well
    global APPLIANCE0_STATE, APPLIANCE1_STATE, APPLIANCE2_STATE, APPLIANCE3_STATE, APPLIANCE4_STATE
    APPLIANCE0_STATE = 0
    APPLIANCE1_STATE = 0
    APPLIANCE2_STATE = 0
    APPLIANCE3_STATE = 0
    APPLIANCE4_STATE = 0
    update_all_appliances()
    GPIO.output(APPLIANCE0_RPI_PIN, False)

# Main part of program running
print "Setting up MCP23017 port expander"
setup_mcp()
print "Verifying database integrity"
verify_database_integrity()

print "Main loop of program running"
while True:
    try:
        page = get_data() # Get appliance data
        print page
        
        # React if state of appliance has changed
        if page.find('!0@0#') != -1 and APPLIANCE0_STATE != 0:
            turn_appliance0_off()
        elif page.find('!0@1#') != -1 and APPLIANCE0_STATE != 1:
            turn_appliance0_on()
        
        if page.find('!1@0#') != -1 and APPLIANCE1_STATE != 0:
            APPLIANCE1_STATE = 0
            mcp.output(APPLIANCE1_MOSFET_PIN, 1 - APPLIANCE1_STATE)
            mcp.output(APPLIANCE1_LED_PIN, APPLIANCE1_STATE)
        elif page.find('!1@1#') != -1 and APPLIANCE1_STATE != 1:
            APPLIANCE1_STATE = 1
            mcp.output(APPLIANCE1_MOSFET_PIN, 1 - APPLIANCE1_STATE)
            mcp.output(APPLIANCE1_LED_PIN, APPLIANCE1_STATE)
        
        if page.find('!2@0#') != -1 and APPLIANCE2_STATE != 0:
            APPLIANCE2_STATE = 0
            mcp.output(APPLIANCE2_MOSFET_PIN, 1 - APPLIANCE2_STATE)
            mcp.output(APPLIANCE2_LED_PIN, APPLIANCE2_STATE)
        elif page.find('!2@1#') != -1 and APPLIANCE2_STATE != 1:
            APPLIANCE2_STATE = 1
            mcp.output(APPLIANCE2_MOSFET_PIN, 1 - APPLIANCE2_STATE)
            mcp.output(APPLIANCE2_LED_PIN, APPLIANCE2_STATE)
        
        if page.find('!3@0#') != -1 and APPLIANCE3_STATE != 0:
            APPLIANCE3_STATE = 0
            mcp.output(APPLIANCE3_MOSFET_PIN, 1 - APPLIANCE3_STATE)
            mcp.output(APPLIANCE3_LED_PIN, APPLIANCE3_STATE)
        elif page.find('!3@1#') != -1 and APPLIANCE3_STATE != 1:
            APPLIANCE3_STATE = 1
            mcp.output(APPLIANCE3_MOSFET_PIN, 1 - APPLIANCE3_STATE)
            mcp.output(APPLIANCE3_LED_PIN, APPLIANCE3_STATE)
            
        if page.find('!4@0#') != -1 and APPLIANCE4_STATE != 0:
            APPLIANCE4_STATE = 0
            mcp.output(APPLIANCE4_MOSFET_PIN, 1 - APPLIANCE4_STATE)
            mcp.output(APPLIANCE4_LED_PIN, APPLIANCE4_STATE)
        elif page.find('!4@1#') != -1 and APPLIANCE4_STATE != 1:
            APPLIANCE4_STATE = 1
            mcp.output(APPLIANCE4_MOSFET_PIN, 1 - APPLIANCE4_STATE)
            mcp.output(APPLIANCE4_LED_PIN, APPLIANCE4_STATE)
        
        button_pressed = False
        if mcp.input(APPLIANCE0_BUTTON_PIN) == 0: # Check if Appliance 0's button was pressed
            if APPLIANCE0_STATE == 0:
                turn_appliance0_on()
            else:
                turn_appliance0_off()
            button_pressed = True
            
        else:
            if mcp.input(APPLIANCE1_BUTTON_PIN) == 0 and APPLIANCE0_STATE == 1: # Check if Appliance 1's button was pressed
                if APPLIANCE1_STATE == 0:                                       # Only act if power supply is on
                    APPLIANCE1_STATE = 1
                else:
                    APPLIANCE1_STATE = 0
                button_pressed = True
            elif mcp.input(APPLIANCE2_BUTTON_PIN) == 0 and APPLIANCE0_STATE == 1: # Check if Appliance 2's button was pressed
                if APPLIANCE2_STATE == 0:                                       # Only act if power supply is on
                    APPLIANCE2_STATE = 1
                else:
                    APPLIANCE2_STATE = 0
                button_pressed = True
            elif mcp.input(APPLIANCE3_BUTTON_PIN) == 0 and APPLIANCE0_STATE == 1: # Check if Appliance 3's button was pressed
                if APPLIANCE3_STATE == 0:                                       # Only act if power supply is on
                    APPLIANCE3_STATE = 1
                else:
                    APPLIANCE3_STATE = 0
                button_pressed = True
            elif mcp.input(APPLIANCE4_BUTTON_PIN) == 0 and APPLIANCE0_STATE == 1: # Check if Appliance 4's button was pressed
                if APPLIANCE4_STATE == 0:                                       # Only act if power supply is on
                    APPLIANCE4_STATE = 1
                else:
                    APPLIANCE4_STATE = 0
                button_pressed = True
        
        if button_pressed:
            print send_data()
            update_all_appliances()
        
        time.sleep(2)
    except KeyboardInterrupt: # User presses Ctrl-C to stop the program
        print "\n\nShutting down power supply and appliances..."
        turn_appliance0_off()
        print send_data()
        print "Exiting program..."
        exit()