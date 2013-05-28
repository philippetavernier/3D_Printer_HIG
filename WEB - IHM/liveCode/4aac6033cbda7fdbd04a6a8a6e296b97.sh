#!/bin/bash

# X Avant
echo -n \'w\' &gt; /dev/ttyUSB0

sleep 2

#Stop!
echo -n \'s\' &gt; /dev/ttyUSB0;
