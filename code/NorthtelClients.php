<?php

class NorthtelClients extends DataObject {

    private static $db = array(
        "Name" => "Varchar(255)",
        "Email" => "Varchar(255)",
        "PhoneNumber" => "Varchar(255)",
        "Address" => "Varchar(255)",
        "City" => "Varchar(255)",
        "Region" => "Varchar(255)",
        'Lat' => 'Double',
        'Lng' => 'Double',
        'Postcode' => 'Varchar(10)',
    );

	public static $summary_fields = array(
		'Name',
		'Email',
		'PhoneNumber',
		'Address',
		'City',
	);

	public function getTitle() {
		return $this->Name;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
//		$fields->removeFieldsFromTab('Root.Main', array('Name', 'InfoWindow', 'lat', 'lng'));

		$fields->addFieldToTab('Root.Main', new TextField('Name', 'Name', $this->Name));
		$fields->addFieldToTab('Root.Main', new TextField('Email', 'E-mail', $this->Name));
		$fields->addFieldToTab('Root.Main', new TextField('PhoneNumber', 'Phone Number', $this->Name));
		$fields->addFieldToTab('Root.Main', new TextField('Address', 'Address', $this->Name));
		$fields->addFieldToTab('Root.Main', new TextField('City', 'City', $this->Name));

		return $fields;
	}
}
