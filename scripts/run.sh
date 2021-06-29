#!/bin/bash
source ./scripts/config

# For each container and associated run argument
for ((i=0; i<${#RUN_ARGS[@]} && i<${#CONTAINERS_NAMES[@]}; i++));
do
    container=${CONTAINERS_NAMES[i]}

    # Check if container has already been created
    if [[ $( ./scripts/check-container.sh $container ; echo $? ) -eq 1 ]]; then

        echo "Running container \"$container\"..."
        
        # If not, create it
        result=$(sudo docker run ${RUN_ARGS[i]}; echo $? )
        
        if [[ ! -z $result ]]; then
            echo "${green}Container \"$container\" is running."
        else
            echo "${red}Failed to run \"$container\" container."
        fi
        
    else
        echo "${yellow}Container \"$container\" has already been created."
    fi

    update_config
done

reset_color