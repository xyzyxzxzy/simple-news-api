#!/bin/bash
PWD=$(pwd)
NAME=$(cat $PWD/dockerName)
IMAGENAME=$(cat $PWD/name)

docker run -it \
    -p 8000:8000 \
    --rm \
    --network dev \
    -v /$PWD:/app \
    --hostname $NAME \
    --name $NAME $IMAGENAME $@

exit 0
