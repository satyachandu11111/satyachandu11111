define([
    'async'
], function(async) {
    var root = this;
    var googlemapsPlugin = {
        load: function(name, parentRequire, onload, opt_config) {
            var googleMapsLoader;
            var config = opt_config || {};

            if (config.isBuild) {
                onload(null);
                return;
            }

            googleMapsLoader = new GoogleMapsLoader(parentRequire, onload, config.googlemaps || {});
            googleMapsLoader.load();
        }
    };

    var GoogleMapsLoader = function(require, onload, config) {
        this.require_ = require;
        this.onload_ = onload || NOOP;
        this.baseUrl_ = config.url || GoogleMapsLoader.DEFAULT_BASE_URL;
        this.async_ = config.async || async;
        this.params_ = config.params;
    };


    GoogleMapsLoader.prototype.load = function() {
        if (this.isGoogleMapsLoaded_()) {
            this.resolveWith_(this.getGlobalGoogleMaps_());
        }
        else {
            this.loadGoogleMaps_();
        }
    };


    GoogleMapsLoader.prototype.loadGoogleMaps_ = function() {
        var self = this;

        var onAsyncLoad = function() {
            // Ensure correct context
            self.resolveWithGoogleMaps_(self);
        };
        onAsyncLoad.onerror = this.onload_.onerror;

        this.async_.load(this.getGoogleUrl_(), this.require_, onAsyncLoad, {});
    };


    GoogleMapsLoader.prototype.getGoogleUrl_ = function() {
        return this.baseUrl_ + '?libraries=geometry,places&key=' + window.checkoutConfig.hubBox.googleMapsKey;
    };

    GoogleMapsLoader.prototype.resolveWithGoogleMaps_ = function() {
        if (!this.isGoogleMapsLoaded_()) {
            this.reject_();
            return;
        }

        this.resolveWith_(this.getGlobalGoogleMaps_());
    };

    GoogleMapsLoader.prototype.isGoogleMapsLoaded_ = function() {
        return root.google && root.google.maps;
    };


    GoogleMapsLoader.prototype.getGlobalGoogleMaps_ = function() {
        return root.google ? root.google.maps : undefined;
    };


    GoogleMapsLoader.prototype.resolveWith_ = function(var_args) {
        this.onload_.apply(root, arguments);
    };


    GoogleMapsLoader.prototype.reject_ = function(opt_error) {
        var error = opt_error || new Error('Failed to load Google Maps library.');

        if (this.onload_.onerror) {
            this.onload_.onerror.call(root, error);
        }
        else {
            throw error;
        }
    };


    GoogleMapsLoader.DEFAULT_BASE_URL = 'https://maps.googleapis.com/maps/api/js';


    function NOOP() {
    }


    return googlemapsPlugin;
});