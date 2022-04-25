window.addEventListener('load', function() {
	js_sv_tracking_manager_extended_plausible_scripts_events.forEach(function(event, index){
		let elements = document.querySelectorAll(event.element);
		for (var i = 0; i < elements.length; i++) {
			elements[i].addEventListener('click',  function () {
				plausible(event.goal);
			});
		}
	});
});