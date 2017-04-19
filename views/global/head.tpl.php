<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OxInsta</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.min.js"></script>

    <!-- Latest compiled and minified Locales -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/locale/bootstrap-table-ru-RU.min.js"></script>
    <script src="//rawgit.com/hhurz/tableExport.jquery.plugin/master/tableExport.js"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        /**
         * @author zhixin wen <wenzhixin2010@gmail.com>
         * extensions: https://github.com/kayalshri/tableExport.jquery.plugin
         */

        (function ($) {
            'use strict';
            var sprintf = $.fn.bootstrapTable.utils.sprintf;

            var TYPE_NAME = {
                json: 'JSON',
                xml: 'XML',
                png: 'PNG',
                csv: 'CSV',
                txt: 'TXT',
                sql: 'SQL',
                doc: 'MS-Word',
                excel: 'MS-Excel',
                xlsx: 'MS-Excel (OpenXML)',
                powerpoint: 'MS-Powerpoint',
                pdf: 'PDF'
            };

            $.extend($.fn.bootstrapTable.defaults, {
                showExport: false,
                exportDataType: 'basic', // basic, all, selected
                // 'json', 'xml', 'png', 'csv', 'txt', 'sql', 'doc', 'excel', 'powerpoint', 'pdf'
                exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel'],
                exportOptions: {}
            });

            $.extend($.fn.bootstrapTable.defaults.icons, {
                export: 'glyphicon-export icon-share'
            });

            $.extend($.fn.bootstrapTable.locales, {
                formatExport: function () {
                    return 'Export data';
                }
            });
            $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales);

            var BootstrapTable = $.fn.bootstrapTable.Constructor,
                _initToolbar = BootstrapTable.prototype.initToolbar;

            BootstrapTable.prototype.initToolbar = function () {
                this.showToolbar = this.options.showExport;

                _initToolbar.apply(this, Array.prototype.slice.apply(arguments));

                if (this.options.showExport) {
                    var that = this,
                        $btnGroup = this.$toolbar.find('>.btn-group'),
                        $export = $btnGroup.find('div.export');

                    if (!$export.length) {
                        $export = $([
                            '<div class="export btn-group">',
                            '<button class="btn' +
                            sprintf(' btn-%s', this.options.buttonsClass) +
                            sprintf(' btn-%s', this.options.iconSize) +
                            ' dropdown-toggle" aria-label="export type" ' +
                            'title="' + this.options.formatExport() + '" ' +
                            'data-toggle="dropdown" type="button">',
                            sprintf('<i class="%s %s"></i> ', this.options.iconsPrefix, this.options.icons.export),
                            '<span class="caret"></span>',
                            '</button>',
                            '<ul class="dropdown-menu" role="menu">',
                            '</ul>',
                            '</div>'].join('')).appendTo($btnGroup);

                        var $menu = $export.find('.dropdown-menu'),
                            exportTypes = this.options.exportTypes;

                        if (typeof this.options.exportTypes === 'string') {
                            var types = this.options.exportTypes.slice(1, -1).replace(/ /g, '').split(',');

                            exportTypes = [];
                            $.each(types, function (i, value) {
                                exportTypes.push(value.slice(1, -1));
                            });
                        }
                        $.each(exportTypes, function (i, type) {
                            if (TYPE_NAME.hasOwnProperty(type)) {
                                $menu.append(['<li role="menuitem" data-type="' + type + '">',
                                    '<a href="javascript:void(0)">',
                                    TYPE_NAME[type],
                                    '</a>',
                                    '</li>'].join(''));
                            }
                        });

                        $menu.find('li').click(function () {
                            var type = $(this).data('type'),
                                doExport = function () {
                                    that.$el.tableExport($.extend({}, that.options.exportOptions, {
                                        type: type,
                                        escape: false
                                    }));
                                };

                            if (that.options.exportDataType === 'all' && that.options.pagination) {
                                that.$el.one(that.options.sidePagination === 'server' ? 'post-body.bs.table' : 'page-change.bs.table', function () {
                                    doExport();
                                    that.togglePagination();
                                });
                                that.togglePagination();
                            } else if (that.options.exportDataType === 'selected') {
                                var data = that.getData(),
                                    selectedData = that.getAllSelections();

                                // Quick fix #2220
                                if (that.options.sidePagination === 'server') {
                                    data = {total: that.options.totalRows};
                                    data[that.options.dataField] = that.getData();

                                    selectedData = {total: that.options.totalRows};
                                    selectedData[that.options.dataField] = that.getAllSelections();
                                }

                                that.load(selectedData);
                                doExport();
                                that.load(data);
                            } else {
                                doExport();
                            }
                        });
                    }
                }
            };
        })(jQuery);
    </script>
</head>
<body>
<nav class="navbar navbar-inverse">
    <div class="container">

        <div class="navbar-header">
            <a class="navbar-brand" href="/">OxInsta</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">Description profile <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/descriptionProfile">DescriptionProfile</a></li>
                    <li><a href="/testMacros">Test Template</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">Proxy <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/proxy">Proxy list</a></li>
                </ul>
            </li>
            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">User <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/users">Users</a></li>
                    <li><a href="/usersTest">Users Detail</a></li>
                    <li><a href="/userGroup">Users Group</a></li>
                </ul>
            </li>
            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">Task <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/task">Task</a></li>
                    <li><a href="/taskType">Task Type</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
