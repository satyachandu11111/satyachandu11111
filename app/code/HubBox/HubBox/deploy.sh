#!/usr/bin/env bash
aws s3 cp .dist/HubBox_HubBox-latest.zip  s3://files.hub-box.com/magento-2/HubBox_HubBox-latest.zip --acl public-read
aws s3 cp .dist/HubBox_HubBox-latest.zip  s3://files.hub-box.com/magento-2/HubBox_HubBox-`cat ./VERSION`.zip --acl public-read
