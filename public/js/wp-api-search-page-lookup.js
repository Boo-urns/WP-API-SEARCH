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

	var page = urlParams.page ? urlParams.page : 1;
	var currentPage = page;
	var posts_per_page = wp_api_search_vars.posts_per_page;

	if(page > 1) {
		posts_per_page *= page;	
		var loadingPage = 1;
		page = 1;
	}

	// setting up multiple post types
	$.each(post_types, function(k, v) {
		wp_api_query_str += '&type[]=' + post_types[k]
	});

	var wp_api_url = wp_api_search_vars.site_url + '/wp-json/posts';

	wp_api_lookup();


	$('#wp-api-search-more').on('click', $(this), wp_api_lookup);


	function wp_api_lookup() {
		var query_page = '&page=' + page;

		$.ajax({
			url: wp_api_url + wp_api_query_str + query_page,
			method: 'GET',
			
		}).done(function(data) {
			console.log(data);
			var output = '<div id="wp-api-search-page'+page+'">';

			$.each(data, function(k, v) {

				output += '<article>';
				
				// featured image thumbnail
				if(data[k].featured_image !== null) {
					output += "<img src='" + data[k].featured_image.attachment_meta.sizes.thumbnail.url + "' style='float: left; margin-right: 20px;'>";
				}
				
				// title
				output += '<h2 style="clear: none;">' + 
										"<a href='" + data[k].link + "'>" +
										highlightTerm(data[k].title, urlParams.s) + 
										'</a>' + 
									'</h2>';
				
				// excerpt
				output +=  highlightTerm(data[k].excerpt, urlParams.s);
				
				output += '</article>';

			});

			output += '</div>';

			// Full URL for history api
			var historyURL = wp_api_search_vars.site_url + '?s=' + urlParams.s + '&page=' + page;
			// Add an item to the history log
		  history.pushState(null, null, historyURL);

			$('#wp-api-search-results').append(output);
			page++;

			if(typeof loadingPage !== 'undefined') {
				if(loadingPage < currentPage) { 
					loadingPage++;
					wp_api_lookup();
				} else {
					// scroll to the current page.
					$("html, body").animate({ scrollTop: $('#wp-api-search-page'+(page-1)).offset().top - 50 }, 1000);
				}
			}
		});

	}

})( jQuery );


/* 
 * Highlight search term
 * 
 * @parameters content string and search term(s)
 * @returns string with search term wrapped with mark element
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