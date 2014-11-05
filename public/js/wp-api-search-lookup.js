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

	




	// typeahead bloodhound 
	var postsLookup = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
	  //prefetch: '../data/films/post_1960.json',
	  remote: {
	  	url: 'http://localhost:8080/wp-json/posts/?type[]=wp-api-search-term&filter[orderby]=menu_order&filter[s]=%QUERY',
	  }
	});
	 
	postsLookup.initialize();

	$('#wp_api_search_input').typeahead(
		{
			highlight: true,
	  	minLength: 3,
	  	limit: 5
		},
		{
		  name: 'wp-api-search',
		  displayKey: 'title',
		  source: postsLookup.ttAdapter(),
		  templates: {
		  	empty: function(){
		  		$('#wp_api_search_input').trigger('typeahead:empty');
		  	},
		  }
		}
	).keypress(function(e) {
		alert(e);
      if ( 13 == e.which ) {
      		alert('enter hit!');
          //$(this).parents( 'form' ).submit();
          return false;
      }
	}).on('typeahead:empty', function () { // typeahead:empty is a custom method. (not in typeahead.js)
		// TODO Pull input value
		var word = 'catrpillr';
		
		suggested_spelling(word).done(function(response) {
	      if(response.spelling) {
	      	console.log(response.spelling.correctedQuery);
	      	
	      } else {
	      	console.log(response);
	      }
    });

	});



	function suggested_spelling(word) {
		if(google_search_api.key && google_search_api.engine_id) {
			return $.ajax({
				url: 	google_search_api.endpoint,
				data: {
	        	 key: google_search_api.key, 
	        		 q: word, 
	        		cx: google_search_api.engine_id,
	     		fields: 'spelling/correctedQuery' // PULLING ONLY WHAT WE NEED https://developers.google.com/custom-search/json-api/v1/performance
				},
	      dataType: 'jsonp'
			});
		} else {
			throw new Error('Google API Key or Engine ID not set');
		}
	}



})( jQuery );