

# Imports
from turtle import distance
import requests
import RPi.GPIO as GPIO
import time
import json
import multiprocessing

# -------------------------------------------------- #
# Constants definition                               #
# -------------------------------------------------- #

# Left motor GPIOs
L1 = 17  # H-Bridge 1
L2 = 27  # H-Bridge 2

# Right motor GPIOs
R1 = 23  # H-Bridge 3
R2 = 24  # H-Bridge 4

# Role
Role = 26

# Ldr
Ldr = 4

# UltraSonic
U1TRIG = 5
U1ECHO = 6
U2TRIG = 12
U2ECHO = 13
# API ENDPOINT
API = 'http://deneme.com/api.php'


# -------------------------------------------------- #
# Role Functions                                     #
# -------------------------------------------------- #
def set_role(status=True):
    if status==False:
        GPIO.output(Role, GPIO.HIGH)
    else:
        GPIO.output(Role, GPIO.LOW)


# -------------------------------------------------- #
# Ldr Functions                                      #
# -------------------------------------------------- #
def get_ldr():
    ldr = GPIO.input(Ldr)
    if ldr == 0:
        return True
    elif ldr == 1:
        return False


# -------------------------------------------------- #
# UltraSonic Functions                               #
# -------------------------------------------------- #
def get_UltraSonic_1():
    
    GPIO.output(U1TRIG, False)
    time.sleep(2)
    GPIO.output(U1TRIG, True)
    time.sleep(0.00001)
    GPIO.output(U1TRIG, False)
    pulse_start = time.time()
    pulse_end = time.time()

    while GPIO.input(U1ECHO) == 0:
        pulse_start = time.time()
   
    while GPIO.input(U1ECHO) == 1:
        pulse_end = time.time()
 
    pulse_duration = pulse_end - pulse_start
    distance = pulse_duration * 17165
    distance = round(distance, 1)

    return distance


def get_UltraSonic_2():
    
    GPIO.output(U2TRIG, False)
    time.sleep(2)
    GPIO.output(U2TRIG, True)
    time.sleep(0.00001)
    GPIO.output(U2TRIG, False)
    pulse_start = time.time()
    pulse_end = time.time()

    while GPIO.input(U2ECHO) == 0:
        pulse_start = time.time()
   
    while GPIO.input(U2ECHO) == 1:
        pulse_end = time.time()

    pulse_duration = pulse_end - pulse_start
    distance = pulse_duration * 17165
    distance = round(distance, 1)

    return distance



# -------------------------------------------------- #
# Left Motor Functions                               #
# -------------------------------------------------- #

def left_stop():
    GPIO.output(L1, GPIO.LOW)
    GPIO.output(L2, GPIO.LOW)


def left_forward():
    GPIO.output(L1, GPIO.HIGH)
    GPIO.output(L2, GPIO.LOW)


def left_backward():
    GPIO.output(L1, GPIO.LOW)
    GPIO.output(L2, GPIO.HIGH)


# -------------------------------------------------- #
# Right Motor Functions                              #
# -------------------------------------------------- #
def right_stop():
    GPIO.output(R1, GPIO.LOW)
    GPIO.output(R2, GPIO.LOW)


def right_forward():
    GPIO.output(R1, GPIO.HIGH)
    GPIO.output(R2, GPIO.LOW)


def right_backward():
    GPIO.output(R1, GPIO.LOW)
    GPIO.output(R2, GPIO.HIGH)


# -------------------------------------------------- #
# Macro definition part                              #
# -------------------------------------------------- #
def go_forward():
    left_backward()
    right_backward()


def go_backward():
    left_forward()
    right_forward()



def turn_left():
    left_forward()
    right_backward()


def turn_right():
    left_backward()
    right_forward()


def stop():
    left_stop()
    right_stop()


# -------------------------------------------------- #
# Server definition part                             #
# -------------------------------------------------- #

def request(params):
    print(params)
    r = requests.get(API, params=params)
    if r.status_code == 200:   
        print(r.content)
        return json.loads(r.content)


def runMacro():
    
    macro = request({'action': 'get_macro'})['value']
    
    if macro == 'stop':
        stop()
    elif macro == 'turn_right':
        turn_right()
        time.sleep(2)
    elif macro == 'turn_left':
        turn_left()
        time.sleep(2)
    elif macro == 'go_backward':
        go_backward()
    elif macro == 'go_forward':
        go_forward()
    else:
        stop()


def runRole():
    role = request({'action': 'get_role'})['value']
    if role:
        set_role(True)
    else:
        set_role(False)


def runLdr():
    ldr = get_ldr()
    time.sleep(0.3)
    request({'action': 'set_ldr', 'value': ldr})



def runUltraSonic1():
    UltraSonic = get_UltraSonic_1()
    request({'action': 'set_ultra_sonic_1', 'value': UltraSonic})

distanceTempData=-1
def runUltraSonic2():
    global distanceTempData
    UltraSonic = get_UltraSonic_2()
    Status=False
    if(distanceTempData !=-1):
        if((float(distanceTempData)-float(UltraSonic))>20):
            Status=True
        
        distanceTempData=UltraSonic
    else:
        distanceTempData=UltraSonic
    request({'action': 'set_ultra_sonic_2', 'value': UltraSonic,'mail':Status})


def setup():
    GPIO.setmode(GPIO.BCM)
    GPIO.setup(L1, GPIO.OUT)
    GPIO.setup(L2, GPIO.OUT)
    GPIO.setup(R1, GPIO.OUT)
    GPIO.setup(R2, GPIO.OUT)
    GPIO.setup(Ldr, GPIO.IN)
    GPIO.setup(Role, GPIO.OUT) 
    GPIO.setwarnings(False)
    GPIO.setup(U1TRIG, GPIO.OUT)
    GPIO.setup(U1ECHO, GPIO.IN)
    GPIO.setup(U2TRIG, GPIO.OUT)
    GPIO.setup(U2ECHO, GPIO.IN)
    stop()


def SenkronMain():
    setup()
    while True:
        runUltraSonic1()
        runUltraSonic2()
        runLdr()
        runRole()
        runMacro()
    GPIO.cleanup()

SenkronMain()

