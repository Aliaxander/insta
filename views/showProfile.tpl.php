{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        {% for profile in profiles %}
        <div class="col-sm-12 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <p>{{ profile.description }}</p>
                    <p><a href="{{ profile.url }}">{{ profile.url }}</a></p>
                    <p><a href="/deleteProfile/{{ profile.id }}" class="btn btn-danger" role="button">Delete</a></p>
                </div>
            </div>
        </div>
        {% endfor %}
    </div>
</div>
{% include "global/footer.tpl.php" %}