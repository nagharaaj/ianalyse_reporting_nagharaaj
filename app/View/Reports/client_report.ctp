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
             var arrCurrencies = new Array();
             
             var calculateStats = function () {
                var dataRows = $('#jqxgrid').jqxGrid('getrows');
                var rowscount = dataRows.length;
                $('#no_of_records span').text(rowscount);
                var clientsCount = 0;
                var pitchesCount = 0;
                var lostCount = 0;
                var sumEstRevenue = 0;
                var sumActRevenue = 0;
                var statsCurrency = $("#statscurrency option:selected").text();
                var convert_ratio = $("#statscurrency").val();
                for(var i = 0; i < rowscount; i++) {
                        if(dataRows[i].PitchStage.match(/Won/g)) {
                                clientsCount++;
                        }
                        if(dataRows[i].PitchStage.match(/Won/g) || dataRows[i].PitchStage.match(/Live/g)) {
                                pitchesCount++;
                        }
                        if(dataRows[i].PitchStage.match(/Lost/g)) {
                                lostCount++;
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
                if(clientsCount > 0 || lostCount > 0) {
                        var conversion_rate = (clientsCount/(clientsCount+lostCount))*100;
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
                url: "/reports/get_client_data/",
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
                    { name: 'PitchLeader', type: 'string' },
                    { name: 'PitchStage', type: 'string' },
                    { name: 'ClientSince', type: 'date' },
                    { name: 'Lost', type: 'date' },
                    { name: 'Service', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'number' },
                    { name: 'ActualRevenue', type: 'number' },
                    { name: 'Comments', type: 'string' }
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
                if(stage.match(/Won/g)) {
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
                width: 1250,
                autoheight: true,
                source: dataAdapter,
                pageable: true,
                pageSize: 20,
                pagerMode: 'simple',
                sortable: true,
                filterable: true,
                showfilterrow: true,
                editable: false,
                autoRowHeight: true,
                columns: [
                  { text: 'RecordId', datafield: 'RecordId', hidden: true },
                  { text: 'Region', datafield: 'Region', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      } 
                  },
                  { text: 'Managing Entity', datafield: 'Country', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Managing City', datafield: 'City', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Lead Agency', datafield: 'LeadAgency', width: 130, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250, cellClassName: cellclass },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250, cellClassName: cellclass },
                  { text: 'Client Category', datafield: 'ClientCategory', width: 200, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 200 });
                      } 
                  },
                  { text: 'Pitch Start', datafield: 'PitchStart', width: 100, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy' },
                  { text: 'Pitch Leader', columngroup: 'PitchLeader', datafield: 'PitchLeader', width: 150, cellClassName: cellclass },
                  { text: 'Stage', columntype: 'template', datafield: 'PitchStage', width: 130, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Client Since (M-Y)', datafield: 'ClientSince', width: 125, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy' },
                  { text: 'Lost (M-Y)', datafield: 'Lost', width: 100, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy' },
                  { text: 'Service', datafield: 'Service', width: 150, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Active Markets', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 200, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 200 });
                      } 
                  },
                  { text: 'Currency', datafield: 'Currency', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      } 
                  },
                  { text: 'iP estimated revenue', columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 120, align: 'right', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2' },
                  { text: 'iP 2014 Actual revenue', columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 120, align: 'right', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2' },
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
            $('#jqxgrid').jqxGrid({ rendered: function(){
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
            // clear the filtering.
            $('#clearfilteringbutton').click(function () {
                $("#jqxgrid").jqxGrid('clearfilters');
                calculateStats();
            });
            
            $('#exporttoexcelbutton').click(function () {
                var rows = $("#jqxgrid").jqxGrid('getrows');
                $.ajax({
                    type: "POST",
                    url: "/reports/export_client_data/",
                    data: JSON.stringify(rows),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            //alert("Data saved successfully...");
                            window.open('/files/Client_Data_<?php echo date('m-d-Y'); ?>.xlsx');
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
            $('#statscurrency').on('change', function () {
                    calculateStats();
            });
        });
    </script>
    <div id="tab-menu" align="left">
        <?php 
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/client_report')) {
        ?>
            <div id="-reports-client-report" class="light-grey selected">
                    <a href="/reports/client_report">Search</a>
            </div>
        <?php
                }
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/client_data')) {
        ?>
            <div id="-reports-client-data" class="light-grey">
                    <a href="/reports/client_data">Create/Update your records</a>
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
            <button value="Reset" id="clearfilteringbutton">Clear filter</button>
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
                        <div style="padding-bottom: 5px">Select stat currency <select id="statscurrency"></select></div>
                        <div id="sum_est_revenue" style="padding-bottom: 5px">Sum of est revenue <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="sum_act_revenue" style="padding-bottom: 5px">Sum of actual revenue <span style="display: inline-block; width: 110px;"></span></div>
                        <div id="conversion_rate">Conversion rate <span style="display: inline-block; width: 110px;"></span></div>
                </fieldset>
        </div>
        
        <div style="margin-right: 7px; margin-top: 15px" align="right">
                <button value="Export to Excel" id="exporttoexcelbutton">Export to Excel</button>
        </div>
</div>
