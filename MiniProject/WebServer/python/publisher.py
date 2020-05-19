import sys
sys.path.append("/usr/lib/python3.6/site-packages")
import paho.mqtt.client as mqtt
import time
import ssl
topicMessage=(sys.argv[1])
try:
	brokerAddress="3.83.104.216" #Mqtt-Server IP address
	port =8883


	topicName = "test"
	client = mqtt.Client()
	client.tls_set('/home/ubuntu/python/ca.crt',cert_reqs=ssl.CERT_NONE)
	#client.tls_insecure_set(True)
	client.username_pw_set("",")
	client.connect(brokerAddress,port,60)
	client.publish(topicName, topicMessage)
	print(topicMessage+" successful")
	client.disconnect();
except:
	print("System failed to lock")


