# PicturPlayer
Plays pictures in your browser as if it was a movie. Ideal for checking saved images of cheap security cams.

## Purpose 
Low-end security cameras can usually save images to a webserver via FTP whenever they detect motion.

However these cameras often provide you with a lot of false positives. It can therefore be quite time consuming 
to look through all those images every day.

That is the reason why this little tool has been developed. It automatically displays all images 
found in a directory in successive order at arond 10 frames per second (depends on network speed).

## Features
- Support for multiple cameras
- Easy installation with almost no configuration necessary
- Pause whenever you want and/or jump directly to a specific frame
- Allows playback to be in reverse or normal mode
- A progress bar and a countdown indicate the remaining time
- Indicators for amount of pictures and total space used per camera
- Easily delete old footage once you have checked it with just 1 click
- Displays timestamp and filename of each frame

## Prerequisites
``PHP 5.6`` or better

## Installation
Just place all files in a directory above the one where your camera saves its images. 

**Example**
Let's say your cameras are already setup to use this directory structure:

``/home/camera/camera01``

``/home/camera/camera02``

Then you need to place all files inside ``/home/camera``. 

**Security note**

Please use ``.htaccess`` protection or something similiar to prevent public access to this script. 
It incorporates some basic user input checks, but it's best not to rely on that.

If you find any bugs or have recommendations (either regarding code or features), please let me know!

## Usage

Usage is pretty straight forward. There are only 2 things to look out for:

1. If you want to jump to a frame, you have to hit pause first.
2. If you want to jump to a switch between normal and reverse playback, you have to click on the reload button 
for the changes to take effect.

## License

GNU General Public License v3.0
