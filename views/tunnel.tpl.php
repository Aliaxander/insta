{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        <div class="alerts"></div>
        <div id="toolbar">
            <button class="btn btn-danger check" data-toggle="modal" data-target=".modal-delete" disabled><i
                        class="glyphicon glyphicon-remove"></i> Delete
            </button>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i
                            class="glyphicon glyphicon-random"></i> Tunnels</h3>
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
                       data-url="/api/tunnels">
                    <thead>
                    <tr>
                        <th data-field="state" data-checkbox="true"></th>
                        <th data-field="id" data-sortable="true">ID</th>
                        <th data-field="tunnelId">tunnelId</th>
                        <th data-field="serverIp">serverIp</th>
                        <th data-field="remoteIp">remoteIp</th>
                        <th data-field="v6route">v6route</th>
                        <th data-field="48sub">48sub</th>
                        <th data-field="tunnelAccountId">tunnelAccountId</th>
                        <th data-field="status" data-editable="true" data-editable-type="select"
                            data-editable-source="[{value:'0',text:'Delete'},{value:'1',text:'Wait Create'},{value:'2',text:'Wait settings server'},{value:'3',text:'Settings server'},{value:'4',text:'Work'}]">status
                        </th>
                        <th data-field="status" data-editable="true" data-editable-type="select"
                            data-editable-source="var:statUss">status
                        </th>
                    </tr>
                    </thead>
                </table>
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
                <h4 class="modal-title" id="gridSystemModalLabel">Delete tunnels</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Do you really want to delete tunnels</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick=deleteTunnels();
                ">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- end moadal delete -->
<script>
    var statUss = [
        {value: 1, text: 'Male'},
        {value: 2, text: 'Female'}
    ];

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

        $('#table').on('editable-save.bs.table', function (e, field, row, oldValue, $el) {
            //console.log(row);
            $.ajax({
                type: "PUT",
                url: "/api/tunnels",
                data: row
            }).done(function (msg) {
                console.log(msg);
            });
        });

    });

    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id
        });
    }


    function deleteTunnels() {
        var ids = getIdSelections();
        $.ajax({
            type: "delete",
            url: "/api/tunnels",
            data: {id: ids},
            success: function (data) {
                if (data.status == '200') {
                    $('.alerts').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Tunnels delete </strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Error tunnels proxy </strong>');
                }
                $('.modal-delete').modal('hide');
                $('#table').bootstrapTable('refresh');
            }
        })
    }
</script>
{% include "global/footer.tpl.php" %}