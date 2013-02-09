<html>
<head>
    <title>Email Word Cloud</title>
    <link rel="stylesheet" type="text/css" href="/css/jqcloud.css" />
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
    <script type="text/javascript" src="/js/jqcloud-1.0.2.js"></script>
    <script type="text/javascript">
      /*!
       * Create an array of word objects, each representing a word in the cloud
       */
  	function getWords(callback) {
  		$.ajax({
  			type: "GET",
  			dataType: "json",
  			url : "/getwords",
  			success : function(data){
  				callback(data);
  			}
  		});
    }
  	var word_array = [
	    {text: "Lorem", weight: 15},
	    {text: "Ipsum", weight: 9, link: "http://jquery.com/"},
	    {text: "Dolor", weight: 6, html: {title: "I can haz any html attribute"}},
	    {text: "Sit", weight: 7},
	    {text: "Amet", weight: 5}
	      // ...as many words as you want
  	];

	$(function() {
    // When DOM is ready, select the container element and call the jQCloud method, passing the array of words as the first argument.
	    getWords(function(data){
	    	console.log("inside getwords callback");
		    $("#example").jQCloud(data);    	
	    });
    // $("#example").jQCloud(word_array);    	
  });
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span12 well">
				<h1>Email Word Cloud</h1>
			</div>
		</div>
		<div class="row">
			<div class="span12">
			    <div id="example" style="width: 700px; height: 350px;"></div>
			</div>
		</div>
		<div class="row">
			<div class="span12 well">
				
			</div>
		</div>
	</div>
</body>
</html>