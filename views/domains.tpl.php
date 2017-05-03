{% include "global/head.tpl.php" %}
<!-- modal addTask -->
<div class="container">
    <div class="row alerts">
    </div>
</div>
<div class="modal fade modal-add" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Add Domains</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="file" class="control-label">Domains</label>
                        <textarea class="form-control" rows="3" id="domains-form" name="domains"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary add-params" onclick="addDomains();">Add Domains</button>
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
                        class="glyphicon glyphicon-link"></i> Add Domains
            </button>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i
                            class="glyphicon glyphicon-link"></i> Domains</h3>
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
                       data-sort-order="desc"
                       data-url="/api/domains">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">ID</th>
                        <th data-field="domain" data-sortable="true">Domain</th>
                        <th data-field="dateCreate" data-sortable="true">dateCreate</th>
                        <th data-field="status" data-editable="true" data-sortable="true">Status</th>
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
            url: "/api/domains",
            data: row
        }).done(function (msg) {
            console.log(msg);
        });
    });

    function addDomains() {
        var domains = $('#domains-form').val();
        $.ajax({
            type: "post",
            url: "/api/domains",
            data: {domains: domains},
            success: function (data) {
                if (data.status == 'success') {
                    $('.alerts').html('<div class="alert alert-' + data.status + ' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong>' + data.name + '</strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong>' + data.name + '</strong>');
                }
                $('.modal-add').modal('hide');
                $('#table').bootstrapTable('refresh');
                $('#name-form').val('');
                $('#value-form').val('');
            }
        });
    }
</script>

{% include "global/footer.tpl.php" %}