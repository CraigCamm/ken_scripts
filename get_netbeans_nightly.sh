#!/bin/bash
# Copyright 2012 Kenneth Mitchner
# 
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
# 
# http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.


# Config
REPO="http://bits.netbeans.org/download/trunk/nightly/latest/bundles/"
DOWNLOAD=/root/netbeans-nightly
HOME=/home/kmitchner
JVI=$DOWNLOAD/nbvi-1.4.6


if [ ! -d $JVI ] ; then
    wget --directory-prefix=$DOWNLOAD http://downloads.sourceforge.net/project/jvi/jVi-for-NetBeans/NetBeans-7.0/nbvi-1.4.6.zip
    unzip $DOWNLOAD/nbvi-1.4.6.zip -d $DOWNLOAD
fi

PLUGINS=$(ls $JVI/*.nbm)

TODAY=$(curl $REPO 2>/dev/null | sed 's/^.*href="\(.*\)">.*$/\1/' | grep '[0-9]\-linux.sh')
TAG=${TODAY//[^0-9]/}

if [ ! -f "./$TODAY" ] ; then
    # Get nightly Netbeans Build
    wget --directory-prefix=$DOWNLOAD $REPO$TODAY
    # Make it executable
    LAST=$(ls /usr/local/ | grep netbeans-dev- | tail -n1)
    mv $HOME/.netbeans $HOME/.$LAST
    sh $DOWNLOAD$TODAY --silent
    echo $TODAY >$DOWNLOAD"current"
    rm /usr/local/netbeans-nightly
    ln -s /usr/local/netbeans-dev-$TAG/bin/netbeans /usr/local/netbeans-nightly
    mkdir -p $HOME/.netbeans/dev/update/download
    mkdir -p $HOME/.netbeans/dev/config/Preferences/org/netbeans/modules
    cp $PLUGINS $HOME/.netbeans/dev/update/download
    NUSER=$(stat -c %U $HOME)
    NGROUP=$(stat -c %U $HOME)
    chown $NUSER.$NGROUP $HOME/.netbeans -R
    CONFIG=$HOME/.netbeans/dev/config/Preferences/org/netbeans/modules/jvi.properties
    touch $CONFIG
    chown $NUSER.$NGROUP $CONFIG
    echo "viAutoPopupCcName=false" >>$CONFIG
    echo "viAutoPopupCcName=false" >>$CONFIG
    echo "viBackspace=2" >>$CONFIG
    echo "viExpandTabs=true" >>$CONFIG
    echo "viShiftRound=true" >>$CONFIG
    echo "viShiftWidth=4" >>$CONFIG
    echo "viTabStop=4" >>$CONFIG
fi
