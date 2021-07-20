define(
    [
        'HubBox_HubBox/js/model/hubbox'
    ],
    function(hubBox) {
        'use strict';
        return function (first) {
            hubBox.firstName(first);
        }
    }
);
