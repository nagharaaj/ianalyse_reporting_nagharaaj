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
                        if(dataRows[i].PitchStage == 'Cancelled' || dataRows[i].PitchStage == 'Declined') {
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
             var textInput;
             var buildFilterPanel = function (filterPanel, datafield) {
                textInput = $("<input style='margin:5px;'/>");
                var applyinput = $("<div class='filter' style='height: 25px; margin-left: 20px; margin-top: 7px;'></div>");
                var filterbutton = $('<span tabindex="0" style="padding: 4px 12px; margin-left: 2px;">Filter</span>');
                applyinput.append(filterbutton);
                var filterclearbutton = $('<span tabindex="0" style="padding: 4px 12px; margin-left: 5px;">Clear</span>');
                applyinput.append(filterclearbutton);
                filterPanel.append(textInput);
                filterPanel.append(applyinput);
                filterbutton.jqxButton({ theme: theme, height: 20 });
                filterclearbutton.jqxButton({ theme: theme, height: 20 });
                var column = $("#jqxgrid").jqxGrid('getcolumn', datafield);
                textInput.jqxInput({ theme: theme, placeHolder: "Enter " + column.text, popupZIndex: 9999999, displayMember: datafield, /*source: dataadapter,*/ height: 23, width: 155 });
                textInput.keyup(function (event) {
                    if (event.keyCode === 13) {
                        filterbutton.trigger('click');
                    }
                });
                filterbutton.click(function () {
                    var filtergroup = new $.jqx.filter();
                    var filter_or_operator = 1;
                    var filtervalue = textInput.val();
                    var filtercondition = 'contains';
                    var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);            
                    filtergroup.addfilter(filter_or_operator, filter1);
                    // add the filters.
                    $("#jqxgrid").jqxGrid('addfilter', "Search"+datafield, filtergroup);
                    // apply the filters.
                    $("#jqxgrid").jqxGrid('applyfilters');
                    $("#jqxgrid").jqxGrid('closemenu');
                });
                filterbutton.keydown(function (event) {
                    if (event.keyCode === 13) {
                        filterbutton.trigger('click');
                    }
                });
                filterclearbutton.click(function () {
                    $("#jqxgrid").jqxGrid('removefilter', "Search"+datafield);
                    // apply the filters.
                    $("#jqxgrid").jqxGrid('applyfilters');
                    $("#jqxgrid").jqxGrid('closemenu');
                    textInput.val("");
                });
                filterclearbutton.keydown(function (event) {
                    if (event.keyCode === 13) {
                        filterclearbutton.trigger('click');
                    }
                    textInput.val("");
                });
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
                editable: false,
                autoRowHeight: true,
                columnsresize: true,
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
                  { text: 'Region', datafield: 'Region', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', filteritems: arrRegions, pinned: true },
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
                  { text: 'Status', columntype: 'template', datafield: 'PitchStage', width: 130, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Service', datafield: 'Service', width: 150, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Division', datafield: 'Division', width: 150, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Client Since (M-Y)', datafield: 'ClientSince', width: 125, cellClassName: cellclass, filtertype: 'date', cellsformat: 'MM/yyyy' },
                  { text: 'Lost Since (M-Y)', datafield: 'Lost', width: 100, cellClassName: cellclass, filtertype: 'date', cellsformat: 'MM/yyyy' },
                  { text: 'Pitched (M-Y)', datafield: 'PitchStart', width: 100, cellClassName: cellclass, filtertype: 'date', cellsformat: 'MM/yyyy' },
                  /*{ text: 'Pitch Leader', columngroup: 'PitchLeader', datafield: 'PitchLeader', width: 150, cellClassName: cellclass },*/
                  { text: 'Active Markets', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 200, cellClassName: cellclass, filtertype: 'checkedlist' },
                  { text: 'Currency', datafield: 'Currency', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', hidden: ((userRole == 'Viewer') ? true : false) },
                  { text: estimatedRevenueColumnTitle, columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 160, align: 'center', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', hidden: ((userRole == 'Viewer') ? true : false) },
                  { text: actualRevenueColumnTitle, columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 150, align: 'center', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', hidden: ((userRole == 'Viewer') ? true : false) },
                  { text: 'Comments', columngroup: 'Comments', datafield: 'Comments', width: 200, cellClassName: cellclass }
                ],
                ready: calculateStats
            });
            $("#jqxgrid").on("filter", function (event) {
                    calculateStats();
                    $("#jqxgrid").jqxGrid('setcolumnproperty', 'City', 'filteritems', false);
                    $("#jqxgrid").jqxGrid('setcolumnproperty', 'Country', 'filteritems', false);

                    var paginginfo = $("#jqxgrid").jqxGrid('getpaginginformation');
                    if(paginginfo.pagescount <= 1) {
                        $('#pagerjqxgrid').hide();
                    } else {
                        $('#pagerjqxgrid').show();
                    }
                    
                    var filterGroups = $("#jqxgrid").jqxGrid('getfilterinformation');
                    if(filterGroups.length) {
                        for (var i = 0; i < filterGroups.length; i++) {
                            var filterGroup = filterGroups[i];
                            if(filterGroup.filtercolumn == 'Region') {
                                var arrRegionCountries = new Array();
                                var arrCities = new Array();
                                var filters = filterGroup.filter.getfilters();
                                for (var j = 0; j < filters.length; j++) {
                                    $.map(markets[filters[j].value], function(el) { 
                                            arrRegionCountries.push(el);
                                            $.map(cities[el], function(elm) { arrCities.push(elm); });
                                    });
                                }
                                arrRegionCountries.sort();
                                $("#jqxgrid").jqxGrid('setcolumnproperty', 'Country', 'filteritems', arrRegionCountries);
                                arrCities.sort();
                                $("#jqxgrid").jqxGrid('setcolumnproperty', 'City', 'filteritems', arrCities);
                            }
                            if(filterGroup.filtercolumn == 'Country') {
                                var arrCities = new Array();
                                var filters = filterGroup.filter.getfilters();
                                for (var j = 0; j < filters.length; j++) {
                                    $.map(cities[filters[j].value], function(el) { arrCities.push(el); });
                                }
                                arrCities.sort();
                                $("#jqxgrid").jqxGrid('setcolumnproperty', 'City', 'filteritems', arrCities);
                            }
                        }
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
            $('#selectcurrencybutton').jqxButton({ theme: theme });
            $('#changeCurrencyButton').jqxButton({ theme: theme });
            $('#cancelCurrencyButton').jqxButton({ theme: theme });
            // clear the filtering.
            $('#clearfilteringbutton').click(function () {
                $("#jqxgrid").jqxGrid('clearfilters');
                if(textInput) {
                    textInput.val("");
                }
                calculateStats();
            });

            $("#popupWindow").jqxWindow({
                width: 300, resizable: false,  isModal: true, autoOpen: false, maxWidth: 400, maxHeight: 250, showCloseButton: false, keyboardCloseKey: 'none' 
            });
            $('#exporttoexcelbutton').click(function () {
                if(userRole == 'Viewer' || userRole == 'Country - Viewer') {
                        $("#popupWindow").jqxWindow({ title: 'Export to excel', position: { x: 'center', y: 'top' }, cancelButton: $('#cancelExportButton'), height: "125px", maxWidth: 400, isModal: true, draggable: false });
                        $('#divSetting').hide();
                        $('#divLoader').show();
                        $('#divRevenueCurrency').hide();
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
                        $('#divRevenueCurrency').hide();
                        $("#popupWindow").jqxWindow('open');
                }
            });

            $('#selectcurrencybutton').click(function () {
                $("#popupWindow").jqxWindow({ title: 'Select currency for revenue', position: { x: 'center', y: 'top' }, cancelButton: $('#cancelCurrencyButton'), height: "125px", maxWidth: 400, isModal: true, draggable: false });
                $('#divSetting').hide();
                $('#divLoader').hide();
                $('#divRevenueCurrency').show();
                $("#popupWindow").jqxWindow('open');
            });

            $('#changeCurrencyButton').click(function () {
                var revenueCurrency = $("#revenuecurrency option:selected").text();
                source.data = { revenue_currency : revenueCurrency };
                $("#exportcurrency option").filter(function() {
                    //may want to use $.trim in here
                    return $(this).text() == revenueCurrency;
                }).prop('selected', true);
                $("#statscurrency option").filter(function() {
                    //may want to use $.trim in here
                    return $(this).text() == revenueCurrency;
                }).prop('selected', true);
                dataAdapter.dataBind();
                calculateStats();
                $("#popupWindow").jqxWindow('close');
            });

            $('#exportButton').click(function () {
                $('#divSetting').hide();
                $('#divLoader').show();
                $('#divRevenueCurrency').hide();

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
            $('#revenuecurrency').append(toAppend);
            $('#statscurrency').on('change', function () {
                    calculateStats();
            });
        });
    </script>
    <div id="tab-menu" align="left">
        <?php
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/client_report') && !preg_match('/Viewer/', $loggedUser['role'])) {
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
            <button style="margin-left: 5px" id="selectcurrencybutton" title="Select currency for report">SELECT CURRENCY</button>
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
                        <div id="no_of_cancelled" style="padding-bottom: 5px">Cancelled/Declined <span style="display: inline-block; width: 110px;"></span></div>
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
                        <div id="divRevenueCurrency">
                                <div style="padding: 10px 5px">
                                        Select currency:
                                        <select id="revenuecurrency"><option value="default">Actual currencies</option></select>
                                </div>
                                <div style="float: right; margin-top: 10px">
                                    <button style="margin-bottom: 5px;" id="changeCurrencyButton">SUBMIT</button>
                                    <button id="cancelCurrencyButton">CANCEL</button>
                                </div>
                                <br />
                        </div>
                </div>
        </div>
</div>
