define(
    [
        'HubBox_HubBox/js/model/hubbox'
    ],
    function(hubBox) {
        'use strict';
        return function (last) {
            hubBox.lastName(last);
        }
    }
);
