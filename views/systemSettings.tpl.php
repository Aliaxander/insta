{% include "global/head.tpl.php" %}
<!-- modal addTask -->
<div class="modal fade modal-add" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Add params</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" class="form-control" id="name-form" name="name" required>
                        <label for="value" class="control-label">Value</label>
                        <input type="text" class="form-control" id="value-form" name="value" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary add-params" onclick="addParams();">Add Params</button>
            </div>
        </div>
    </div>
</div>
<!-- end moadal add-task -->
<div class="container">
    <div class="row">
        <div class="alerts"></div>
        <div id="toolbar">
            <button class="btn btn-success" data-toggle="modal"
                    data-target=".modal-add"><i
                        class="glyphicon glyphicon-wrench"></i> Add Params
            </button>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Settings</h3>
            </div>
            <div class="panel-body">
                <table id="table"
                       data-toolbar="#toolbar"
                       data-toggle="table"
                       data-url="/api/systemSettings">
                    <thead>
                    <tr>
                        <th data-field="id">ID</th>
                        <th data-field="name">Name</th>
                        <th data-field="value" data-editable="true">Value</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
        $('#table').on('editable-save.bs.table', function (e, field, row, oldValue, $el) {
            //console.log(row);
            $.ajax({
                type: "PUT",
                url: "/api/systemSettings",
                data: row
            }).done(function (msg) {
                console.log(msg);
            });
        });

    function addParams() {
        var name = $('#name-form').val();
        var value = $('#value-form').val();
        $.ajax({
            type: "post",
            url: "/api/systemSettings",
            data: {name: name, value: value}
        }).done(function (msg) {
            console.log(msg);
            $('.modal-add').modal('hide');
            $('#table').bootstrapTable('refresh');
            $('#name-form').val('');
            $('#value-form').val('');
        });
    };
</script>
{% include "global/footer.tpl.php" %}