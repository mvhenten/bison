## install phantom.js
For Ubuntu 10.04 (lucid) see below.

    sudo apt-get install libqt4-dev libqtwebkit-dev qt4-qmake
    git clone git://github.com/ariya/phantomjs.git && cd phantomjs
    git checkout 1.3
    qmake-qt4 && make
    sudo cp phantomjs /usr/local/bin/

( taken from http://code.google.com/p/phantomjs/wiki/BuildInstructions)

## getting libqtwebkit-dev on 10.04 (supported on later versions)

1. Enable the repository:
    sudo add-apt-repository ppa:kubuntu-ppa/backports

2. Update the package index:
    sudo apt-get update

3. Install libqtwebkit-dev deb package:
    sudo apt-get install libqtwebkit-dev

## install Xvfb

    sudo apt-get install xvfb

start xvfb:

    Xvfb :1 -screen 0 1024x768x24

see http://code.google.com/p/phantomjs/wiki/XvfbSetup for setting up deamon.

## scrape css & render dir

    phantomjs js/transmediale.phantom.js http://www.transmediale.de/resource tm-resource.json
    php scripts/bison-cli.php tm-resource.json
