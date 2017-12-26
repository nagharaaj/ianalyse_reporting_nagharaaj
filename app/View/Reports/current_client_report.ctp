
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
             var estimatedRevenueColumnTitle = '<?php echo 'iP '. date('Y') . ' Estimated Revenue';?>';
             var actualRevenueColumnTitle = '<?php echo 'iP '. (date('Y')-1) . ' Actual Revenue';?>';
             var cities = jQuery.parseJSON('<?php echo $cities; ?>');
             var arrCities = $.map(cities, function(el) { return el; });
             var markets = jQuery.parseJSON('<?php echo $markets; ?>');
             var arrMarkets = new Array();
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });
             var currencies = jQuery.parseJSON('<?php echo $currencies; ?>');
             var currYear = '<?php echo $current_year; ?>';
             var userRole = '<?php echo $userRole;?>';
             var widthPreferences = jQuery.parseJSON('<?php echo $widthPreferences; ?>');
             var arrCurrencies = new Array();
             var months = [
                     {value: 1, label: "Jan (1)"},
                     {value: 2, label: "Feb (2)"},
                     {value: 3, label: "Mar (3)"},
                     {value: 4, label: "Apr (4)"},
                     {value: 5, label: "May (5)"},
                     {value: 6, label: "Jun (6)"},
                     {value: 7, label: "Jul (7)"},
                     {value: 8, label: "Aug(8)"},
                     {value: 9, label: "Sep (9)"},
                     {value: 10, label: "Oct (10)"},
                     {value: 11, label: "Nov (11)"},
                     {value: 12, label: "Dec (12)"}
             ];
             var monthsSource =
             {
                 datatype: "array",
                 datafields: [
                     { name: 'label', type: 'string' },
                     { name: 'value', type: 'number' }
                 ],
                 localdata: months
             };
             var monthsAdapter = new $.jqx.dataAdapter(monthsSource, {
                autoBind: true
             });

             var calculateStats = function () {
                var dataRows = $('#jqxgrid').jqxGrid('getrows');
                var rowscount = dataRows.length;
                $('#no_of_records span').text(rowscount);
                var aggressivePitchesCount = 0;
                var defensivePitchesCount = 0;
                var totalClients = new Array();
                var totalAggressiveClients = new Array();
                var totalDefensiveClients = new Array();
                for(var i = 0; i < rowscount; i++) {
                        if(dataRows[i].PitchStage.match(/Won/g) || dataRows[i].PitchStage == 'Current client') {
                                if($.inArray(dataRows[i].ClientName, totalClients) == -1) {
                                        totalClients.push(dataRows[i].ClientName);
                                }
                        }
                        if(dataRows[i].PitchStage == 'Live - aggressive') {
                                aggressivePitchesCount++;
                                if($.inArray(dataRows[i].ClientName, totalAggressiveClients) == -1) {
                                        totalAggressiveClients.push(dataRows[i].ClientName);
                                }
                        }
                        if(dataRows[i].PitchStage == 'Live - defensive') {
                                defensivePitchesCount++;
                                if($.inArray(dataRows[i].ClientName, totalDefensiveClients) == -1) {
                                        totalDefensiveClients.push(dataRows[i].ClientName);
                                }
                        }
                }
                $('#no_of_clients span').text(totalClients.length);
                $('#no_of_aggressive_pitches_client span').text(totalAggressiveClients.length);
                $('#no_of_aggressive_pitches span').text(aggressivePitchesCount);
                $('#no_of_defensive_pitches_client span').text(totalDefensiveClients.length);
                $('#no_of_defensive_pitches span').text(defensivePitchesCount);
             }

             var horizontalScroll=function(){
                 var mousewheel = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel" //FF doesn't recognize mousewheel as of FF3.x
                 $("#jqxScrollWraphorizontalScrollBarjqxgrid").bind(mousewheel, function(e) {
                        e.preventDefault();
                        var position = $('#jqxgrid').jqxGrid('scrollposition');
                        var left = position.left;
                        var top = position.top;
                        var evt = window.event || e //equalize event object
                        evt = evt.originalEvent ? evt.originalEvent : evt; //convert to originalEvent
                        var delta = evt.detail ? evt.detail*(-40) : evt.wheelDelta //check for it is used by Opera and FF
                        if(delta > 0) {
                                top=top+45;
                                left=left-25;
                                $('#jqxgrid').jqxGrid('scrolloffset',top,left);
                        } else {
                                top=top-45;
                                left=left+25;
                                $('#jqxgrid').jqxGrid('scrolloffset', top,left);
                        }
                });
             }

             var source =
             {
                dataType: "json",
                id: 'id',
                url: "/reports/get_current_client_report_data/",
                datafields: [
                    { name: 'RecordId', type: 'number' },
                    { name: 'Region', type: 'string' },
                    { name: 'Country', type: 'string' },
                    { name: 'City', type: 'string' },
                    { name: 'LeadAgency', type: 'string' },
                    { name: 'ClientName', type: 'string' },
                    { name: 'ParentCompany', type: 'string' },
                    { name: 'ClientCategory', type: 'string' },
                    { name: 'PitchStart', type: 'string' },
                    { name: 'PitchStage', type: 'string' },
                    { name: 'ClientSince', type: 'string' },
                    { name: 'Lost', type: 'string' },
                    { name: 'Service', type: 'string' },
                    { name: 'MarketScope', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'number' },
                    { name: 'ActualRevenue', type: 'number' },
                    { name: 'Comments', type: 'string' },
                    { name: 'Year', type: 'number' },
                    { name: 'ParentId', type: 'number' },
                    { name: 'Created', type: 'date' },
                    { name: 'Modified', type: 'date' },
                    { name: 'SearchClientName', type: 'string' },
                    { name: 'SearchParentCompany', type: 'string' }
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
                rowsheight:45,
                height:'600',
                source: dataAdapter,
                enablemousewheel: true,
                pageable: false,
                pageSize: 20,
                pagerMode: 'simple',
                sortable: true,
                filterable: true,
                editable: false,
                autoRowHeight: false,
                columnsresize: true,
                autoshowfiltericon:true,
                autoshowcolumnsmenubutton: false,
                showpinnedcolumnbackground: false,
                enablehover: false,
                columnmenuopening: function (menu, datafield, height) {
                    var column = $("#jqxgrid").jqxGrid('getcolumn', datafield);
                    if (column.filtertype === "custom") {
                            menu.height(155);
                        setTimeout(function () {
                            menu.find('input').focus();
                        }, 25);
                    }
                    else menu.height(height);
                },
                columns: [
                  { text: 'RecordId', datafield: 'RecordId', hidden: true },
                  { text: 'ParentId', datafield: 'ParentId', hidden: true },
                  { text: 'Created', datafield: 'Created', hidden: true },
                  { text: 'Modified', datafield: 'Modified', hidden: true },
                  { text: 'Year', datafield: 'Year', hidden: true },
                  { text: '', datafield: 'SearchClientName', hidden: true },
                  { text: '', datafield: 'SearchParentCompany', hidden: true },
                  { text: 'Region', datafield: 'Region', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true },
                  { text: 'Country', datafield: 'Country', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true },
                  { text: 'City', datafield: 'City', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true },
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250, cellClassName: cellclass, pinned: true, filtertype: 'custom',
                      createfilterpanel: function (datafield, filterPanel) {
                          buildFilterPanel(filterPanel, datafield);
                      }
                  },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250, cellClassName: cellclass, filtertype: 'custom',
                      createfilterpanel: function (datafield, filterPanel) {
                          buildFilterPanel(filterPanel, datafield);
                      }
                  },
                  { text: 'Client Category', datafield: 'ClientCategory', width: 200, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Lead Agency', datafield: 'LeadAgency', width: 130, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Service', datafield: 'Service', width: 150, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Status', columntype: 'template', datafield: 'PitchStage', width: 130, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Client Since (M-Y)', datafield: 'ClientSince', width: 140, cellClassName: cellclass, filtertype: 'date', cellsformat: 'MM/yyyy', editable: false },
                  { text: 'Lost Since (M-Y)', datafield: 'Lost', width: 140, cellClassName: cellclass, filtertype: 'date', cellsformat: 'MM/yyyy', editable: false },
                  { text: 'Pitched (M-Y)', datafield: 'PitchStart', width: 140, cellClassName: cellclass, filtertype: 'date', cellsformat: 'MM/yyyy', editable: false},
                  { text: 'Scope', datafield: 'MarketScope', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', editable: false },
                  { text: 'Active Markets', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 160, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Currency', datafield: 'Currency', width: 100, cellClassName: cellclass, filtertype: 'checkedlist'},
                  { text: estimatedRevenueColumnTitle, columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 200, align: 'left', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', hidden: ((userRole == 'Viewer') ? true : false) },
                { text: actualRevenueColumnTitle, columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 200, align: 'left', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', hidden: ((userRole == 'Viewer') ? true : false) },
                  { text: 'Comments', columngroup: 'Comments', datafield: 'Comments', width: 230, cellClassName: cellclass }
                ],
                ready:function()
                {
                        defaultState = $("#jqxgrid").jqxGrid('savestate');
                        calculateStats();
                        horizontalScroll();
                        var columns = widthPreferences.columns;
                        if(columns) {
                                $.each(columns, function(columnName, columnSettings) {
                                       $('#jqxgrid').jqxGrid('setcolumnproperty',columnName,'width',columnSettings.width);
                                });
                        }
                }
            });

            $("#jqxgrid").on("columnresized", function (event) {
                    var state=null;
                    state = $("#jqxgrid").jqxGrid('savestate');
                    var obj=[];
                    obj= {
                            state:state,
                            formname:'client_report'
                         };
                    $.ajax({
                            type: "POST",
                            url: "/reports/user_grid_preferences/",
                            data: JSON.stringify(obj),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json"
                    });
            });
 
   });
    </script>

<script type="text/javascript">
        $(document).ready(function() {
                $('#tab-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
        });
</script>

<div id='jqxWidget'>
        <div id="tab-menu" align="left">
        <?php
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/current_client_report') && !preg_match('/Viewer/', $loggedUser['role'])) {
        ?>
            <div id="-reports-client-report" class="light-grey selected">
                    <a href="/reports/current_client_report">SEARCH</a>
            </div>
        <?php
                }
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/current_client_data')) {
        ?>
            <div id="-reports-client-data" class="light-grey">
                    <a href="/reports/current_client_data">CREATE/UPDATE YOUR RECORDS</a>
            </div>
        <?php
                }
        ?>
            <div style="float: right; margin-top: 35px;">
                <button value="Reset" id="clearfilteringbutton" title="Reset filters">RESET</button>
                <button style="margin-left: 5px" id="selectcurrencybutton" title="Select currency for report">SELECT CURRENCY</button>
                <button style="margin-left: 5px" value="Export to Excel" id="exporttoexcelbutton">EXPORT .XLS</button>
            </div>
        </div>
        <div id="jqxgrid"></div>

</div>
