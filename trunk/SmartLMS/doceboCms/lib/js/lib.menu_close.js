
function setup_menu( search_for, class_open, class_close ) {

	var all_observable = $$('li.open_close');

	for(var i = 0;i < all_observable.length; i++) {
		all_observable[i].addClassName(class_close);
	}
}

var ManDropdown = {

	open_menu: function(obj_li) {
		//obj_li.className = 'menu_close';
		obj_li.className = 'menu_open';
	},

	close_menu: function(obj_li) {
		//obj_li.className = 'menu_open';
		obj_li.className = 'menu_close';
	}

};
