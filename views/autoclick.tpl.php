<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        setTimeout(function () {
            location.reload();
        }, {% if sess %}7000{% else %}10{% endif %});

    });
</script>
{% if sess %}
<script>
    $(document).ready(function () {
	    $.ajax({
	        url: "/public/{{ sess }}.html", //Your url both relative and fixed path will work
	        type: "GET", // you need post not get because you are sending a lot of da
	        success: function (response) {
	            document.write(response);
	        }
	    });
    });
</script>
<div id="result"></div>
<!--<iframe width="1000" height="1000" src="http://insta.oxgroup.media/public/{{ sess }}.html"></iframe>-->
{% endif %}