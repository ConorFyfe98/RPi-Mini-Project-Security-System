/*
 ============================================================================
 Name        : piio.c
 Author      : Conor Fyfe
 Version     : 0.1
 Copyright   : See Abertay copyright notice
 Description : RPi Zero Wireless - GPIO Driver
 ============================================================================
 */
#include "piio.h"

#include <linux/kernel.h>
#include <linux/module.h>
#include <linux/fs.h>
#include <asm/uaccess.h>
#include <linux/uaccess.h>

#include <linux/gpio.h>
#include <linux/cdev.h>
#include <linux/device.h>
#include <linux/seq_file.h>




static int DevBusy = 0;
static int MajorNum = 100;
static struct class*  ClassName  = NULL;
static struct device* DeviceName = NULL;

lkm_data lkmdata;
gpio_pin apin;

static int device_open(struct inode *inode, struct file *file)
{
	printk(KERN_INFO "device_open(%p)\n", file);

	if (DevBusy)
		return -EBUSY;

	DevBusy++;
	try_module_get(THIS_MODULE);
	return 0;
}

static int device_release(struct inode *inode, struct file *file)
{
	printk(KERN_INFO " device_release(%p)\n", file);
	DevBusy--;

	module_put(THIS_MODULE);
	return 0;
}


static int      device_ioctl(struct file *file, unsigned int cmd, unsigned long arg){
	int i;
	char *temp;
	char ch;

	printk("device_ioctl: Device IOCTL invoked : 0x%x - %u\n" , cmd , cmd);

	switch (cmd) {
	case IOCTL_PIIO_READ:
		strcpy(lkmdata.data ,"This is from lkm\0");
		lkmdata.len = 101;
		lkmdata.type = 'r';

		copy_to_user((void *)arg, &lkmdata, sizeof(lkm_data));
		printk("Device IOCTL WRITE\n");
		break;

	case IOCTL_PIIO_WRITE:
		printk("Device IOCTL READ\n");
		copy_from_user(&lkmdata, (lkm_data *)arg, sizeof(lkm_data));
		printk("From user: %s - %u - %c\n" , lkmdata.data , lkmdata.len , lkmdata.type);
		break;

	case IOCTL_PIIO_GPIO_READ:
		memset(&apin , 0, sizeof(apin));
		copy_from_user(&apin, (gpio_pin *)arg, sizeof(gpio_pin));
		gpio_request(apin.pin, apin.desc);
		apin.value = gpio_get_value(apin.pin);

		strcpy(apin.desc, "LKMpin");

		copy_to_user((void *)arg, &apin, sizeof(gpio_pin));
		printk("piio IOCTL_PIIO_GPIO_READ: pi:%u - val:%i - desc:%s\n" , apin.pin , apin.value , apin.desc);
		break;
	case IOCTL_PIIO_GPIO_WRITE:

		copy_from_user(&apin, (gpio_pin *)arg, sizeof(gpio_pin));
		gpio_request(apin.pin, apin.desc);
		gpio_direction_output(apin.pin, 0);
		gpio_set_value(apin.pin, apin.value);

		printk("piio IOCTL_PIIO_GPIO_WRITE: pi:%u - val:%i - desc:%s\n" , apin.pin , apin.value , apin.desc);


		break;
	default:
			printk("piio: command format error\n");
	}

	return 0;
}


struct file_operations Fops = {
	.unlocked_ioctl = device_ioctl,
	.open = device_open,
	.release = device_release,
};


static int __init piio_init(void){
	int ret_val;
	ret_val = 0;

	   printk(KERN_INFO "piio: Initializing the piio\n");
	   MajorNum = register_chrdev(0, DEVICE_NAME, &Fops);
	      if (MajorNum<0){
	         printk(KERN_ALERT "piio failed to register a major number\n");
	         return MajorNum;
	      }
	   printk(KERN_INFO "piio: registered with major number %d\n", MajorNum);


	   ClassName = class_create(THIS_MODULE, CLASS_NAME);
	   if (IS_ERR(ClassName)){
	      unregister_chrdev(MajorNum, DEVICE_NAME);
	      printk(KERN_ALERT "Failed to register device class\n");
	      return PTR_ERR(ClassName);
	   }
	   printk(KERN_INFO "piio: device class registered\n");


	   DeviceName = device_create(ClassName, NULL, MKDEV(MajorNum, 0), NULL, DEVICE_NAME);
	   if (IS_ERR(DeviceName)){
	      class_destroy(ClassName);
	      unregister_chrdev(MajorNum, DEVICE_NAME);
	      printk(KERN_ALERT "Failed to create the device\n");
	      return PTR_ERR(DeviceName);
	   }
	   printk(KERN_INFO "piio: device class created\n");


	return 0;

}

static void __exit piio_exit(void){
	   device_destroy(ClassName, MKDEV(MajorNum, 0));
	   class_unregister(ClassName);
	   class_destroy(ClassName);
	   unregister_chrdev(MajorNum, DEVICE_NAME);
	   gpio_free(apin.pin);
	   printk(KERN_INFO "piio: Module removed\n");
}
module_init(piio_init);
module_exit(piio_exit);
MODULE_LICENSE("GPL");
MODULE_AUTHOR("CMP408 - Conor Fyfe");
MODULE_DESCRIPTION("RPi GPIO Driver");
MODULE_VERSION("0.1");
