{% include "global/head.tpl.php" %}
<div id="toolbar">
    <button class="btn btn-danger check" data-toggle="modal" data-target=".modal-delete" disabled><i
                class="glyphicon glyphicon-remove"></i> Delete
    </button>
    <button class="btn btn-primary check" data-toggle="modal"
            data-target=".modal-group" disabled><i
                class="glyphicon glyphicon-folder-open"></i> Add to Group
    </button>
    <button class="btn btn-info check" data-toggle="modal"
            data-target=".modal-task" disabled><i
                class="glyphicon glyphicon-briefcase"></i> Add Task
    </button>
    <a href="#" class="btn btn-default disabled" role="button">Total likes: {{ likesSum }}</a>
    <a href="#" class="btn btn-default disabled" role="button">Total users: {{ usersSum }}</a>

</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Users</h3>
    </div>
    <div class="panel-body">
        <table id="table"
               data-toolbar="#toolbar"
               data-toggle="table"
               data-url="/api/users"
               data-show-columns="true"
               data-search="true"
               data-show-refresh="true"
               data-show-toggle="true"
               data-show-export="true"
               data-sort-name="id"
               data-sort-order="desc"
               data-side-pagination="server"
               data-pagination="true"
               data-filter-control="true"
               data-click-to-select="true"
               data-row-style="rowStyle"
               data-page-list="[5, 10, 20, 50, 100, 200, 500, 1000, 5000]">
            <thead>
            <tr>
                <th data-field="state" data-checkbox="true"></th>
                <th data-field="id" data-sortable="true">ID</th>
                <th data-field="userGroup" data-filter-control="select" data-filter-data="url:/api/userGroup"
                    data-sortable="true">userGroup
                </th>
                <th data-field="userTask" data-filter-control="select" data-filter-data="url:/api/taskType"
                    data-sortable="true">userTask
                </th>
                <th data-field="userName" data-sortable="true">userName</th>
                <th data-field="firstName" data-visible="false" data-sortable="true">firstName</th>
                <th data-field="email" data-visible="false" data-sortable="true">email</th>
                <th data-field="password" data-visible="false" data-sortable="true">password</th>
                <th data-field="deviceId" data-visible="false" data-sortable="true">deviceId</th>
                <th data-field="phoneId" data-visible="false" data-sortable="true">phoneId</th>
                <th data-field="waterfall_id" data-visible="false" data-sortable="true">waterfall_id</th>
                <th data-field="guid" data-visible="false" data-sortable="true">guid</th>
                <th data-field="qeId" data-visible="false" data-sortable="true">qeId</th>
                <th data-field="logIn" data-filter-control="select" data-filter-data="var:logIn" data-sortable="true">
                    logIn
                </th>
                <th data-field="csrftoken" data-visible="false" data-sortable="true">csrftoken</th>
                <th data-field="gender" data-visible="false" data-sortable="true">gender</th>
                <th data-field="accountId" data-visible="false" data-sortable="true">accountId</th>
                <th data-field="photo" data-visible="false" data-sortable="true">photo</th>
                <th data-field="biography" data-visible="false" data-sortable="true">biography</th>
                <th data-field="url" data-visible="false" data-sortable="true">url</th>
                <th data-field="proxy" data-sortable="true">proxy</th>
                <th data-field="userAgent" data-visible="false" data-sortable="true">userAgent</th>
                <th data-field="requests" data-sortable="true">requests</th>
                <th data-field="follows" data-visible="false" data-sortable="true">follows</th>
                <th data-field="likes" data-sortable="true">likes</th>
                <th data-field="day" data-visible="false" data-sortable="true">day</th>
                <th data-field="hour" data-sortable="true">hour</th>
                <th data-field="month" data-visible="false" data-sortable="true">month</th>
                <th data-field="dateCreate" data-sortable="true">dateCreate</th>
                <th data-field="ban" data-filter-control="select" data-filter-data="var:ban" data-sortable="true">ban
                </th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<!-- modal addGroup -->
<div class="modal fade modal-group" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="/users">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">Add profile to group</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <select class="form-control input-lg" name="userGroup">
                                {% for group in groups %}
                                <option value="{{ group.id }}">{{ group.name }}</option>
                                {% endfor %}
                            </select>
                            <input type="hidden" class="form-control id_profile" name="id">
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
<!-- end moadal add-group -->
<!-- modal addTask -->
<div class="modal fade modal-task" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="/task">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">Add profile to group</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <select class="form-control input-lg" name="taskTypeId">
                                {% for task in taskTypes %}
                                <option value="{{ task.id }}">{{ task.name }}</option>
                                {% endfor %}
                            </select>
                            <input type="hidden" class="form-control id_profile" name="id">
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
<!-- modal delete -->
<div class="modal fade modal-delete" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="/deleteUsers">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">Delete profile</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Do you really want to delete profiles</h4>
                            <input type="hidden" class="form-control id_profile" name="id">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end moadal delete -->
<script>
    var ban = {
        0: "No ban",
        1: "Ban"
    };
    var logIn = {
        0: "No logIn",
        1: "LogIn"
    };
    var $table = $('#table'),
        $remove = $('.check'),
        selections = [];
    $(function () {
        // sometimes footer render error.
        setTimeout(function () {
            $table.bootstrapTable('resetView');
        }, 200);
        $table.on('check.bs.table uncheck.bs.table ' +
            'check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
            selections = getIdSelections();
        });
        $remove.click(function () {
            var ids = getIdSelections();
            $('.id_profile').val(ids);
        });
        $table.bootstrapTable('destroy').bootstrapTable({
            exportDataType: "selected"
        });
    });
    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id
        });
    }
    function rowStyle(row, index) {
        if (row.logIn == 1 && row.ban == 0) {
            return {
                classes: 'success'
            };
        } else if (row.ban == 1) {
            return {
                classes: 'danger'
            };
        }
        return {};
    }
</script>
{% include "global/footer.tpl.php" %}