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
			
			if(data[k].featured_image !== null) {
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


			output += '<article>';
			
			// featured image thumbnail
			if(response[k].thumbnail !== undefined) {
				output += "<img src='" + response[k].thumbnail + "' style='float: left; margin-right: 20px;'>";
			}
			
			// title
			output += '<h2 style="clear: none;">' + highlightTerm(response[k].title, urlParams.s) + '</h2>';
			
			// excerpt
			output +=  highlightTerm(response[k].excerpt, urlParams.s);
			
			output += '</article>';
		})
		
		$('#wp-api-search-results').append(output);
	
	});

})( jQuery );


/* 
 * Highlight search term
 * 
 * @parameters content string and search term(s)
 * @returns string with search term wrapped with marked element
 *
*/
function highlightTerm(content, term) {
	term_arr = term.split(' ');

	term_arr.forEach(function(v, k){
		var find = new RegExp(v, 'gi'); //gi = global, ignore case
	 	content = content.replace(find, function(match) {
	 			return '<mark>' + match + '</mark>';
	 		}
	 	); 
	});

	return content;
}