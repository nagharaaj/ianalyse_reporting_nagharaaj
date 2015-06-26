    <script type="text/javascript">
         $(document).ready(function () {

             var theme = 'base';
             var classWon = 'stage-won';
             var classLost = 'stage-lost';
             var classLive = 'stage-live';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }
             var currencies = jQuery.parseJSON('<?php echo $currencies; ?>');
             var currYear = '<?php echo $current_year; ?>';
             var userRole = '<?php echo $userRole;?>';
             var arrCurrencies = new Array();

             var calculateStats = function () {
                var dataRows = $('#jqxgrid').jqxGrid('getrows');
                var rowscount = dataRows.length;
                $('#no_of_records span').text(rowscount);
                var clientsCount = 0;
                var pitchesCount = 0;
                var wonCount = 0;
                var lostCount = 0;
                var cancelledCount = 0;
                var lostCurrentYear = 0;
                var sumEstRevenue = 0;
                var sumActRevenue = 0;
                var statsCurrency = $("#statscurrency option:selected").text();
                var convert_ratio = $("#statscurrency").val();
                for(var i = 0; i < rowscount; i++) {
                        if(dataRows[i].PitchStage.match(/Won/g) || dataRows[i].PitchStage == 'Current client') {
                                if(dataRows[i].ParentId == 0 || dataRows[i].ParentId == null || dataRows[i].ParentId == '') {
                                        clientsCount++;
                                }
                        }
                        if(dataRows[i].PitchStage.match(/Live/g)) {
                                pitchesCount++;
                        }
                        if(dataRows[i].PitchStage.match(/Won/g)) {
                                wonCount++;
                        }
                        if(dataRows[i].PitchStage.match(/Lost/g)) {
                                lostCount++;
                                if(dataRows[i].PitchStage != 'Lost - archive') {
                                        lostCurrentYear++;
                                }
                        }
                        if(dataRows[i].PitchStage == 'Cancelled') {
                                cancelledCount++;
                        }
                        if(!isNaN(parseFloat(dataRows[i].EstimatedRevenue))) {
                                if(dataRows[i].Currency == statsCurrency) {
                                        sumEstRevenue = sumEstRevenue + parseFloat(dataRows[i].EstimatedRevenue);
                                } else {
                                        $.each(currencies,function(value,symbol){
                                                if(symbol == dataRows[i].Currency) {
                                                        DollarConvertRatio = value;
                                                }
                                        });
                                        if(statsCurrency == "USD") {
                                             sumEstRevenue = sumEstRevenue + parseFloat(dataRows[i].EstimatedRevenue * DollarConvertRatio);
                                        } else {
                                             dollarEstRevenue = parseFloat(dataRows[i].EstimatedRevenue * DollarConvertRatio);
                                             sumEstRevenue = sumEstRevenue + parseFloat(dollarEstRevenue / convert_ratio);
                                        }
                                }
                        }
                        if(!isNaN(parseFloat(dataRows[i].ActualRevenue))) {
                                if(dataRows[i].Currency == statsCurrency) {
                                        sumActRevenue = sumActRevenue + parseFloat(dataRows[i].ActualRevenue);
                                } else {
                                        $.each(currencies,function(value,symbol){
                                                if(symbol == dataRows[i].Currency) {
                                                        DollarConvertRatio = value;
                                                }
                                        });
                                        if(statsCurrency == "USD") {
                                             sumActRevenue = sumActRevenue + parseFloat(dataRows[i].ActualRevenue * DollarConvertRatio);
                                        } else {
                                             dollarEstRevenue = parseFloat(dataRows[i].ActualRevenue * DollarConvertRatio);
                                             sumActRevenue = sumActRevenue + parseFloat(dollarEstRevenue / convert_ratio);
                                        }
                                }
                        }
                }
                $('#no_of_clients span').text(clientsCount);
                $('#no_of_pitches span').text(pitchesCount);
                $('#no_of_lost span').text(lostCount);
                $('#no_of_cancelled span').text(cancelledCount);
                if(wonCount > 0 || lostCurrentYear > 0) {
                        var conversion_rate = (wonCount/(wonCount+lostCurrentYear))*100;
                } else {
                        var conversion_rate = 0;
                }
                $('#conversion_rate span').text(Math.round(conversion_rate) + '%');
                $('#sum_est_revenue span').text(Math.round(sumEstRevenue/1000) + 'K');
                $('#sum_act_revenue span').text(Math.round(sumActRevenue/1000) + 'K');
             }

             var source =
             {
                dataType: "json",
                id: 'id',
                url: "/reports/get_client_report_data/",
                datafields: [
                    { name: 'RecordId', type: 'number' },
                    { name: 'Region', type: 'string' },
                    { name: 'Country', type: 'string' },
                    { name: 'City', type: 'string' },
                    { name: 'LeadAgency', type: 'string' },
                    { name: 'ClientName', type: 'string' },
                    { name: 'ParentCompany', type: 'string' },
                    { name: 'ClientCategory', type: 'string' },
                    { name: 'PitchStart', type: 'date' },
                    /*{ name: 'PitchLeader', type: 'string' },*/
                    { name: 'PitchStage', type: 'string' },
                    { name: 'ClientSince', type: 'date' },
                    { name: 'Lost', type: 'date' },
                    { name: 'Service', type: 'string' },
                    { name: 'Division', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'number' },
                    { name: 'ActualRevenue', type: 'number' },
                    { name: 'Comments', type: 'string' },
                    { name: 'Year', type: 'number' },
                    { name: 'ParentId', type: 'number' },
                    { name: 'Created', type: 'date' },
                    { name: 'Modified', type: 'date' }
                ],
                addRow: function (rowID, rowData, position, commit) {
                    // synchronize with the server - send insert command
                    // call commit with parameter true if the synchronization with the server is successful 
                    // and with parameter false if the synchronization failed.
                    // you can pass additional argument to the commit callback which represents the new ID if it is generated from a DB.
                    commit(true);
                },
                updateRow: function (rowID, rowData, commit) {
                    // synchronize with the server - send update command
                    // call commit with parameter true if the synchronization with the server is successful 
                    // and with parameter false if the synchronization failed.
                    commit(true);
                },
                deleteRow: function (rowID, commit) {
                    // synchronize with the server - send delete command
                    // call commit with parameter true if the synchronization with the server is successful 
                    // and with parameter false if the synchronization failed.
                    commit(true);
                }
             };

             var dataAdapter = new $.jqx.dataAdapter(source);
             var cellclass = function (row, datafield, value, rowdata) {
                //console.log(rowdata);
                var stage = rowdata.PitchStage;
                if(stage.match(/Won/g) || stage == 'Current client') {
                        return classWon;
                } else if (stage.match(/Lost/g)) {
                        return classLost;
                } else if (stage.match(/Live/g)) {
                        return classLive;
                } else {
                        return '';
                }
            }

             // initialize jqxGrid
             $("#jqxgrid").jqxGrid(
             {
                width: (parseInt(screen.availWidth) - 30),
                autoheight: true,
                source: dataAdapter,
                enablemousewheel: false,
                pageable: true,
                pageSize: 20,
                pagerMode: 'simple',
                sortable: true,
                filterable: true,
                showfilterrow: true,
                editable: false,
                autoRowHeight: true,
                columnsresize: true,
                showpinnedcolumnbackground: false,
                enablehover: false,
                columns: [
                  { text: 'RecordId', datafield: 'RecordId', hidden: true },
                  { text: 'ParentId', datafield: 'ParentId', hidden: true },
                  { text: 'Created', datafield: 'Created', hidden: true },
                  { text: 'Modified', datafield: 'Modified', hidden: true },
                  { text: 'Year', datafield: 'Year', hidden: true },
                  { text: 'Region', datafield: 'Region', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      }
                  },
                  { text: 'Country', datafield: 'Country', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'City', datafield: 'City', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250, cellClassName: cellclass, pinned: true },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250, cellClassName: cellclass },
                  { text: 'Client Category', datafield: 'ClientCategory', width: 200, cellClassName: cellclass, filtertype: 'checkedlist',
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 200 });
                      }
                  },
                  { text: 'Lead Agency', datafield: 'LeadAgency', width: 130, cellClassName: cellclass, filtertype: 'checkedlist',
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Status', columntype: 'template', datafield: 'PitchStage', width: 130, cellClassName: cellclass, filtertype: 'checkedlist',
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Service', datafield: 'Service', width: 150, cellClassName: cellclass, filtertype: 'checkedlist',
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Division', datafield: 'Division', width: 150, cellClassName: cellclass, filtertype: 'checkedlist',
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Client Since (M-Y)', datafield: 'ClientSince', width: 125, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy' },
                  { text: 'Lost Since (M-Y)', datafield: 'Lost', width: 100, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy' },
                  { text: 'Pitched (M-Y)', datafield: 'PitchStart', width: 100, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy' },
                  /*{ text: 'Pitch Leader', columngroup: 'PitchLeader', datafield: 'PitchLeader', width: 150, cellClassName: cellclass },*/
                  { text: 'Active Markets', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 200, cellClassName: cellclass, filtertype: 'checkedlist',
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 200 });
                      }
                  },
                  { text: 'Currency', datafield: 'Currency', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', hidden: ((userRole == 'Viewer') ? true : false),
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      }
                  },
                  { text: 'iP estimated revenue', columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 130, align: 'center', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', hidden: ((userRole == 'Viewer') ? true : false) },
                  { text: 'iP 2014 Actual revenue', columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 150, align: 'center', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', hidden: ((userRole == 'Viewer') ? true : false) },
                  { text: 'Comments', columngroup: 'Comments', datafield: 'Comments', width: 200, cellClassName: cellclass }
                ],
                ready: calculateStats
            });
            $("#jqxgrid").on("filter", function (event) {
                    calculateStats();
                    var paginginfo = $("#jqxgrid").jqxGrid('getpaginginformation');
                    if(paginginfo.pagescount <= 1) {
                        $('#pagerjqxgrid').hide();
                    } else {
                        $('#pagerjqxgrid').show();
                    }
            });
            $('#jqxgrid').jqxGrid({ rendered: function() {
                    var paginginfo = $("#jqxgrid").jqxGrid('getpaginginformation');
                    if(paginginfo.pagescount <= 1) {
                        $('#pagerjqxgrid').hide();
                    } else {
                        $('#pagerjqxgrid').show();
                    }
                }
            });
            $('#clearfilteringbutton').jqxButton({ theme: theme });
            $('#exporttoexcelbutton').jqxButton({ theme: theme });
            $('#exportButton').jqxButton({ theme: theme });
            $('#cancelExportButton').jqxButton({ theme: theme });
            // clear the filtering.
            $('#clearfilteringbutton').click(function () {
                $("#jqxgrid").jqxGrid('clearfilters');
                calculateStats();
            });

            $("#popupWindow").jqxWindow({
                width: 300, resizable: false,  isModal: true, autoOpen: false, maxWidth: 400, maxHeight: 250, showCloseButton: false, keyboardCloseKey: 'none' 
            });
            $('#exporttoexcelbutton').click(function () {
                if(userRole == 'Viewer') {
                        $("#popupWindow").jqxWindow({ title: 'Export to excel', position: { x: 'center', y: 'top' }, cancelButton: $('#cancelExportButton'), height: "125px", maxWidth: 400, isModal: true, draggable: false });
                        $('#divSetting').hide();
                        $('#divLoader').show();
                        $("#popupWindow").jqxWindow('open');

                        var rows = $("#jqxgrid").jqxGrid('getrows');
                        var tz = jstz.determine(); // Determines the time zone of the browser client
                        $.ajax({
                            type: "POST",
                            url: "/reports/export_client_data/",
                                data: JSON.stringify({datarows: rows, timezone: tz.name(), 
                                currency: $("#exportcurrency option:selected").text(),
                                format: 'excel'
                            }),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success : function(result) {
                                if(result.success == true) {
                                    $("#popupWindow").jqxWindow('close');
                                    window.open('/files/' + result.filename);
                                } else {
                                    alert(result.errors);
                                    return false;
                                }
                            }
                        });
                } else {
                        $("#popupWindow").jqxWindow({ title: 'Export to excel/CSV', position: { x: 'center', y: 'top' }, cancelButton: $('#cancelExportButton'), height: "125px", maxWidth: 400, isModal: true, draggable: false });
                        $('#divSetting').show();
                        $('#divLoader').hide();
                        $("#popupWindow").jqxWindow('open');
                }
            });

            $('#exportButton').click(function () {
                $('#divSetting').hide();
                $('#divLoader').show();

                var rows = $("#jqxgrid").jqxGrid('getrows');
                var tz = jstz.determine(); // Determines the time zone of the browser client
                $.ajax({
                    type: "POST",
                    url: "/reports/export_client_data/",
                    data: JSON.stringify({datarows: rows, timezone: tz.name(), 
                            currency: $("#exportcurrency option:selected").text(),
                            format: $("#exportformat").val()
                    }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#popupWindow").jqxWindow('close');
                            window.open('/files/' + result.filename);
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            });

            var toAppend = '';
            $.each(currencies,function(value,symbol){
                toAppend += '<option value="'+ value +'">'+ symbol +'</option>';
            });
            $('#statscurrency').append(toAppend);
            $('#exportcurrency').append(toAppend);
            $('#statscurrency').on('change', function () {
                    calculateStats();
            });
        });
    </script>
    <div id="tab-menu" align="left">
        <?php
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/client_report') && $loggedUser['role'] != 'Viewer') {
        ?>
            <div id="-reports-client-report" class="light-grey selected">
                    <a href="/reports/client_report">SEARCH</a>
            </div>
        <?php
                }
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/client_data')) {
        ?>
            <div id="-reports-client-data" class="light-grey">
                    <a href="/reports/client_data">CREATE/UPDATE YOUR RECORDS</a>
            </div>
        <?php
                }
        ?>
    </div>
<script type="text/javascript">
        $(document).ready(function() {
                $('#tab-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
        });
</script>    

<div id='jqxWidget'>
        <div style="margin-right: 7px; margin-bottom: 5px" align="right">
            <button value="Reset" id="clearfilteringbutton" title="Reset filters">RESET</button>
            <button style="margin-left: 5px" value="Export to Excel" id="exporttoexcelbutton">EXPORT .XLS</button>
        </div>
        <div id="jqxgrid"></div>
            <div style='margin-top: 20px;'>
        </div>

        <div style="margin-right: 5px; margin-top: 5px" align="right">
                <fieldset style="width: 260px">
                        <legend>Quick stats</legend>
                        <div id="no_of_records" style="padding-bottom: 5px">Number of records <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="no_of_clients" style="padding-bottom: 5px">Clients <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="no_of_pitches" style="padding-bottom: 5px">Pitches <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="no_of_lost" style="padding-bottom: 5px">Lost <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="no_of_cancelled" style="padding-bottom: 5px">Cancelled <span style="display: inline-block; width: 110px;"></span></div>
                        <div style="padding-bottom: 5px; <?php if ($userRole == 'Viewer') { echo 'display: none;'; }?>">Select stat currency <select id="statscurrency"></select></div>
                        <div id="sum_est_revenue" style="padding-bottom: 5px; <?php if ($userRole == 'Viewer') { echo 'display: none;'; }?>">Sum of est revenue <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="sum_act_revenue" style="padding-bottom: 5px; <?php if ($userRole == 'Viewer') { echo 'display: none;'; }?>">Sum of actual revenue <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="conversion_rate">Conversion rate <span style="display: inline-block; width: 110px;"></span></div>
                </fieldset>
        </div>

        <div id="popupWindow">
                <div>Export to excel</div>
                <div style="overflow: hidden;">
                        <div id="divSetting">
                                <div style="padding-bottom:10px;">
                                        Select format:
                                        <select id="exportformat"><option value="Excel">Excel</option><option value="csv">CSV</option></select>
                                </div>
                                <div>
                                        Select currency:
                                        <select id="exportcurrency"><option value="default">Actual currencies</option></select>
                                </div>
                                <div style="float: right; margin-top: 10px">
                                    <button style="margin-bottom: 5px;" id="exportButton">EXPORT</button>
                                    <button id="cancelExportButton">CANCEL</button>
                                </div>
                                <br />
                        </div>
                        <div id="divLoader" align="center" style="display: none; padding-top: 25px; padding-left: 90px;">
                                <div class="jqx-grid-load" style="float: left; overflow: hidden; width: 32px; height: 32px;"></div>
                                <span style="margin-top: 10px; float: left; display: block; margin-left: 5px;">Please wait...</span>
                        </div>
                </div>
        </div>
</div>
