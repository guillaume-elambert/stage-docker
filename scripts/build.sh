#!/bin/bash
source ./scripts/config

# For each image
for ((i=0;i<${#IMAGES_NAMES[@]} && i<${#DOCKERFILE_PATH[@]};i++));
do
    echo "Start building the \"${IMAGES_NAMES[i]}\" image..."

    # Build the image
    result=$(sudo docker build -q -t ${IMAGES_NAMES[i]} ${DOCKERFILE_PATH[i]}; echo $?)

    if [[ ! -z result ]];
    then
        echo "${green}Image \"${IMAGES_NAMES[i]}\" successfully built."
    else
        echo "${red}Failed to build \"${IMAGES_NAMES[i]}\" image."
    fi

    reset_color
done

reset_color