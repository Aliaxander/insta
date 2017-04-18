{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        {% for key, alert in alerts %}
        <div class="alert alert-{{ key }} alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <strong>{{ key }}</strong> {{ alert }}
        </div>
        {% endfor %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Description profile</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    {% for profile in profiles %}
                    <div class="col-sm-12 col-md-4">
                        <div class="thumbnail">
                            <div class="caption">
                                <p>{{ profile.description }}</p>
                                <p><a href="{{ profile.url }}">{{ profile.url }}</a></p>
                                <p><a href="/deleteProfile/{{ profile.id }}" class="btn btn-danger"
                                      role="button">Delete</a></p>
                            </div>
                        </div>
                    </div>
                    {% endfor %}
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success" data-toggle="modal"
                                    data-target=".modal-description">Generate Description
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal generateDescription -->
<div class="modal fade modal-description" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="/descriptionProfile">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">Generate Description</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Description</label>
                                <textarea name="biography" class="form-control" rows="4" placeholder="{Добрый день|Доброе утро}! Какая {на улице|за {окном|бортом}} {прекрасная|очаровательная|великолепная} погода!"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="count" class="control-label">count</label>
                                <input type="text" class="form-control" name="count" id="count" placeholder="100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end moadal generate-description -->
{% include "global/footer.tpl.php" %}