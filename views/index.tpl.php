{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h1>All profile</h1>
                    <h1>{{ allUsers }}</h1>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h1>Ban profile</h1>
                    <h1>{{ banUsers }}</h1>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h1>Description profile</h1>
                    <h1>{{ description }}</h1>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h1>Proxy</h1>
                    <h1>{{ proxy }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
{% include "global/footer.tpl.php" %}