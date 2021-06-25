#!/bin/bash
source ./scripts/config

# For each image
for ((i=0;i<${#IMAGES_NAMES[@]} && i<${#IMAGES_DOCKERFILE_PATH[@]};i++));
do
    echo "Start building the \"${IMAGES_NAMES[i]}\" image..."

    # Build the image
    result=$(sudo docker build -q -t ${IMAGES_NAMES[i]} ${IMAGES_DOCKERFILE_PATH[i]}; echo $?)

    if [[ ! -z result ]];
    then
        echo "Image \"${IMAGES_NAMES[i]}\" built."
    else
        echo "Failed to build \"${IMAGES_NAMES[i]}\" image."
    fi
done