<?php

class StockistAdmin extends ModelAdmin {

	private static $managed_models = array(
        'Stockist'
	);

	private static $url_segment = 'distributers';

	private static $menu_title = 'Distributers';

	private static $menu_icon = 'mappable/images/ic_my_location_black_24dp_1x.png'; // TODO: use MODULE_MAPPABLE_DIR

}
