#!/bin/bash
sudo printf ""

# Check if there is 1 parameter
if [[ ${#} -ne 1 ]]
then
    exit 1
fi

# Check if container exist
result=$( sudo docker ps -a -q -f name=$1 )

if [[ -z "$result" ]];
then
    exit 1
fi