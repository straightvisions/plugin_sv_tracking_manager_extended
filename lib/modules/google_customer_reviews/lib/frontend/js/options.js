window.renderOptIn = function() {
	window.gapi.load('surveyoptin', function() {
		window.gapi.surveyoptin.render({
			"merchant_id": js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.merchant_id,
			"order_id": js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.order_id,
			"email": js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.email,
			"delivery_country": js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.delivery_country,
			"estimated_delivery_date": js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.estimated_delivery_date,
			"products": js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.products,
			"opt_in_style": js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.opt_in_style
		});
	});
}
window.___gcfg = {
	lang: js_sv_tracking_manager_extended_google_customer_reviews_scripts_options.lang
};