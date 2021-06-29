#!/bin/bash

sudo docker rm -f webservice
sudo docker build -t webservice webservice/
sudo docker run -d -p 8080:8080 --name webservice webservice

WEBSERVICE_IP=$(sudo docker container inspect -f '{{ .NetworkSettings.IPAddress }}' webservice)
WEBSERVICE_PORT=8080


sudo docker rm -f encode-explorer
sudo docker build -t encode-explorer encode-explorer/
sudo docker run -d -p 80:80 -e WEBSERVICE_IP=$WEBSERVICE_IP -e WEBSERVICE_PORT=$WEBSERVICE_PORT --name encode-explorer encode-explorer

#docker container inspect -f '{{ .NetworkSettings.IPAddress }}'

#docker container inspect -f '{{ .NetworkSettings.Ports }}' webservice | sed -e 's/\([0-9]*\)\/tcp.*/\1/'