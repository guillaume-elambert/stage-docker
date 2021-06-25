#!/bin/bash
source ./scripts/config

# For each container
for container in "${CONTAINERS_NAMES[@]}"
do
    # Check if container exist
    result=$( ./scripts/check-container.sh $container ; echo $? )
    
    if [[ $result -eq 0 ]];
    then
    # If so, delete it
        echo "Container $(docker rm -f $container | echo $container ) removed."
    fi

done