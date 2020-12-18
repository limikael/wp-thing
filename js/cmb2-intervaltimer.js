(function($) {
	'use strict'

	function enableField(field) {
		$(field).prop("disabled",false);

		if (!$(field).val())
			$(field).val(0);
	}

	function disableField(field) {
		$(field).prop("disabled",true);
		$(field).val(null);
	}

	function updateIntervalTimerFields() {
		console.log("blabla...");

		$(".cmb2-intervaltimer").each(function() {
			$(this).children(".it-interval").off("change",updateIntervalTimerFields);
			$(this).children(".it-interval").on("change",updateIntervalTimerFields);
			let sel=$(this).children(".it-interval").val();

			switch (sel) {
				case "day":
					enableField($(this).children(".it-hour"));
					enableField($(this).children(".it-minute"));
					enableField($(this).children(".it-second"));
					break;

				case "hour":
					disableField($(this).children(".it-hour"));
					enableField($(this).children(".it-minute"));
					enableField($(this).children(".it-second"));
					break;

				case "minute":
					disableField($(this).children(".it-hour"));
					disableField($(this).children(".it-minute"));
					enableField($(this).children(".it-second"));
					break;

				default:
					disableField($(this).children(".it-hour"));
					disableField($(this).children(".it-minute"));
					disableField($(this).children(".it-second"));
			}
		});
	}

    $( '.cmb2-wrap > .cmb2-metabox' ).on( 'cmb2_add_row', function() {
    	console.log("adding row...");
        updateIntervalTimerFields();
    });

	updateIntervalTimerFields();
})(jQuery);