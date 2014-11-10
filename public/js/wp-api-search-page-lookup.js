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

	wp_api_query_str += '&page=1';
	// setting up multiple post types
	$.each(post_types, function(k, v) {
		wp_api_query_str += '&type[]=' + post_types[k]
	});

	var wp_api_url = wp_api_search_vars.site_url + '/wp-json/posts';

	$.ajax({
		url: wp_api_url + wp_api_query_str,
		method: 'GET',
		
	}).done(function(data) {
		console.log(data);
		var response = [];

		$.each(data, function(k, v) {
			var specificProps = {
				title: data[k].title,
				content: data[k].content,
				excerpt: data[k].excerpt,
				date: data[k].date,
			};
																					// will be highlighted content section
			//specificProps.content = data[k].content;
			
			if(data[k].featured_image !== null) {
				//console.log(data[k].featured_image.attachment_meta.sizes.thumbnail.url);
				specificProps.thumbnail = data[k].featured_image.attachment_meta.sizes.thumbnail.url;
			}

			response.push(specificProps);
		});

		console.log(response);
		var output = '';
		$.each(response, function(k, v) {
			// Look up moment.js or Date
			// var date = Date.parse(response[k].date);
			// date = new Date(date);
			// console.log(date.getFullMonth());


			output += '<article><h2>' + response[k].title + '</h2>';
			output += '<p>' + response[k].excerpt + '</p>';
			output += '</article>';
		})
		$('#wp-api-search-results').append(output);
	});

})( jQuery );