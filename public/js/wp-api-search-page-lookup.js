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

	// log full search into custom post type
	var full_search = urlParams.full_search;

	// search term
	var wp_api_query_str = '?filter[s]=' + urlParams.s + '&filter[orderby]=menu_order%20date%20page';
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

	$('#wp-api-search-results').find('h1 > span').text(urlParams.s);
	wp_api_lookup();

	// Click handler for the more button
	$('#wp-api-search-more').on('click', $(this), wp_api_lookup);



	function wp_api_lookup() {
		if(typeof loadingPage !== 'undefined') {
			page = loadingPage;
		}
		var query_page = '&page=' + page;

		$.ajax({
			url: wp_api_url + wp_api_query_str + query_page,
			method: 'GET',
			
		}).done(function(data) {
			// HIDE MORE button if results length is not == posts_per_page
			if(data.length !== parseInt(wp_api_search_vars.posts_per_page)) {
				$('#wp-api-search-more').hide();
			}

			if(data.length == 0) {
				if(page == 1) {	
					// Return suggested posts.
					$.ajax({
						type: 'POST',
						url: wp_api_search_vars.ajaxurl,
						data: {action: 'return_suggested_posts'},
						dataType: 'json'
					}).always(function(data) {
							$('#wp-api-search-results').append("<p>Sorry, we couldn't find anything matching \"" + urlParams.s + '"');
					    $('#wp-api-search-results').append(data.data);
					});
					return false;
				}
			} else { // Results!
				if(page == 1) { 
					// saving search term only on first page.
					$.ajax({
						type: 'POST',
						url: wp_api_search_vars.ajaxurl,
						data: {action: 'save_search_term', results: true, term: urlParams.s, fullTerm: full_search},
						dataType: 'json'
					});
				}
				
				var output = (typeof loadingPage === undefined) 
												? '<div id="wp-api-search-page'+loadingPage+'">'
												: '<div id="wp-api-search-page'+page+'">';
				
				$.each(data, function(k, v) {

					output += '<article>';
					
					// featured image thumbnail
					if(data[k].featured_image !== null && data[k].featured_image.hasOwnProperty('attachment_meta')) {
						var img;
						if(data[k].featured_image.attachment_meta.hasOwnProperty('sizes')) { 
							if(data[k].featured_image.attachment_meta.sizes.hasOwnProperty('thumbnail')) {
								img = data[k].featured_image.attachment_meta.sizes.thumbnail.url;
							}
						}
						else {
							if(data[k].featured_image.attachment_meta.file) {
								img = data[k].featured_image.guid;
							}
						}
						if(img) {
							output += "<img src='" + img + "' style='float: left; margin-right: 20px;' alt='" + data[k].featured_image.title + "'>";
						}
					}

					// div wrapper for easy styling
					output += '<div>';
					
					// title
					output += '<h2 style="clear: none;">' + 
											"<a href='" + data[k].link + "'>" +
											highlightTerm(data[k].title, urlParams.s) + 
											'</a>' + 
										'</h2>';
					
					// excerpt
					if(data[k].excerpt !== null) {
						output +=  highlightTerm(data[k].excerpt, urlParams.s);
					}

					output += '</div>';
					output += '</article>';

				});

				output += '</div>';

				// Append results
				$('#wp-api-search-results').append(output);
			}

			// If loading from history
			if(typeof loadingPage !== 'undefined') {
				if(loadingPage < currentPage) { 
					loadingPage++;
					wp_api_lookup(); // call the function again to provide results until currentPage (can't alter posts_per_page without being authenticated.)

				} else {
					// scroll to the current page.
					$("html, body").animate({ scrollTop: $('#wp-api-search-page' + loadingPage).offset().top - 50 }, 100);
					loadingPage = undefined;
					page++;
				}
			} else {
					if(data.length > 0) {

						// Full URL for history api
						var historyURL = wp_api_search_vars.site_url + '/?s=' + urlParams.s + '&page=' + page;
						// Add an item to the history log
					  history.pushState(null, null, historyURL);
					}
				page++;
			}

		});

	}

})( jQuery );


/* 
 * Highlight search term
 * 
 * @param string content and string search term(s)
 * @returns string with search term wrapped with mark element
 *
*/
function highlightTerm(content, term) {
	var find = new RegExp(term, 'gi'); //gi = global, ignore case
	content = content.replace(find, function(match){ 
		return '<mark>' + match + '</mark>';
	});

	term_arr = term.split(' ');

	term_arr.forEach(function(v, k){
		// not preceded with mark element
		var find = new RegExp(v, 'gi'); //gi = global, ignore case
	 
	 	content = content.replace(find, function(match) {
	 		 
	 			return '<mark>' + match + '</mark>';
	 		}
	 	); 
	});

	return content;
}