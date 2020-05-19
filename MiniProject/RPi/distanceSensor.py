import paho.mqtt.client as mqtt
import RPi.GPIO as GPIO
from datetime import datetime
import time
import ssl
import json
from urllib import request, parse


brokerAddress="3.83.104.216" #Mqtt-Server IP address
port =8883

def insertData():
        #Get current time of device
        now = datetime.now()
        date = now.date().strftime("%Y-%m-%d")
        time = now.time().strftime("%H:%M:%S")
        #convert to Json format
        datalog={}
        datalog["date"]=date
        datalog["time"]=time
        datalog=json.dumps(datalog)
        myData=[('Data',datalog)]
        #POST to insert php file
        data = parse.urlencode(myData).encode()
        req =  request.Request("http://ec2-174-129-82-6.compute-1.amazonaws.com/insertJson.php", data=data) # this will make the method "POST"
        with request.urlopen(req) as response:
                page = response.read().decode()
        print(page)
        
while True:
	try:
		GPIO.setmode(GPIO.BCM)

		echoPin=24
		triggerPin=23

		GPIO.setup(echoPin, GPIO.IN)
		GPIO.setup(triggerPin, GPIO.OUT)

		GPIO.output(triggerPin, GPIO.LOW)
		print("Calculating distance...")
		time.sleep(2)
		GPIO.output(triggerPin, GPIO.HIGH)

		time.sleep(0.00001)
		GPIO.output(triggerPin, GPIO.LOW)

		while GPIO.input(echoPin)==0:
			pulseStart = time.time()
		while GPIO.input(echoPin)==1:
			pulseFinish = time.time()

		pulseDuration = pulseFinish - pulseStart
		distance = round(pulseDuration * 17150, 2)
		print("Distance: ",distance,"cm")

		if distance < 5:
                        print("Triggering Alarm...")
                        client = mqtt.Client()
                        client.tls_set('ca.crt',cert_reqs=ssl.CERT_NONE)
                        client.username_pw_set("conor","conor")
                        client.connect(brokerAddress,port)
                        client.publish("test", "Alert")
                        print("System Alert!")
                        client.disconnect();
                        insertData()
	finally:
		GPIO.cleanup()
