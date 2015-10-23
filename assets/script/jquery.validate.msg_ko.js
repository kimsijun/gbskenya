/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: KO (Korean; 한국어)
 */
(function ($) {
	$.extend($.validator.messages, {
		required: "You can't leave this empty.",
		remote: "You have already signed up.",
		email: "Please enter a valid email address",
		url: "Is an invalid address",
		date: "Please enter a valid date.",
		dateISO: "Please enter a valid date(ISO).",
		number: "Is not a valid number.",
		digits: "Only numbers can be entered.",
		creditcard: "Credit card number is invalid.",
		equalTo: "The passwords do not match.",
		accept: "It is not the correct extension.",
		maxlength: $.format("Can not exceed {0} characters."),
		minlength: $.format("Please enter at least {0} characters."),
		rangelength: $.format("Enter characters from {0} to {1}."),
		range: $.format("Enter a value from {0} to {1}."),
		max: $.format("Can not exceed {0}."),
		min: $.format("Please enter at least {0}.")
	});
}(jQuery));
