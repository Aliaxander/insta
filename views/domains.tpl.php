{% include "global/head.tpl.php" %}
<!-- modal addTask -->
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
                <button type="button" class="btn btn-primary" onclick="addDomains();">Add Domains</button>
            </div>
        </div>
    </div>
</div>
<!-- modal delete -->
<div class="modal fade modal-delete" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Delete domains</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Do you really want to delete domains</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick=deleteDomains();
                ">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- end moadal delete -->
<!-- end moadal add-task -->
<div class="container">
    <div class="row">
        <div class="alerts"></div>
        <div id="toolbar">
            <button class="btn btn-success" data-toggle="modal"
                    data-target=".modal-add"><i
                        class="glyphicon glyphicon-link"></i> Add Domains
            </button>
            <button class="btn btn-danger check" data-toggle="modal" data-target=".modal-delete" disabled><i
                        class="glyphicon glyphicon-remove"></i> Delete
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
                       data-height="500"
                       data-page-list="[10, 50, 100, 200, 500, 1000, 5000]"
                       data-sort-name="id"
                       data-show-refresh="true"
                       data-search="true"
                       data-sort-order="desc"
                       data-filter-control="true"
                       data-click-to-select="true"
                       data-url="/api/domains">
                    <thead>
                    <tr>
                        <th data-field="state" data-checkbox="true"></th>
                        <th data-field="id" data-sortable="true">ID</th>
                        <th data-field="domain" data-sortable="true">Domain</th>
                        <th data-field="dateCreate" data-sortable="true">dateCreate</th>
                        <th data-field="status" data-filter-control="select" data-align="center" data-editable="true"
                            data-filter-data="var:statUs" data-sortable="true">Status
                        </th>
                        <th data-field="isAviable" data-sortable="true">isAviable</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    var statUs = {
        0: "0",
        1: "1"
    };

    var $table = $('#table'),
        $remove = $('.check'),
        selections = [];
    $(function () {
        // sometimes footer render error.
        setTimeout(function () {
            $('#table').bootstrapTable('resetView', {height: getHeight()});
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

        $('#table').on('editable-save.bs.table', function (e, field, row, oldValue, $el) {
            $.ajax({
                type: "PUT",
                url: "/api/domains",
                data: row
            }).done(function (msg) {
                console.log(msg);
            })
        });
    });

    function getHeight() {
        return $(window).height() - $('nav').outerHeight(false);
    }

    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id
        });
    }

    function addDomains() {
        var domains = $('#domains-form').val();
        $.ajax({
            type: "post",
            url: "/api/domains",
            data: {domains: domains},
            success: function (data) {
                if (data.status == '200') {
                    $('.alerts').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Domains add </strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Error add domains </strong>');
                }
                $('.modal-delete').modal('hide');
                $('#table').bootstrapTable('refresh');
                $('#name-form').val('');
                $('#value-form').val('');
            }
        })
    }

    function deleteDomains() {
        var ids = getIdSelections();
        $.ajax({
            type: "delete",
            url: "/api/domains",
            data: {id: ids},
            success: function (data) {
                if (data.status == '200') {
                    $('.alerts').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Domains delete </strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Error delete domains </strong>');
                }
                $('.modal-add').modal('hide');
                $('#table').bootstrapTable('refresh');
            }
        })
    }
</script>

{% include "global/footer.tpl.php" %}