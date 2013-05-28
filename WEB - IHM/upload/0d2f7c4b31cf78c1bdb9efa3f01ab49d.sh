#!/bin/bash
beginSerial;
SetMotorOn("x, y");
echo -n "w" > /dev/ttyUSB0
endSerial;
 