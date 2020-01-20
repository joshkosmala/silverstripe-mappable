<?php

class LocationMapPage extends Page {

}

class LocationMapPage_Controller extends Page_Controller {

    public function init() {
        parent::init();

        // Add jQuery, Map API, CSS and Config to the Page
        Requirements::javascript(MODULE_MAPPABLE_DIR . '/javascript/jquery-2.1.4.min.js');
        //TODO:: Remove the key, put it in settings
        Requirements::javascript('https://maps.googleapis.com/maps/api/js?key=AIzaSyAAaa_ApoYASmy5j35SKI7q1UcLzvdxf2E');
        Requirements::javascript(MODULE_MAPPABLE_DIR . '/javascript/GoogleMapConfig.js');

        Requirements::css(MODULE_MAPPABLE_DIR . '/css/mappable.css');
        self::locationData();

    }

    private static $allowed_actions = array(
        'locationData', 'test'
    );

    public function getImportAddressFile($file) {
        $address = array();
        foreach ()
    }

    public function getRanges() {
        // Get the locations from the database, exclude any that don't have LatLng's defined
        $infoWindowList = Location::get();
        $test = $this->getLocationFromAdress('tauranga');

        if ($infoWindowList) {
            $InfoWindows = array();
            foreach ($infoWindowList as $obj) {
                $InfoWindows[] = array(
                    'lat' => $test['lat'],
                    'lng' => $test['lng'],
                    'info' => $obj->Name . "<br />" . $obj->InfoWindow,
                    'iconSize' => $obj->IconSize
                );
            }
            $InfoWindowsJson = Convert::array2json($InfoWindows);
            // Return a JSON object for GoogleMapConfig.js to use
            return $InfoWindowsJson;
        }
    }

    public function getLocationFromAdress($address) {
        if (empty($address)) {
            return;
        }
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAAaa_ApoYASmy5j35SKI7q1UcLzvdxf2E&address='.$address;

        //Use file_get_contents to GET the URL in question.
        $contents = file_get_contents($url);

        $arr = json_decode($contents, true);
        $lat = '';
        foreach ($arr['results'] as $element) {
            $geometry = $element['geometry'];
            $location = $geometry['location'];
        }

        //If $contents is not a boolean FALSE value.
//        if($contents !== false){
//            //Print out the contents.
//        }

//        return json_encode($location);
        return $location;
    }

    public function locationData() {
        // Get the locations from the database, exclude any that don't have LatLng's defined
		$infoWindowList = Location::get();

        if ($infoWindowList) {
            $InfoWindows = array();
            foreach ($infoWindowList as $obj) {
                $InfoWindows[] = array(
                    'lat' => $obj->lat,
                    'lng' => $obj->lng,
                    'info' => $obj->Name . "<br />" . $obj->InfoWindow,
                    'iconSize' => $obj->IconSize
                );
            }
            $InfoWindowsJson = Convert::array2json($InfoWindows);
            // Return a JSON object for GoogleMapConfig.js to use
            return $InfoWindowsJson;
        }
    }

    public function Map() {
        // The element to house the map
        return '<div id="map_canvas"></div>';
    }
}
