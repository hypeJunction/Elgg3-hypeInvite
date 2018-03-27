define(function(require) {

	var Ajax = require('elgg/Ajax');
	var $ = require('jquery');

	$(document).on('click', '.elgg-menu-user-invite-request > li > a', function(e) {
		e.preventDefault();

		var $elem = $(this);
		var ajax = new Ajax();

		ajax.action($elem.attr('href')).done(function() {
			$elem.closest('tr').slideUp('fast').remove();
		});
	});
});