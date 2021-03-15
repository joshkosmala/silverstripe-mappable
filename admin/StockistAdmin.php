<?php

class StockistAdmin extends ModelAdmin {

	private static $managed_models = array(
        'Stockist'
	);

	private static $url_segment = 'stockists';

	private static $menu_title = 'Stockists';

	private static $menu_icon = 'mappable/images/ic_my_location_black_24dp_1x.png'; // TODO: use MODULE_MAPPABLE_DIR

}
