{% include "global/head.tpl.php" %}
<div class="container">
    <div class="rows">
        <form method="post">
            <div class="form-group">
                <label for="inputEmail3" class="control-label">Description</label>
                    <textarea name="biography" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="domains" class="control-label">domain</label>
                    <input type="text" class="form-control" name="domain" id="domain">
            </div>
            <div class="form-group">
                <label for="count" class="control-label">count</label>
                <input type="text" class="form-control" name="count" id="count">
            </div>
            <div class="form-group">
                <div>
                    <button type="submit" class="btn btn-success">Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>
{% include "global/footer.tpl.php" %}