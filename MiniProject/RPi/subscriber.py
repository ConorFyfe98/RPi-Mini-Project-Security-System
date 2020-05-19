import paho.mqtt.client as mqtt
import RPi.GPIO as GPIO
import sys
import time
import ssl
import os

brokerAddress="3.83.104.216" #Mqtt-Server IP address
port=8883

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

buzzerPin ="11"
buttonPin = 26
ledPin="12"
lockPin="17"

userApp="/home/pi/MiniProject/pinControl.out writepin"

GPIO.setup(buttonPin, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

def my_callback(buttonPin):
    os.system(userApp+' '+buzzerPin+' 0')
    os.system(userApp+' '+ledPin+' 0')
    os.system(userApp+' '+lockPin+' 0')

def on_message(client, userdata, msg):
    print("Topic: " + msg.topic)
    print("Message received: " + str(msg.payload.decode('utf8')))
    if "Alert" in str(msg.payload.decode('utf8')):
        os.system(userApp+' '+ledPin+' 1')
        os.system(userApp+' '+lockPin+' 1')
        os.system(userApp+' '+buzzerPin+' 1')

    if "Lock" in str(msg.payload.decode('utf8')):
        os.system(userApp+' '+lockPin+' 1')


    if "Unlock" in str(msg.payload.decode('utf8')):
        os.system(userApp+' '+lockPin+' 0')

    GPIO.add_event_detect(buttonPin, GPIO.RISING, callback=my_callback)

    
def on_connect(client, userdata, flags, rc):
    print("Alarm System set up and connected :"+str(rc))    

def subscribe():
    #client id should be unique
    client = mqtt.Client(client_id="RPi", clean_session=True, userdata=None, transport="tcp")
    client.tls_set('ca.crt',cert_reqs=ssl.CERT_NONE)
    client.username_pw_set("conor","conor")
    client.on_connect = on_connect
    client.on_message=on_message
    print("Setting Up Alarm System...")
    client.connect(brokerAddress,port)
    client.loop_start()
    client.subscribe("test")
    client.loop_forever()

subscribe()
