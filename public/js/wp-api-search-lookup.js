(function($) {

	var google_search_api = {
		'endpoint': 'https://www.googleapis.com/customsearch/v1',
		'key': wp_api_search_vars.google_api_key,
		'engine_id': wp_api_search_vars.google_search_engine_id
	}
	
	// $('#wp_api_search_submit').on('click', function(e){
	// 	e.preventDefault();
	// 	var search_term = $('input[name="wp_api_search_widget"]').val();
		
	// 	var search_word = suggested_spelling(search_term);

	// });



	// typeahead bloodhound 
	var postsLookup = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
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
		if ( 13 == e.which ) {
			$(this).parents( 'form' ).submit();
			return false;
		}
	}).on('typeahead:empty', function () { // typeahead:empty is a custom method. (not in typeahead.js)

		var word = $(this).val();
		$("#wp_api_search_spelling_suggestion").eq(0).val(word).trigger("input");
	 
	});



	if(google_search_api.key && google_search_api.engine_id) {
		var google_cse_api_url = google_search_api.endpoint + '?key' + google_search_api.key + '&cx=' + google_search_api.engine_id + '&fields=spelling%2FcorrectedQuery' + '&q=%QUERY';

		var spellingSuggestion = function(data) {
			console.log(data);
			var arr = [];
			for(var key in data) {
				var obj = data[key];
				if(key == 'spelling') {
					arr.push({value: obj.correctedQuery});
				} 
				// handle error (over quota for the day)
			}
			return arr;
	  };

		var results = new Bloodhound({
	    datumTokenizer: function(data) {
	      return Bloodhound.tokenizers.whitespace(data.value)
	    },
	    queryTokenizer: Bloodhound.tokenizers.obj.whitespace,
	    minLength: 5,
	    remote: {
	      url: google_cse_api_url,
	      ajax: $.ajax({type:'GET',dataType:'jsonp' }),
	      filter: spellingSuggestion
	    }
	  });

		results.initialize()

	  $('#wp_api_search_spelling_suggestion').typeahead(null, {
	    name: 'results',
	    displayKey: 'value',
	    source: results.ttAdapter()
	  }).on('typeahead:selected', function(){
	  	console.log(obj);
      console.log(datum);
      console.log(name);
	  });
	} // end of if google api key and engine id are set




})( jQuery );