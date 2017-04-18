{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        {% for key, alert in alerts %}
        <div class="alert alert-{{ key }} alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>{{ key }}</strong> {{ alert }}
        </div>
        {% endfor %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Task Type</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="list-group">
                            {% for taskType in taskTypes %}
                            <li class="list-group-item">{{ taskType.name }}</li>
                            {% endfor %}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success" data-toggle="modal"
                                data-target=".modal-taskType">Add Task Type
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal addTask -->
<div class="modal fade modal-taskType" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="/taskType">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">Add Task Type</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="name">
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
<!-- end moadal add-task -->
{% include "global/footer.tpl.php" %}