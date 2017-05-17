{% include "global/head.tpl.php" %}
<!-- modal addTask -->
<div class="modal fade modal-add" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Add techAccount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" class="form-control" id="name-form" name="name" required>
                        <label for="value" class="control-label">Password</label>
                        <input type="text" class="form-control" id="password-form" name="value" required>
                        <label for="type" class="control-label">Type</label>
                        <input type="text" class="form-control" id="type-form" name="type" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary add-params" onclick="addTechAccount();">Add TechAccount
                </button>
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
                        class="glyphicon glyphicon-envelope"></i> Add TechAccount
            </button>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i
                            class="glyphicon glyphicon-envelope"></i> TechAccount</h3>
            </div>
            <div class="panel-body">
                <table id="table"
                       data-toolbar="#toolbar"
                       data-toggle="table"
                       data-show-refresh="true"
                       data-url="/api/techAccount">
                    <thead>
                    <tr>
                        <th data-field="id">ID</th>
                        <th data-field="name">Name</th>
                        <th data-field="password">Password</th>
                        <th data-field="type">type</th>
                        <th data-field="comment" data-editable="true">Comment</th>
                        <th data-field="count" data-editable="true">Count</th>
                        <th data-field="dateUpdate">Update</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('#table').on('editable-save.bs.table', function (e, field, row, oldValue, $el) {
        $.ajax({
            type: "PUT",
            url: "/api/techAccount",
            data: row,
            success: function (data) {
                if (data.status == '200') {
                    $('.alerts').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Update success </strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Update error </strong>');
                }
                console.log(data);
            }
        })
    });

        function addTechAccount() {
            var name = $('#name-form').val();
            var password = $('#password-form').val();
            var type = $('#type-form').val();
            $.ajax({
                type: "post",
                url: "/api/techAccount",
                data: {name: name, password: password, type: type}
            }).done(function (msg) {
                console.log(msg);
                $('.modal-add').modal('hide');
                $('#table').bootstrapTable('refresh');
                $('#name-form').val('');
                $('#password-form').val('');
                $('#type-form').val('');
            });
        }
</script>

{% include "global/footer.tpl.php" %}