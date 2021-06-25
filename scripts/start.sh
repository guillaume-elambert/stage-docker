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
        # If so, start it
        result=$(sudo docker start ${START_ARGS[i]} $container | echo $?)

        if [[ result -eq 0 ]]; then
            echo "Container \"$container\" started."
        else
            echo "Failed to start \"$container\" container."
        fi
    fi

done