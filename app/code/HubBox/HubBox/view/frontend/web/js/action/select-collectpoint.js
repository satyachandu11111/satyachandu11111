define(
    [
        'HubBox_HubBox/js/model/hubbox'
    ],
    function(hubBox) {
        'use strict';
        return function (collectPoint) {
            hubBox.selectedCollectPoint(collectPoint);
        }
    }
);
