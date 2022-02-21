window.addEventListener('load', function() {
	[...document.querySelectorAll('#sv_tracking_manager_extended_usercentrics_open_settings')].forEach(toggle_link => {
		toggle_link.addEventListener('click', function(e) {
			e.preventDefault();
			UC_UI.showSecondLayer();
		});
	});
});