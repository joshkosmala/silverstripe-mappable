<?php

class Location extends DataObject
{

    private static $db = array(
        'Name' => 'Text',
        'Address1' => 'Text',
        'Suburb' => 'Varchar',
        'City' => 'Varchar',
        //'Region' => "Enum('Northland,Auckland,Coromandel,Waikato,Bay Of Plenty,East Cape,Taupo,King Country,Taranaki,Hawkes Bay,Wanganui,Wellington,Wairarapa,Nelson,Marlborough,West Coast,Canterbury,Otago,Southland', 'Auckland')",
        //TODO: Use nzregiondropdownfield module?
        'Region' => "Enum('Auckland,Bay Of Plenty,Canterbury,Coromandel,East Cape,Hawkes Bay,King Country,Marlborough,Nelson,Northland,Otago,Southland,Taranaki,Taupo,Waikato,Wairarapa,Wanganui,Wellington,West Coast', 'Auckland')",
        'InfoWindow' => 'HTMLText',
        'lat' => 'Double',
        'lng' => 'Double',
    );

    public static $summary_fields = array(
        'Name',
        'City',
        'Region'
    );

    public function getTitle()
    {
        return $this->Name;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeFieldsFromTab('Root.Main', array('Name', 'InfoWindow', 'lat', 'lng'));

        $fields->addFieldToTab('Root.Main', new TextField('Name', 'Name', $this->Name), 'Address1');
        $fields->addFieldToTab('Root.Main', $infoWindow = HtmlEditorField::create('InfoWindow'));
        $infoWindow->setRows(8); // limit the height of the editor
        $fields->addFieldToTab('Root.Main', new TextField('lat', 'Latitude', $this->lat), 'InfoWindow');
        $fields->addFieldToTab('Root.Main', new TextField('lng', 'Longitude', $this->lng), 'InfoWindow');

        return $fields;
    }

    public function onBeforeWrite()
    {
        // TODO: Reinstate the checks below
        parent::onBeforeWrite();
        // If the record doesn't have Lat/Lng OR the address has changed, get a new geolocation
        if (!$this->lat || !$this->lng || $this->isChanged('Address1')) {
            // If the record has an address, suburb and region, retrieve the geolocation (line 52)
            if ($this->Address1 && $this->Suburb && $this->Region) {
                // Get the member's address
                $address = $this->Address1 . ' ' . $this->Suburb . ' ' . $this->Region;
                //$address = $this->Address1;
                // Run it through GoogleGeocoding's address_to_point function (RestfulService/XML)
                $point = GoogleGeocoding::address_to_point($address);
                // Set the lat and lng values with the response
                $this->lat = $point['Latitude'];
                $this->lng = $point['Longitude'];
            }
        }
    }
}
