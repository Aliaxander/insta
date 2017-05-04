{% include "global/head.tpl.php" %}
<div class="container">
    <div class="row">
        <div class="alerts"></div>
        <div id="toolbar">
            <button class="btn btn-success" data-toggle="modal"
                    data-target=".modal-add"><i
                        class="glyphicon glyphicon-link"></i> Add Proxy
            </button>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i
                            class="glyphicon glyphicon-link"></i> Proxy</h3>
            </div>
            <div class="panel-body">
                <table id="table"
                       data-toolbar="#toolbar"
                       data-toggle="table"
                       data-side-pagination="server"
                       data-pagination="true"
                       data-page-size="50"
                       data-height="500"
                       data-page-list="[50, 100, 200, 500, 1000, 5000]"
                       data-sort-name="id"
                       data-show-refresh="true"
                       data-search="true"
                       data-sort-order="desc"
                       data-url="/api/proxy">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">ID</th>
                        <th data-field="proxy" data-sortable="true">Proxy</th>
                        <th data-field="status" data-editable="true" data-sortable="true">Status</th>
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
					<h4 class="modal-title" id="gridSystemModalLabel">Add Proxy</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="ip" class="control-label">Proxy ip </label>
								<textarea name="ip" id="ip-form" class="form-control" placeholder="127.155.254.222"></textarea>
							</div>
							<div class="form-group">
								<label for="portIn" class="control-label">Port from </label>
								<input type="text" class="form-control" id="portIn-form" name="portIn" placeholder="port from" value="">
							</div>
							<div class="form-group">
								<label for="portOut" class="control-label">Port to </label>
								<input type="text" class="form-control" id="portOut-form" name="portOut" placeholder="port to" value="">
							</div>
							<div class="form-group">
								<label for="authData" class="control-label">Login:pass </label>
								<input type="text" class="form-control" id="authData-form" name="authData" placeholder="login:pass"
								       value="">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="addProxy();">Add Proxy</button>
				</div>
		</div>
	</div>
</div>
<!-- end moadal add-proxy -->
<script>
    $(function () {
        // sometimes footer render error.
        setTimeout(function () {
            $table.bootstrapTable('resetView', {height: getHeight()});
        }, 200);
    });

    $('#table').on('editable-save.bs.table', function (e, field, row, oldValue, $el) {
        //console.log(row);
        $.ajax({
            type: "PUT",
            url: "/api/proxy",
            data: row
        }).done(function (msg) {
            console.log(msg);
        });
    });

    function getHeight() {
        return $(window).height() - $('nav').outerHeight(false);
    }

    function addProxy() {
        var ip = $('#ip-form').val();
        var portIn = $('#portIn-form').val();
        var portOut = $('#portOut-form').val();
        var authData = $('#authData-form').val();
        $.ajax({
            type: "post",
            url: "/api/proxy",
            data: {ip: ip, portIn: portIn, portOut: portOut, authData: authData},
            success: function (data) {
                if (data.status == '200') {
                    $('.alerts').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Proxy add </strong>');
                } else {
                    $('.alerts').html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong> Error add Proxy </strong>');
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