#!/bin/bash
source ./scripts/config

# For each container name
for (( i=0; i<${#CONTAINERS_NAMES[@]}; i++ ));
do
    container=${CONTAINERS_NAMES[i]}

    # Check if container exist
    result=$( ./scripts/check-container.sh $container ; echo $? )

    if [[ $result -ne 0 ]]; then
        echo "${yellow}Container \"$container\" doesn't exist."
        continue
    fi

    # If so check if running
    result=$( sudo docker ps -q -f name=$container )

    if [[ -z $result ]]; then
        echo "${yellow}Container \"$container\" has already been stopped."
        continue
    fi

    # Check if container has been stopped
    result=$( sudo docker stop $container | echo $? )
    
    if [[ $result -eq 0 ]]; then
        echo "${green}Container \"$container\" has been stopped."
    else
        echo "${red}Failed to stop \"$container\" container."
    fi

done

reset_color