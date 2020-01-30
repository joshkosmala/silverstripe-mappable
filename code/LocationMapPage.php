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
        'locationData', 'importAddressFile'
    );

    public function importAddressFile()
    {
        $conn = mysqli_connect("localhost", "root", "root", "subbase");

        if (isset($_POST["file"])) {

            $fileName = $_POST["file"];

            $file = fopen($fileName, "r");

            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($column[0] == 'Type' || $column[0] == 'First Name' || $column[0] == 'Name') {
                    continue;
                }

                $name = $column[0];
                if (!empty($column[0])) {
                    $name = strstr($column[0], "'") ? str_replace("'", "''", $column[0]) : $column[0];
                }
                $northtelClients = NorthtelClients::get()->where(" Name = '" . $name . "' OR Email = '" . $column[1] . "'")->exists();
                if (!$northtelClients) {
                    $sqlInsert = "INSERT into NorthtelClients (Name, Email, PhoneNumber, Address, City)
                   values ('" . $name . "','" . $column[1] . "','" . $column[2] . "','" . $column[5] . "','" . $column[6] . "')";
                    $result = mysqli_query($conn, $sqlInsert);

                    if (!empty($result)) {
                        $type = "success";
                        $message = "CSV Data Imported into the Database";
                    } else {
                        $type = "error";
                        $message = "Problem in Importing CSV Data";
                    }
                } else {
                    continue;
                }


            }
        }
        fclose($file);
    }

    public function getRanges() {
        // Get the locations from the database, exclude any that don't have LatLng's defined
        $infoWindowList = Location::get();
        //uses + between words on address
        $test = $this->getLocationFromAddress('Little+Queen+St+Russel');

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

    public function getLocationFromAddress($address) {
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
