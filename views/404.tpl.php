{% include 'global/head.tpl.php' %}


<div class="container">

        <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-2">
                <div class="font-green" style="font-size: 80px;"> 404</div>

            </div>
            <div class="col-md-8">
                <h3>Oops! You're lost.</h3>
                <p> We can not find the page you're looking for.
                    <br>
                    <a href="/"> Return home </a>. </p>
            </div>
        </div>
</div>




</div>
</div>
</div>
{% include 'global/footer.tpl.php' %}
{% include 'global/globalJS.tpl.php' %}


<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="/plugins/select2/select2_locale_ru.js"></script>
<script type="text/ecmascript" src="/plugins/jqgrid/js/i18n/grid.locale-ru.js"></script>
<script type="text/ecmascript" src="/plugins/jqgrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="/plugins/bootstrap-modal/js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="/plugins/bootstrap-modal/js/bootstrap-modal.js"></script>
<script src="/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
<script src="/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->

<link rel="stylesheet" type="text/css" media="screen" href="/plugins/jqgrid/css/ui.jqgrid-bootstrap.css"/>
<script>
    $.jgrid.defaults.responsive = true;
    $.jgrid.defaults.styleUI = 'Bootstrap';
</script>

<script src="/js/news.js"></script>

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/js/core.js" type="text/javascript"></script>
<script src="/js/layout.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    $(document).ready(function () {
        Ox.init();
        Layout.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>