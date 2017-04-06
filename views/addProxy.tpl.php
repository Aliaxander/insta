{% include "global/head.tpl.php" %}
<div class="container">
    <div class="rows">
        <form method="post">
            <div class="form-group">
                <label for="inputEmail3" class="control-label">Proxy List</label>
                <textarea name="proxy" class="form-control" rows="8"
                          placeholder="каждый ip с новой строки"></textarea>
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