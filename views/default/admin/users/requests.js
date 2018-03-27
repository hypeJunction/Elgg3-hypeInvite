define(function (require) {

	var $ = require('jquery');

	$(document).on('change', '.elgg-table-check-all', function () {
		var target = '[name="' + $(this).attr('target') + '"]';
		if ($(this).is(':checked')) {
			$(target).prop('checked', true);
		} else {
			$(target).prop('checked', false);
		}
	});

});