# HubBox Magento2 Module #

Docs: https://hubbox.atlassian.net/wiki/spaces/M2/overview

# INSTALLATION #

* Setup: Add HubBox folder to app/code directory then run php bin/magento setup:upgrade from your root directory
* Once the module is installed run php bin/magento setup:di:compile
* Configuration: is under STORES/CONFIGURATION > SALES > HUBBOX

### CONSOLE COMMANDS ###

* REFRESH ACCESS TOKEN: php bin/magento hubbox:refreshtoken
* SYNC ORDERS:          php bin/magento hubbox:syncorders 

### CRON GROUP ###
* Scheduled tasks are under the Cron tab in Stores > Configuration > Advanced > System hubbox_hubbox

