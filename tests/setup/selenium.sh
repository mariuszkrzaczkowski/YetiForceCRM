#!/bin/bash

apt-get install -y --no-install-recommends xvfb xauth

serverUrl='http://127.0.0.1:4444'
CHROME_DRIVER_VERSION=`curl -sS chromedriver.storage.googleapis.com/LATEST_RELEASE`
SELENIUM_STANDALONE_VERSION=3.6.0
SELENIUM_SUBDIR=$(echo "$SELENIUM_STANDALONE_VERSION" | cut -d"." -f-2)
HOME=/var/www/

if [ ! -f $HOME/chromedriver_linux64.zip ]; then wget -N http://chromedriver.storage.googleapis.com/$CHROME_DRIVER_VERSION/chromedriver_linux64.zip -q -O $HOME/chromedriver_linux64.zip; fi
unzip $HOME/chromedriver_linux64.zip -d ~/
mv -f ~/chromedriver /usr/local/bin/chromedriver
chown root:root /usr/local/bin/chromedriver
chmod 0755 /usr/local/bin/chromedriver

if [ ! -f $HOME/selenium-server-standalone.jar ]; then wget -N http://selenium-release.storage.googleapis.com/$SELENIUM_SUBDIR/selenium-server-standalone-$SELENIUM_STANDALONE_VERSION.jar -q -O $HOME/selenium-server-standalone.jar; fi
chmod 0755 $HOME/selenium-server-standalone.jar

xvfb-run java -Dwebdriver.chrome.driver=/usr/local/bin/chromedriver -jar $HOME/selenium-server-standalone.jar -debug > /tmp/selenium.log &
wget --retry-connrefused --tries=60 --waitretry=1 --output-file=/dev/null $serverUrl/wd/hub/status -O /dev/null
if [ ! $? -eq 0 ]; then
    echo "Selenium Server not started"
else
    echo "Finished setup"
fi
