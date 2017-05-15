{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        <div class="alerts"></div>
        <div id="toolbar">
            <button class="btn btn-success" data-toggle="modal"
                    data-target=".modal-add"><i
                    class="glyphicon glyphicon-link"></i> Add Server
            </button>
            <button class="btn btn-danger check" data-toggle="modal" data-target=".modal-delete" disabled><i
                    class="glyphicon glyphicon-remove"></i> Delete
            </button>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i
                        class="glyphicon glyphicon-link"></i> Servers</h3>
            </div>
            <div class="panel-body">
                <table id="table"
                       data-toolbar="#toolbar"
                       data-toggle="table"
                       data-side-pagination="server"
                       data-pagination="true"
                       data-page-size="50"
                       data-page-list="[50, 100, 200, 500, 1000, 5000]"
                       data-sort-name="id"
                       data-show-refresh="true"
                       data-search="true"
                       data-sort-order="asc"
                       data-click-to-select="true"
                       data-url="/api/servers">
                    <thead>
                    <tr>
                        <th data-field="state" data-checkbox="true"></th>
                        <th data-field="id" data-sortable="true">ID</th>
                        <th data-field="ip">IP</th>
                        <th data-field="password">Password</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- modal addProxy -->
<div class="modal fade modal-add" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Add Server</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="ip" class="control-label">IP </label>
                            <input type="text" class="form-control" id="ip-form" name="ip"
                                   placeholder="ip" value="">
                        </div>
                        <div class="form-group">
                            <label for="password" class="control-label">Password </label>
                            <input type="text" class="form-control" id="password-form" name="password"
                                   placeholder="password"
                                   value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addServer();">Add Server</button>
            </div>
        </div>
    </div>
</div>
<!-- end moadal add-proxy -->
<!-- modal delete -->
<div class="modal fade modal-delete" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Delete server</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Do you really want to delete server</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick=deleteServer();
                ">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- end moadal delete -->
<script>
    var $table = $('#table'),
        $remove = $('.check'),
        selections = [];
    $(function () {

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

    function addServer() {
        var ip = $('#ip-form').val();
        var password = $('#password-form').val();
        $.ajax({
            type: "post",
            url: "/api/servers",
            data: {ip: ip, password: password},
            success: function (data) {
                if (data.status == '200') {
                    $('.alerts').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Server add </strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Error add server </strong>');
                }
                $('.modal-add').modal('hide');
                $('#table').bootstrapTable('refresh');
                $('#ip-form').val('');
                $('#password-form').val('');
            }
        });
    }

    function deleteServer() {
        var ids = getIdSelections();
        $.ajax({
            type: "delete",
            url: "/api/servers",
            data: {id: ids},
            success: function (data) {
                if (data.status == '200') {
                    $('.alerts').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Server delete </strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Error server proxy </strong>');
                }
                $('.modal-delete').modal('hide');
                $('#table').bootstrapTable('refresh');
            }
        })
    }
</script>
{% include "global/footer.tpl.php" %}