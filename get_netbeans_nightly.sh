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
