<?php

class LocationMapPage extends Page {

}

class LocationMapPage_Controller extends Page_Controller {

	public function init() {
		parent::init();
		$vars = self::locationData();
		//Debug::show($vars);die();
		// Add jQuery, Map API, CSS and Config to the Page
		Requirements::javascript(MODULE_MAPPABLE_DIR . '/javascript/jquery-2.1.4.min.js');
		//TODO:: Remove the key, put it in settings
		Requirements::javascript('https://maps.googleapis.com/maps/api/js?key=[YOUR_KEY]');
		Requirements::javascriptTemplate(MODULE_MAPPABLE_DIR . '/javascript/GoogleMapConfig.js', $vars);

		Requirements::css(MODULE_MAPPABLE_DIR . '/css/mappable.css');


	}

	private static $allowed_actions = array(
		'locationData'
	);


	/* return ARRAY */
	public function locationData() {
		// Get the locations from the database, exclude any that don't have LatLng's defined
		$infoWindowList = Location::get()->exclude(array('lat' => null, 'lng' => null))->first();
		//Debug::show($infoWindowList);die();
		if ($infoWindowList) {

				$InfoWindows = array(
					'lat' => $infoWindowList->lat,
					'lng' => $infoWindowList->lng,
					'info' => $infoWindowList->Name// . "/<br />" . $obj->InfoWindow
				);

			//$InfoWindows = Convert::array2json($InfoWindows);
			// Return a JSON object for GoogleMapConfig.js to use
			//Debug::show($InfoWindows);
			return $InfoWindows;

		}
	}

	public function LocationInfo() {
		return Location::get()->exclude(array('lat' => null, 'lng' => null))->first();
	}

	public function BigMap() {
		// The element to house the map
		return '<div id="map_canvas"></div>';
	}
	public function SmallMap() {
		// The element to house the map
		return '<div id="map_canvas_small"></div>';
	}
}
