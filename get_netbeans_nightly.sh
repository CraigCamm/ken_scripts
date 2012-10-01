#!/bin/bash
REPO="http://bits.netbeans.org/download/trunk/nightly/latest/bundles/"
NIGHTLY="/root/netbeans-nightly/"

TODAY=$(curl $REPO 2>/dev/null | sed 's/^.*href="\(.*\)">.*$/\1/' | grep '[0-9]\-linux.sh')
TAG=${TODAY//[^0-9]/}

if [ ! -f "./$TODAY" ] ; then
    wget $REPO$TODAY --output-file=/root/netbeans-nightly/$TODAY
    chmod u+x $NIGHTLY$TODAY
    $NIGHTLY$TODAY --silent
    echo $TODAY >$NIGHTLY"current"
    rm /usr/local/netbeans-nightly
    ln -s /usr/local/netbeans-dev-$TAG/bin/netbeans /usr/local/netbeans-nightly
fi
