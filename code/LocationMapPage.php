<?php

class LocationMapPage extends Page
{

}

class LocationMapPage_Controller extends Page_Controller
{

    const API_KEY = "AIzaSyAAaa_ApoYASmy5j35SKI7q1UcLzvdxf2E";

    public function init()
    {
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
        'locationData', 'importAddressFile', 'getLocationFromAddress'
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
//                if (!$northtelClients) {
                $sqlInsert = "INSERT into NorthtelClients (Name, PhoneNumber, Address, City, Region, Postcode)
                   values ('" . $column[1] . "','" . $column[2] . "','" . $column[3] . "','" . $column[4] . "','" . $column[5] . "', " . $column[6] . ")";
                $result = mysqli_query($conn, $sqlInsert);

                if (!empty($result)) {
                    $type = "success";
                    $message = "CSV Data Imported into the Database";
                } else {
                    $type = "error";
                    $message = "Problem in Importing CSV Data";
                }
//                } else {
//                    continue;
//                }
            }
            $result = mysqli_query($conn, "commit;");
            fclose($file);
            $this->populateLocation();
        }
    }

    public function populateLocation()
    {
        // Get the locations from the database, exclude any that don't have LatLng's defined
        $northtelClients = NorthtelClients::get();

        if ($northtelClients) {
            foreach ($northtelClients as $obj) {
                if (empty($obj->Address)) continue;
                $test = $this->getLocationFromAddress($obj->Address, $obj->City, $obj->Region, $obj->Postcode);
                $obj->Lat = $test['lat'];
                $obj->Lng = $test['lng'];
                $obj->write();
            }
        }
    }

    public function locationData()
    {
        // Get the locations from the database, exclude any that don't have LatLng's defined
        $search = Controller::curr()->getRequest()->getVar('search');
        if (is_numeric($search)) {
            $search = $this->getRegionFromPostcode($search);
        }

        if (!empty($search) && $search != 'null') {
            $infoWindowList = NorthtelClients::get()->where("Postcode = '" . $search . "' OR " . "Address like '%" . $search . "%' OR " . "Name like '%" . $search . "%' OR " . "City = '" . $search . "'" . " OR " . "Region = '" . $search . "'");

        } else {
            $infoWindowList = NorthtelClients::get();
        }

        if ($infoWindowList) {
            $InfoWindows = array();
            foreach ($infoWindowList as $obj) {
                if (empty($obj->Address)) continue;
                $InfoWindows[] = array(
                    'lat' => $obj->Lat,
                    'lng' => $obj->Lng,
                    'info' => $obj->Name . '<br/> ' . $obj->Address . '<br/> ' . $obj->Postcode . '<br/><br/> ' . $obj->City . '<br/> ' . $obj->PhoneNumber,
                    'iconSize' => "0.6"
                );
            }
            $InfoWindowsJson = Convert::array2json($InfoWindows);
            // Return a JSON object for GoogleMapConfig.js to use
            return $InfoWindowsJson;
        }
    }

    private function getRegionFromPostcode($postcode)
    {
        if (empty($postcode)) {
            return;
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?key=' . self::API_KEY . '&address=' . $postcode . '+NZ';

        $options = array(
            "http" => array(
                "header" => "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
            )
        );

        $context = stream_context_create($options);
        $contents = file_get_contents($url, false, $context);

        $arr = json_decode($contents, true);
        if (empty($arr['results'])) {
            return;
        }

        foreach ($arr['results'] as $element) {
            $addressCompoents = $element['address_components'];
            foreach ($addressCompoents as $component) {
                if (!empty($component['types'][0]) && $component['types'][0] == "administrative_area_level_1") {
                    $googleRegion = $component['long_name'];
                }
            }
        }

        return $googleRegion;
    }

    public function getLocationFromAddress($address, $city, $region, $postcode)
    {
        if (empty($address)) {
            return;
        }
        //uses + between words on address
        $address = strstr($address, " ") ? str_replace(" ", "+", $address) : $address;

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?key=' . self::API_KEY . '&address=' . $address . '+' . $city . '+' . $region . '+' . $postcode;

        $options = array(
            "http" => array(
                "header" => "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
            )
        );

        $context = stream_context_create($options);
        $contents = file_get_contents($url, false, $context);

        $arr = json_decode($contents, true);
        if (empty($arr['results'])) {
            return;
        }
        $lat = '';
        foreach ($arr['results'] as $element) {
            $geometry = $element['geometry'];
            $location = $geometry['location'];
        }

        return $location;
    }

    public function locationDatas()
    {
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

    public function Map()
    {
        // The element to house the map
        $param = Controller::curr()->getRequest()->getVar('search');
        $map = '<div class="mt-3" id="map_canvas"></div>';
        return !empty($param) && $param != 'null'
            ? '<div class="text-center">Filtering by ' . $param . $map . '</div>'
            : $map;
    }
}
