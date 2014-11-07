// Get URL parameters
// http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript#answer-2880929
var urlParams;
(window.onpopstate = function () {
    var match,
        pl     = /\+/g,  // Regex for replacing addition symbol with a space
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
        query  = window.location.search.substring(1);

    urlParams = {};
    while (match = search.exec(query))
       urlParams[decode(match[1])] = decode(match[2]);
})();


(function($) {
	// search term
	var wp_api_query_str = '?filter[s]=' + urlParams.s;

	// pulling post types from localize script on wp-api-search-lookup.
	var post_types = wp_api_search_vars.wp_api_search_post_types;

	// setting up multiple post types
	$.each(post_types, function(k, v) {
		wp_api_query_str += '&type[]=' + post_types[k]
	});

	// posts per page from localize script on wp-api-search-lookup
	//wp_api_query_str += '&filter[posts_per_page]=1';

	// static for now..need to make endpoint dynamic
	var wp_api_url = 'http://localhost:8080/wp-json/posts';

	$.ajax({
		url: wp_api_url + wp_api_query_str,
		method: 'GET',
		
	}).done(function(data) {
		console.log(data);
		var response = [];

		$.each(data, function(k, v) {
			var specificProps = {
				title: data[k].title
			};
																					// will be highlighted content section
			specificProps.content = data[k].content;
			
			if(data[k].featured_image !== null) {
				//console.log(data[k].featured_image.attachment_meta.sizes.thumbnail.url);
				specificProps.thumbnail = data[k].featured_image.attachment_meta.sizes.thumbnail.url;
			}

			response.push(specificProps);
		});

		console.log(response);

	});

})( jQuery );