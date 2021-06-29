#!/bin/bash
source ./scripts/config

# For each container name and associated start arguments
for (( i=0; i<${#START_ARGS[@]} && i<${#CONTAINERS_NAMES[@]}; i++ ));
do
    container=${CONTAINERS_NAMES[i]}

    # Check if container exist
    result=$( ./scripts/check-container.sh $container ; echo $? )

    if [[ $result -eq 0 ]];
    then

        # If so check if running
        result=$( sudo docker ps -q -f name=$container )

        if [[ ! -z $result ]]; then
            echo "${yellow}Container \"$container\" has already been started." "Abort."
            continue
        fi

        # If so, start it
        result=$(sudo docker start ${START_ARGS[i]} $container | echo $?)

        if [[ result -eq 0 ]]; then
            echo "${green}Container \"$container\" started."
        else
            echo "${red}Failed to start \"$container\" container."
        fi
    else 
        echo "${yellow}Container \"$container\" doesn't exist."
    fi

    reset_color

done

reset_color