[![Build Status](https://travis-ci.org/GlobalFreightSolutions/gfs-selected-store.svg?branch=master)](https://travis-ci.org/GlobalFreightSolutions/gfs-selected-store)


# &lt;gfs-selected-store&gt;

The `gfs-selected-store` widget displays information on a droppoint. When used in conjuction with the checkout widget it will display the details of a droppoint selected from the map.

## Install

```bash
# via bower
$ bower install --save gfs-selected-store
```

## Usage

1. Import Web Components' polyfill:

```html
<script src="bower_components/webcomponentsjs/webcomponents-lite.js"></script>
```

2. Import Custom Element:

```html
<link rel="import" href="bower_components/gfs-selected-store/gfs-selected-store.html">
```

3. Start using it!

<!---
```
<custom-element-demo>
    <template>
        <script src="../webcomponentsjs/webcomponents-lite.js"></script>
        <link rel="import" href="gfs-selected-store.html">
        <next-code-block></next-code-block>
    </template>
</custom-element-demo>
```
-->

```html
<gfs-selected-store
    title="Your collection point"
    visible="true"
    store-data='{"droppointId":"DPD-123","isStore":true,"providerName":"DPD","distanceInMeters":888,"localizedDistance":"888 meters","droppointDescription":"The Pharmacy at Mayfair (Numark)","geoLocation":{"addressLines":["Shepherd Market"],"town":"London","postCode":"W1J 7UD","countryCode":"GB","directions":"The Pharmacy at Mayfair (Numark)"},"collectionSlots":[{"collectionDate":"2016-11-10T00:00:00Z","timeSlots":[{"from":"09:30","to":"17:00"}]}]}'>
</gfs-selected-store>
```

More info and all the available properties can be found at [GFS widget portal](http://developer.justshoutgfs.com/info/documentation/gfs-checkout/the-gfs-checkout-widgets/selected-store-widget/ "The GFS Selected Store Widget")


## License

[Apache License 2.0](https://www.apache.org/licenses/LICENSE-2.0.html)
