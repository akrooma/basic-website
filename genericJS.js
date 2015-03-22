$(document).ready(function () {
			
			//muudab ülemise menüüriba elementide (kastide) värvust.
			$(".mainMenu a").on({
				mouseenter: function () {
					$(this).css("background-color", "whitesmoke");
				},
				
				mouseleave: function () {
					$(this).css("background-color", "lightgray");
				}
				
			});
			
});