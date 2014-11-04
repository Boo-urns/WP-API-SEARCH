(function($) {

	var google_search_api = {
		'endpoint': 'https://www.googleapis.com/customsearch/v1',
		'key': wp_api_search_vars.google_api_key,
		'engine_id': wp_api_search_vars.google_search_engine_id
	}
	
	$('#wp_api_search_submit').on('click', function(e){
		e.preventDefault();
		var search_term = $('input[name="wp_api_search_widget"]').val();
		
		var search_word = suggested_spelling(search_term);

	});

	var postsLookup = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
	  //prefetch: '../data/films/post_1960.json',
	  remote: 'http://localhost:8080/wp-json/posts/?filter[s]='
	});
	 
	postsLookup.initialize();
 
	$('#wp_api_search').typeahead(
		{
			highlight: true,
	  	minLength: 3
		},
		{
		  name: 'wp-api-search',
		  displayKey: 'title',
		  source: postsLookup.ttAdapter(),
		  // templates: {
	   //  empty: [
	   //    '<div class="empty-message">',
	   //    'unable to find any results',
	   //    '</div>'
	   //  ].join('\n'),
	   //  suggestion: Handlebars.compile('<p><strong>{{title}}</strong> – {{id}}</p>')
	  	// }
		}
	);

	function suggested_spelling(word) {
		if(google_search_api.key && google_search_api.engine_id) {
			$.ajax({
				url: 	google_search_api.endpoint,
				data: {
	        	 key: google_search_api.key, 
	        		 q: word, 
	        		cx: google_search_api.engine_id,
	     		fields: 'spelling/correctedQuery' // PULLING ONLY WHAT WE NEED https://developers.google.com/custom-search/json-api/v1/performance
				},
	      dataType: 'jsonp'
			}).done(function(response) {
	      if(response.spelling) {
	        console.log(response.spelling.correctedQuery);
	        //$('body').append('<h2>Did you mean <u>' + response.spelling.correctedQuery + '</u> ?</h2>');
	      }
	  	});
		}
	}




})( jQuery );