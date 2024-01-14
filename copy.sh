#!/bin/bash

ANGULARBLOG_OUTPUT_PATH=/volume/angularblog.com

copy(){
    composer install
    cp .env $ANGULARBLOG_OUTPUT_PATH
    cp .htaccess $ANGULARBLOG_OUTPUT_PATH
    cp -r ./src/assets $ANGULARBLOG_OUTPUT_PATH/src
    cp -r vendor $ANGULARBLOG_OUTPUT_PATH
}

copy