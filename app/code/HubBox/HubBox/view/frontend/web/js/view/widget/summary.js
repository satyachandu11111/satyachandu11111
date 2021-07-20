define(
  [
    'jquery',
    'ko',
    'uiComponent',
    'underscore',
    'googlemaps!',

    'HubBox_HubBox/js/model/hubbox',
    'HubBox_HubBox/js/action/unconfirm-collectpoint',
    'HubBox_HubBox/js/action/show-widget',
    'HubBox_HubBox/js/action/view-explore',
    'HubBox_HubBox/js/action/view-info',
    'HubBox_HubBox/js/action/set-postcode',

    'HubBox_HubBox/js/model/collectpoints',
  ],
  function (
    $,
    ko,
    Component,
    _,
    googleMaps,

    hubBox,
    action_unConfirmCollectPoint,
    action_showWidget,
    action_viewExplore,
    action_viewInfo,
    action_setPostCode,

    CollectPoints
  ) {
    'use strict';
    return Component.extend({
      defaults: {
        template: 'HubBox_HubBox/widget/summary',
      },

      show: hubBox.collectPointConfirmed,
      collectPoint: hubBox.collectPoint,

      isHubBox: ko.computed(function(){
          return (hubBox.collectPoint() && hubBox.collectPoint().type === 'hubbox');
      }),

      confirmedBack: function() {
        action_unConfirmCollectPoint();
        action_showWidget();
        // do we have a postcode? ok lets do the search again, take them back
          if (sessionStorage.hubBox_postCode) {
              var postCode = JSON.parse(sessionStorage.hubBox_postCode);
              action_setPostCode(postCode);
              CollectPoints.getNearest(postCode);
              action_viewExplore();
          } else {
              action_viewInfo();
          }
      },

      getPrivatePickupMessage: function () {
          return window.checkoutConfig.hubBox.privatePickupMessage;
      }

    });
  }
);
