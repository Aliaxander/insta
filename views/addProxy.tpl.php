{% include "global/head.tpl.php" %}
<div class="container">
    <div class="rows">
        <form method="post">
            <div class="form-group">
                <label for="inputEmail3" class="control-label">Proxy List</label>
	            <input type="text" name="ip" placeholder="127.155.254.222" value="">
	            <input type="text" name="portIn" placeholder="port from" value="">
	            <input type="text" name="portOut" placeholder="port to" value="">
	            <input type="text" name="authData" placeholder="login:pass" value="">
            </div>
            <div class="form-group">
                <div>
                    <button type="submit" class="btn btn-success">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>
{% include "global/footer.tpl.php" %}