<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
{% if sess %}
        {% autoescape false %}
            {{ data }}
        {% endautoescape %}
{% endif %}
<script>
    $(document).ready(function () {
        setTimeout(function () {
            location.reload();
        }, {% if sess %}7000{% else %}10{% endif %});

    });
</script>