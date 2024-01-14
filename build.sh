ANGULARBLOG_OUTPUT_PATH=/volume/angularblog.com

build(){
   ng build --output-path=$ANGULARBLOG_OUTPUT_PATH
   cp .env $ANGULARBLOG_OUTPUT_PATH
   cp .htaccess $ANGULARBLOG_OUTPUT_PATH
   composer install
   npm i
   cp -r vendor $ANGULARBLOG_OUTPUT_PATH
   chmod -R 775 $ANGULARBLOG_OUTPUT_PATH
   chown -R www-data: $ANGULARBLOG_OUTPUT_PATH
}

build
