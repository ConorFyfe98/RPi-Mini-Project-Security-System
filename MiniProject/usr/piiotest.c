/*
 ============================================================================
 Name        : piiotest.c
 Author      : Conor Fyfe
 Version     :
 Copyright   : See Abertay copyright notice
 Description : Test application for piio driver
 ============================================================================
 */

#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/ioctl.h>

#include"piio.h"

/*
 * Functions for the ioctl calls
 *
 root@raspberrypi:/home/pi# ./piiotest writepin 3 1
 User App - CGI
 WRITE:Requested pin:3 - val:1 - desc:desc
 Exit 0
 root@raspberrypi:/home/pi# ./piiotest read 3
 User App - CGI
 READ:Requested  pin:3 - val:1 - desc:LKMpin
 Exit 0
 */

gpio_pin apin;
lkm_data lkmdata;

void write_to_driver(int fd) {
	int ret;
	/* Write to kernel space - see dmesg command*/
	strcpy(lkmdata.data, "This is from user application");
	lkmdata.len = 32;
	lkmdata.type = 'w';
	ret = ioctl(fd, IOCTL_PIIO_WRITE, &lkmdata);

	if (ret < 0) {
		printf("Function failed:%d\n", ret);
		exit(-1);
	}

}

void read_from_drvier(int fd) {
	int ret;

	/*Read from kernel space - see dmesg command*/
	strcpy(lkmdata.data, "");
	ret = ioctl(fd, IOCTL_PIIO_READ, &lkmdata);

	if (ret < 0) {
		printf("Function failed:%d\n", ret);
		exit(-1);
	}

	printf("Message from driver: %s\n", lkmdata.data);
}

int main(int argc, char *argv[]) {
	int fd, ret, i, loop, time, startvalue;
	char *msg = "Message passed by ioctl\n";

	fd = open("//dev//conor", O_RDWR);
	if (fd < 0) {
		printf("Can't open device file: %s\n", DEVICE_NAME);
		exit(-1);
	}


	if (argc > 1) {
		if (!strncmp(argv[1], "readmsg", 8)) {
			read_from_drvier(fd);

		}

		if (!strncmp(argv[1], "writemsg", 9)) {
			write_to_driver(fd);
		}

		if (!strncmp(argv[1], "readpin", 8)) {
			/*  Pass GPIO struct with IO control */
			memset(&apin , 0, sizeof(apin));
			strcpy(apin.desc, " Read Option ");
			apin.pin =  strtol (argv[2],NULL,10);
			/* Pass 'apin' struct to 'fd' with IO control*/
			ret =ioctl(fd,IOCTL_PIIO_GPIO_READ ,&apin);
	}

		if (!strncmp(argv[1], "writepin", 9)) {
			/*  Pass GPIO struct with IO control */
			memset(&apin , 0, sizeof(apin));
			gpio_pin apin;
			strcpy(apin.desc," Write Option ");
			apin.pin =strtol(argv[2],NULL,10);
			apin.value =strtol(argv[3],NULL,10);
			/* Pass 'apin' struct to 'fd' with IO control*/
			ret =ioctl(fd,IOCTL_PIIO_GPIO_WRITE ,&apin);
		}

		if (!strncmp(argv[1], "togglepin", 10)) {
			memset(&apin , 0, sizeof(apin));
			gpio_pin apin;
			strcpy(apin.desc," Toggle LED ");
			apin.pin =strtol(argv[2],NULL,10);			
			loop =strtol(argv[3], NULL, 10);
			time =strtol(argv[4], NULL, 10);
			apin.value =strtol(argv[5], NULL, 10);		

			for(i = 0; i<loop; i++){	
			/*  Pass GPIO struct with IO control */
			ret =ioctl(fd,IOCTL_PIIO_GPIO_WRITE ,&apin);
			
			sleep(time);
			apin.value ^=1;
			}
		}


	}

	close(fd);
	return 0;
}
