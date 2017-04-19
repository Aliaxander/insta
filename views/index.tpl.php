{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        {% for key, type in data %}
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h1 class="panel-title">{{ key }}</h1>
                </div>
                <div class="panel-body">
                    <h2>{{ type }}</h2>
                </div>
            </div>
        </div>
        {% endfor %}
    </div>
</div>
{% include "global/footer.tpl.php" %}