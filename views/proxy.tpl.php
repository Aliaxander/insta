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
				<h3 class="panel-title">Proxy</h3>
			</div>
			<div class="panel-body">
				<button class="btn btn-success btn-sm" data-toggle="modal"
				        data-target=".modal-proxy">Add Proxy
				</button>
			</div>
			<table class="table">
				<thead>
				<tr>
					<th>#</th>
					<th>proxy</th>
					<th>status</th>
				</tr>
				</thead>
				<tbody>
				{% for proxy in proxyes %}
				{% if proxy.status == 0 %}
				<tr class="success">
					{% else %}
				<tr class="danger">
					{% endif %}
					<td>{{ proxy.id }}</td>
					<td>{{ proxy.proxy }}</td>
					<td>{{ proxy.status }}</td>
				</tr>
				{% endfor %}
				</tbody>
			</table>
			<div class="panel-footer">
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-success" data-toggle="modal"
						        data-target=".modal-proxy">Add Proxy
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- modal addProxy -->
<div class="modal fade modal-proxy" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<form method="post" action="/proxy">
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
								<textarea name="ip" class="form-control" placeholder="127.155.254.222"></textarea>
							</div>
							<div class="form-group">
								<label for="portIn" class="control-label">Port from </label>
								<input type="text" class="form-control" name="portIn" placeholder="port from" value="">
							</div>
							<div class="form-group">
								<label for="portOut" class="control-label">Port to </label>
								<input type="text" class="form-control" name="portOut" placeholder="port to" value="">
							</div>
							<div class="form-group">
								<label for="authData" class="control-label">Login:pass </label>
								<input type="text" class="form-control" name="authData" placeholder="login:pass"
								       value="">
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
<!-- end moadal add-proxy -->
{% include "global/footer.tpl.php" %}