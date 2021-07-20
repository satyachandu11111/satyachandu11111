define(
    [
        'HubBox_HubBox/js/model/hubbox'
    ],
    function(hubBox) {
        'use strict';
        return function (postCode) {
            hubBox.postCode(postCode);
        }
    }
);
