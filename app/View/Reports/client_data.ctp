    <script type="text/javascript">
         $(document).ready(function () {

             var userRole = '<?php echo $userRole; ?>';
             var cities = jQuery.parseJSON('<?php echo $cities; ?>');
             var arrCities = $.map(cities, function(el) { return el; });
             var categories = jQuery.parseJSON('<?php echo $categories; ?>');
             var arrCategories = $.map(categories, function(el) { return el; });
             var countries = jQuery.parseJSON('<?php echo $countries; ?>');
             var arrCountries = $.map(countries, function(el) { return el; });
             var currencies = jQuery.parseJSON('<?php echo $currencies; ?>');
             var arrCurrencies = $.map(currencies, function(el) { return el; });
             var agencies = jQuery.parseJSON('<?php echo $agencies; ?>');
             var arrAgencies = $.map(agencies, function(el) { return el; });
             var services = jQuery.parseJSON('<?php echo $services; ?>');
             var arrServices = $.map(services, function(el) { return el; });
             var markets = jQuery.parseJSON('<?php echo $markets; ?>');
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });
             var stages = ['Live - aggressive', 'Live - defensive', 'Lost - current client', 'Lost - new business', 'Won - new business', 'Won - retained'];
             var arrStages = $.map(stages, function(el) { return el; });
             var arrMonths = ['Jan (1)', 'Feb (2)', 'Mar (3)', 'Apr (4)', 'May (5)', 'Jun (6)', 'Jul (7)', 'Aug (8)', 'Sep (9)', 'Oct (10)', 'Nov (11)', 'Dec (12)'];
             
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
             var years = [];
             var currDate = new Date();
             var currYear = currDate.getFullYear();
             for(i = 1995; i <= currYear; i++) {
                     years[years.length] = {value: i, label: i.toString()};
             }

             var theme = 'base';
             var classWon = 'stage-won';
             var classLost = 'stage-lost';
             var classLive = 'stage-live';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
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
                    { name: 'PitchStart', type: 'string' },
                    { name: 'PitchLeader', type: 'string' },
                    { name: 'PitchStage', type: 'string' },
                    { name: 'ClientMonth', type: 'string' },
                    { name: 'ClientYear', type: 'number' },
                    { name: 'Lost', type: 'string' },
                    { name: 'Service', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'float' },
                    { name: 'ActualRevenue', type: 'float' },
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
             $("#dataTable").jqxDataTable(
             {
                width: 1250,
                source: dataAdapter,
                pageable: true,
                pageSize: 20,
                sortable: false,
                editable: true,
                //filterable: true,
                //filterMode: 'advanced',
                selectionMode: 'singleRow',
                autoRowHeight: true,
                editSettings: { saveOnPageChange: true, saveOnBlur: true, saveOnSelectionChange: false, cancelOnEsc: true, saveOnEnter: true, editOnDoubleClick: false, editOnF2: false },
                // called when jqxDataTable is going to be rendered.
                rendering: function()
                {
                    // destroys all buttons.
                    if ($(".editButtons").length > 0) {
                        $(".editButtons").jqxButton('destroy');
                    }
                    if ($(".deleteButtons").length > 0) {
                        $(".deleteButtons").jqxButton('destroy');
                    }
                },
                // called when jqxDataTable is rendered.
                rendered: function () {
                    if ($(".editButtons").length > 0) {
                        $(".editButtons").jqxButton();
                        if(userRole == 'Global') {
                                $(".deleteButtons").jqxButton();
                                $(".deleteButtons").show();
                        }
                        
                        var editClick = function (event) {
                            var target = $(event.target);
                            // get button's value.
                            var value = target.val();
                            // get clicked row.
                            var rowIndex = parseInt(event.target.getAttribute('data-row'));
                            if (isNaN(rowIndex)) {
                                return;
                            }
                            if (value == "Edit") {
                                // begin edit.
                                var rules = new Array();
                                var recordid = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'RecordId');
                                var clientname = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ClientName');
                                var parentcompany = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ParentCompany');
                                var region = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Region');
                                var country = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Country');
                                var city = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'City');
                                var leadagency = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'LeadAgency');
                                var clientcategory = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ClientCategory');
                                var pitchstart = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'PitchStart');
                                var pitchleader = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'PitchLeader');
                                var pitchstage = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'PitchStage');
                                var clientsincemonth = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ClientMonth');
                                var clientsinceyear = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ClientYear');
                                var lostdate = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Lost');
                                var service = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Service');
                                var activemarkets = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ActiveMarkets');
                                var currency = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Currency');
                                var estimatedrevenue = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'EstimatedRevenue');
                                var actualrevenue = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ActualRevenue');
                                var comments = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Comments');
                                
                                var offset = $("#dataTable").offset();
                                $("#updateWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "700px", maxWidth: 700, isModal: true });
                                $("#updateWindow").attr('data-row', rowIndex);
                                $("#recordid").val(recordid);
                                if(region != null) {
                                        $("#divRegion").text(region);
                                } else {
                                        $("#divRegion").html('');
                                        var inpRegion = $("<div id=\"update_region\"></div>");
                                        $("#divRegion").append(inpRegion);
                                        $("#update_region").jqxDropDownList({ source: regions }).val(region);
                                }
                                if(country != null) {
                                        $("#divEntity").text(country);
                                } else {
                                        $("#divEntity").html('');
                                        var inpEntity = $("<div id=\"update_nameofentity\"></div>");
                                        $("#divEntity").append(inpEntity);
                                        $("#update_nameofentity").jqxDropDownList({ source: markets }).val(country);
                                }
                                if(city != null) {
                                        $("#divCity").text(city);
                                } else {
                                        $("#divCity").html('');
                                        var inpCity = $("<div id=\"update_city\"></div>");
                                        $("#divCity").append(inpCity);
                                        if(country == "Global") {
                                                $("#update_city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                                        } else if(country.match(/Regional/g)) {
                                                $("#update_city").jqxDropDownList({ source: [country], checkboxes: false, selectedIndex: 0 });
                                        } else {
                                                arrCities = cities[country];
                                                $("#update_city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                                        }
                                        $("#update_city").val(city);
                                }
                                if(leadagency != null) {
                                        $("#divAgency").text(leadagency);
                                } else {
                                        $("#divAgency").html('');
                                        var inpAgency = $("<div id=\"update_agency\"></div>");
                                        $("#divAgency").append(inpAgency);
                                        $("#update_agency").jqxDropDownList({ source: agencies }).val(leadagency);
                                }
                                $("#divAdvertiser").text(clientname);
                                if(parentcompany != null) {
                                        $("#divParentCompany").text(parentcompany);
                                } else {
                                        $("#divParentCompany").html('');
                                        var inpParentCompany = $("<input type=\"text\" id=\"update_parentcompany\" />");
                                        $("#divParentCompany").append(inpParentCompany);
                                        $("#update_parentcompany").jqxInput({ height: 25, width: 175 }).val(parentcompany);
                                }
                                if(clientcategory != null) {
                                        $("#divCategory").text(clientcategory);
                                } else {
                                        $("#divCategory").html('');
                                        var inpCategory = $("<div id=\"update_category\"></div>");
                                        $("#divCategory").append(inpCategory);
                                        $("#update_category").jqxDropDownList({ source: categories }).val(clientcategory);
                                }
                                if(pitchstart != '') {
                                        $("#divPitchStart").text(pitchstart);
                                } else {
                                        $("#divPitchStart").html('');
                                        var inpPitchStart = $("<div id=\"update_pitchstart\"></div>");
                                        $("#divPitchStart").append(inpPitchStart);
                                        $("#update_pitchstart").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                                }
                                if(pitchleader != '') {
                                        if(pitchstage.match(/Live/g)) {
                                                $("#divPitchLeader").html('');
                                                var inpPitchLeader = $("<input type=\"text\" id=\"update_pitchleader\" />");
                                                $("#divPitchLeader").append(inpPitchLeader);
                                                $("#update_pitchleader").jqxInput({ height: 25, width: 200 }).val(pitchleader);
                                                rules.push(validator.pitchleader);
                                        } else {
                                                $("#divPitchLeader").text(pitchleader);
                                        }
                                } else {
                                        $("#divPitchLeader").html('');
                                        var inpPitchLeader = $("<input type=\"text\" id=\"update_pitchleader\" />");
                                        $("#divPitchLeader").append(inpPitchLeader);
                                        $("#update_pitchleader").jqxInput({ height: 25, width: 200 }).val(pitchleader);
                                        rules.push(validator.pitchleader);
                                }
                                $("#update_pitchstage").jqxDropDownList({ source: ['Live - aggressive', 'Live - defensive', 'Lost - current client', 'Lost - new business', 'Won - new business', 'Won - retained'] }).val(pitchstage);
                                rules.push(validator.pitchstage);
                                if(clientsincemonth != 0 && clientsincemonth != null) {
                                        if(pitchstage.match(/Live/g)) {
                                                $("#divClientMonth").html('');
                                                var inpClientMonth = $("<div id=\"update_clientsincemonth\"></div>");
                                                $("#divClientMonth").append(inpClientMonth);
                                                $("#update_clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  }).val(clientsincemonth);
                                                //rules.push(validator.clientsincemonth);
                                        } else {
                                                $("#divClientMonth").text(arrMonths[parseInt(clientsincemonth-1)]);
                                        }
                                } else {
                                        if(pitchstage.match(/Lost/g)) {
                                                $("#divClientMonth").text('');
                                        } else {
                                                $("#divClientMonth").html('');
                                                var inpClientMonth = $("<div id=\"update_clientsincemonth\"></div>");
                                                $("#divClientMonth").append(inpClientMonth);
                                                $("#update_clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  });
                                                //rules.push(validator.clientsincemonth);
                                        }
                                }
                                if(clientsinceyear != 0 && clientsinceyear != null) {
                                        if(pitchstage.match(/Live/g)) {
                                                $("#divClientYear").html('');
                                                var inpClientYear = $("<div id=\"update_clientsinceyear\"></div>");
                                                $("#divClientYear").append(inpClientYear);
                                                $("#update_clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  }).val(clientsinceyear);
                                                rules.push(validator.clientsinceyear);
                                        } else {
                                                $("#divClientYear").text(clientsinceyear);
                                        }
                                } else {
                                        if(pitchstage.match(/Lost/g)) {
                                                $("#divClientYear").text('');
                                        } else {
                                                $("#divClientYear").html('');
                                                var inpClientYear = $("<div id=\"update_clientsinceyear\"></div>");
                                                $("#divClientYear").append(inpClientYear);
                                                $("#update_clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  });
                                                rules.push(validator.clientsinceyear);
                                        }
                                }
                                if(pitchstage.match(/Won/g)) {
                                        $("#divLostDate").text('No');
                                } else {
                                        $("#divLostDate").html('');
                                        var inpLostDate = $("<div id=\"update_lostdate\"></div>");
                                        $("#divLostDate").append(inpLostDate);
                                        $("#update_lostdate").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                                        var lostDate = lostdate.split('/');
                                        $("#update_lostdate").val(new Date(lostDate[1], (parseInt(lostDate[0])-1), 1));
                                        rules.push(validator.lostdate);
                                }
                                if(service != null) {
                                        $("#divService").text(service);
                                } else {
                                        $("#divService").html('');
                                        var inpService = $("<div id=\"update_service\"></div>");
                                        $("#divService").append(inpService);
                                        $("#update_service").jqxDropDownList({ source: services }).val(service);
                                }
                                $("#update_activemarket").jqxDropDownList({ source: countries, checkboxes: true });
                                var entities = activemarkets.split(',');
                                for(key in entities) {
                                        if(arrCountries.indexOf(entities[key]) != -1) {
                                               index = arrCountries.indexOf(entities[key]);
                                               $("#update_activemarket").jqxDropDownList('checkIndex', index);
                                        }
                                }
                                rules.push(validator.activemarkets);
                                if(currency != null) {
                                        $("#divCurrency").text(currency);
                                } else {
                                        $("#divCurrency").html('');
                                        var inpCurrency = $("<div id=\"update_currency\"></div>");
                                        $("#divCurrency").append(inpCurrency);
                                        $("#update_currency").jqxDropDownList({ source: currencies }).val(currency);
                                }
                                if(pitchstage.match(/Lost/g)) {
                                        $("#divEstRevenue").text(estimatedrevenue);
                                        $("#divActualRevenue").text(actualrevenue);
                                } else {
                                        $("#divEstRevenue").html('');
                                        var inpEstRevenue = $("<input type=\"text\" id=\"update_estrevenue\" />");
                                        $("#divEstRevenue").append(inpEstRevenue);
                                        $("#update_estrevenue").jqxInput({ height: 25, width: 150, rtl:true }).val(estimatedrevenue);
                                        rules.push(validator.estrevenueRequired);
                                        rules.push(validator.estrevenueNumeric);

                                        $("#divActualRevenue").html('');
                                        var inpActualRevenue = $("<input type=\"text\" id=\"update_actualrevenue\" />");
                                        $("#divActualRevenue").append(inpActualRevenue);
                                        $("#update_actualrevenue").jqxInput({ height: 25, width: 150, rtl:true }).val(actualrevenue);
                                        if(pitchstage.match(/Won/g)) {
                                                rules.push(validator.actualrevenueRequired);
                                                rules.push(validator.actualrevenueNumeric);
                                        }
                                }
                                $("#update_notes").jqxInput({ height: 25, width: 200 }).val(comments);
                                // show the popup window.
                                $("#updateWindow").jqxWindow('open');
                                $("#update_nameofentity").bind('select', function (event) {
                                    var args = event.args;
                                    var item = $('#update_nameofentity').jqxDropDownList('getItem', args.index);
                                    if(item != null) {
                                        if(item.label == "Global") {
                                                $("#update_city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                                        } else if(item.label.match(/Regional/g)) {
                                                $("#update_city").jqxDropDownList({ source: [item.label], checkboxes: false, selectedIndex: 0 });
                                        } else {
                                                arrCities = cities[item.label];
                                                $("#update_city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                                        }
                                    }
                                });
                                $('#updateForm').jqxValidator({ position: 'right', rules: rules});
                            }
                        }
                        $(".editButtons").on('click', function (event) {
                            editClick(event);
                        });
                        
                        $(".deleteButtons").on('click', function (event) {
                            var target = $(event.target);
                            // get button's value.
                            var value = target.val();
                            // get clicked row.
                            var rowIndex = parseInt(event.target.getAttribute('data-row'));
                            if (isNaN(rowIndex)) {
                                return;
                            }
                            var recordid = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'RecordId');
                            var row = { RecordId: recordid };
                            if(confirm('Are you sure to delete this record?')) {
                                $.ajax({
                                    type: "POST",
                                    url: "/reports/delete_client_record/",
                                    data: JSON.stringify(row),
                                    contentType: "application/json; charset=utf-8",
                                    dataType: "json",
                                    success : function(result) {
                                        if(result.success == true) {
                                            alert("Data deleted successfully...");
                                            $("#dataTable").jqxDataTable('updateBoundData');
                                        } else {
                                            alert(result.errors);
                                            return false;
                                        }
                                    }
                                });
                            }
                        });
                    }
                },
                columns: [
                  { text: 'RecordId', datafield: 'RecordId', hidden: true, editable: false },
                  { text: 'Region', datafield: 'Region', width: 100, cellClassName: cellclass, editable: false },
                  { text: 'Managing Entity', datafield: 'Country', width: 120, cellClassName: cellclass, editable: false },
                  { text: 'Managing City', datafield: 'City', width: 120, cellClassName: cellclass, editable: false },
                  { text: 'Lead Agency', datafield: 'LeadAgency', width: 130, cellClassName: cellclass, editable: false },
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250, cellClassName: cellclass, editable: false },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250, cellClassName: cellclass, editable: false },
                  { text: 'Client Category', datafield: 'ClientCategory', width: 200, cellClassName: cellclass, editable: false },
                  { text: 'Pitch Start', datafield: 'PitchStart', width: 100, cellClassName: cellclass },
                  { text: 'Pitch Leader', columngroup: 'PitchLeader', datafield: 'PitchLeader', width: 150, cellClassName: cellclass },
                  { text: 'Stage', datafield: 'PitchStage', width: 130, cellClassName: cellclass },
                  { text: 'Client Since Month', datafield: 'ClientMonth', width: 120, cellClassName: cellclass,
                      cellsRenderer: function (row, column, value) {
                          // render custom column.
                          return (value != 0 && value != null) ? arrMonths[parseInt(value-1)] : '';
                      }   
                  },
                  { text: 'Client Since Year', datafield: 'ClientYear', width: 120, cellClassName: cellclass },
                  { text: 'Lost (M-Y)', datafield: 'Lost', width: 100, cellClassName: cellclass },
                  { text: 'Service', datafield: 'Service', width: 150, cellClassName: cellclass, editable: false },
                  { text: 'Active Markets', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 250, cellClassName: cellclass },
                  { text: 'Currency', datafield: 'Currency', width: 100, cellClassName: cellclass, editable: false },
                  { text: 'iP estimated revenue', columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 120, align: 'right', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2' },
                  { text: 'iP 2014 Actual revenue', columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 120, align: 'right', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2' },
                  { text: 'Comments', columngroup: 'Comments', datafield: 'Comments', width: 200, cellClassName: cellclass },
                  {
                      text: '', cellsAlign: 'center', align: "center", pinned: true, columnType: 'none', width: 150, editable: false, sortable: false, dataField: null, 
                      cellsRenderer: function (row, column, value) {
                          // render custom column.
                          return "<button style='margin-right: 5px; display: none;' data-row='" + row + "' class='deleteButtons'>Delete</button><button data-row='" + row + "' class='editButtons'>Edit</button>";
                      }
                  }
                ]
            });

            $("#popupWindow").jqxWindow({
                width: 600, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelNew"), maxWidth: 700, maxHeight: 750, showCloseButton: false 
            });
            $("#updateWindow").jqxWindow({
                width: 600, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelUpdate"), maxWidth: 700, maxHeight: 700, showCloseButton: false 
            });
            $("#createNew").jqxButton({ theme: theme });
            $("#createNew").click(function () {
                var offset = $("#dataTable").offset();
                $("#popupWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "750px", maxWidth: 700, isModal: true });
                $("#region").jqxDropDownList({ source: regions, selectedIndex: -1 });
                $("#nameofentity").jqxDropDownList({ source: markets, selectedIndex: -1 });
                $("#city").jqxDropDownList();
                $("#agency").jqxDropDownList({ source: agencies, selectedIndex: -1 });
                $("#advertisername").jqxInput({ placeHolder: "Enter a Name", height: 25, width: 200, minLength: 1, 
                        source: function (query, response) {
                                var dataAdapter = new $.jqx.dataAdapter
                                (
                                        {
                                                datatype: "json",
                                                type: "POST",
                                                datafields:
                                                [
                                                        { name: 'advertiser_name' },
                                                        { name: 'parent_company' }
                                                ],
                                                url: "/reports/search_client/",
                                                data:
                                                {
                                                        maxRows: 20
                                                }
                                        },
                                        {
                                                autoBind: true,
                                                formatData: function (data) {
                                                        data.name_startsWith = query;
                                                        return data;
                                                },
                                                loadComplete: function (data) {
                                                        if (data.length > 0) {
                                                                response($.map(data, function (item) {
                                                                        return {
                                                                                label: item.advertiser_name + (item.parent_company ? ", " + item.parent_company : ""),
                                                                                value: item.advertiser_name + ", " + item.parent_company
                                                                        }
                                                                }));
                                                        }
                                                }
                                        }
                                );
                        }
                }).val('');
                $("#advertisername").on('select', function (event) {
                    if (event.args) {
                        var item = event.args.item;
                        if (item) {
                            var terms = item.value.split(/,\s*/);
                            $("#advertisername").jqxInput('val', terms[0]);
                            $("#parentcompany").jqxInput('val', terms[1]);
                        }
                    }
                });
                $("#parentcompany").jqxInput({ height: 25, width: 175 }).val('');
                $("#category").jqxDropDownList({ source: categories, selectedIndex: -1 });
                $("#pitchstart").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                $("#pitchleader").jqxInput({ height: 25, width: 200 }).val('');
                $("#pitchstage").jqxDropDownList({ source: ['Live - aggressive', 'Live - defensive', 'Lost - current client', 'Lost - new business', 'Won - new business', 'Won - retained'], selectedIndex: -1 });
                $("#clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  });
                $("#clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  });
                $("#lostdate").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                $("#service").jqxDropDownList({ source: services, selectedIndex: -1 });
                $("#activemarket").jqxDropDownList({ source: countries, checkboxes: true, selectedIndex: -1 });
                $("#currency").jqxDropDownList({ source: currencies, selectedIndex: -1 });
                $("#estrevenue").jqxInput({ height: 25, width: 100, rtl:true }).val('');
                $("#notes").jqxInput({ height: 25, width: 200 }).val('');
                // show the popup window.
                $("#popupWindow").jqxWindow('open');
                $("#nameofentity").bind('select', function (event) {
                    var args = event.args;
                    var item = $('#nameofentity').jqxDropDownList('getItem', args.index);
                    if(item != null) {
                        if(item.label == "Global") {
                                $("#city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                        } else if(item.label.match(/Regional/g)) {
                                $("#city").jqxDropDownList({ source: [item.label], checkboxes: false, selectedIndex: 0 });
                        } else {
                                arrCities = cities[item.label];
                                $("#city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                        }
                    }
                });
            });
            
            $('#testForm').jqxValidator({ position: 'right', rules: [
                        { input: '#advertisername', message: 'Advertiser name is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#parentcompany', message: 'Parent company is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#region', message: 'Region is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#city', message: 'City is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#agency', message: 'Lead agency is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#nameofentity', message: 'Entity is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#category', message: 'Category is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#pitchleader', message: 'Pitch leader is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#pitchstage', message: 'Stage is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#clientsinceyear', message: 'Client Since Year is required!', action: 'change', rule: function (input) {
                                var pitchstage = $('#pitchstage').val();
                                if (pitchstage.match(/Won/g) && input.val() == '') {
                                        return false;
                                }
                                return true;
                            } 
                        },
                        { input: '#lostdate', message: 'Lost Date is required!', action: 'change', rule: function (input) {
                                var pitchstage = $('#pitchstage').val();
                                if (pitchstage.match(/Lost/g) && input.val() == '') {
                                        return false;
                                }
                                return true;
                            } 
                        },
                        { input: '#service', message: 'Service is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#activemarket', message: 'Active Market is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#currency', message: 'Currency is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#estrevenue', message: 'iP estimated revenue is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#estrevenue', message: 'iP estimated revenue should be numeric!', action: 'keyup, blur', rule: function (input) {
                                if (!isNaN(parseFloat(input.val())) && isFinite(input.val())) {
                                        return true;
                                }
                                return false;
                            } 
                        }
                ]
            });
            
            $("#CancelNew").jqxButton({ theme: theme });
            $("#SaveNew").jqxButton({ theme: theme });
            // update the edited row when the user clicks the 'Save' button.
            $("#SaveNew").click(function () {
                if(!$('#testForm').jqxValidator('validate')) {
                        return false;
                }
                
                var row = { ClientName: $("#advertisername").val(), ParentCompany: $("#parentcompany").val(), Region: $("#region").val(),
                    Country: $("#nameofentity").val(), City: $("#city").val(), LeadAgency: $("#agency").val(), ClientCategory: $("#category").val(), 
                    PitchStart: $("#pitchstart").val(), PitchLeader: $("#pitchleader").val(), PitchStage: $("#pitchstage").val(),
                    ClientSinceMonth: $("#clientsincemonth").val(), ClientSinceYear: $("#clientsinceyear").val(),
                    Service: $("#service").val(), ActiveMarkets: $("#activemarket").val(), Currency: $("#currency").val(),
                    EstimatedRevenue: $("#estrevenue").val(), Comments: $("#notes").val()
                };
                $.ajax({
                    type: "POST",
                    url: "/reports/save_client_record/",
                    data: JSON.stringify(row),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            //alert("Data saved successfully...");
                            $("#dataTable").jqxDataTable('updateBoundData');
                            $("#popupWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
                
            });
            
            var validator = {
                    
                pitchstage : {
                        input: '#update_pitchstage', message: 'Stage is required!', action: 'change', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        }
                },
                pitchleader : {
                        input: '#update_pitchleader', message: 'Pitch Leader is required!', action: 'blur', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        } 
                },
                clientsincemonth: { input: "#update_clientsincemonth", message: 'Client Since Month is required!', action: 'change', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if ((pitchstage.match(/Live/g) || pitchstage.match(/Won/g)) && input.val() == '') {
                                return false;
                        }
                        return true;
                    } 
                },
                clientsinceyear: { input: "#update_clientsinceyear", message: 'Client Since Year is required!', action: 'change', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if ((pitchstage.match(/Live/g) || pitchstage.match(/Won/g)) && input.val() == '') {
                                return false;
                        }
                        return true;
                    } 
                },
                lostdate: { input: '#update_lostdate', message: 'Lost Date is required!', action: 'change', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if (pitchstage.match(/Lost/g) && input.val() == '') {
                                return false;
                        }
                        return true;
                    } 
                },
                activemarkets: { input: '#update_activemarket', message: 'Active Market is required!', action: 'change', rule: function (input) {
                        if (input.val() != '') {
                                return true;
                        }
                        return false;
                    } 
                },
                estrevenueRequired: { input: '#update_estrevenue', message: 'iP estimated revenue is required!', action: 'keyup, blur', rule: 'required' },
                estrevenueNumeric: { input: '#update_estrevenue', message: 'iP estimated revenue should be numeric!', action: 'keyup, blur', rule: function (input) {
                        if (!isNaN(parseFloat(input.val())) && isFinite(input.val())) {
                                return true;
                        }
                        return false;
                    } 
                },
                actualrevenueRequired: { input: '#update_actualrevenue', message: 'iP Actual revenue is required!', action: 'keyup, blur', rule: 'required' },
                actualrevenueNumeric: { input: '#update_actualrevenue', message: 'iP Actual revenue should be numeric!', action: 'keyup, blur', rule: function (input) {
                        if (!isNaN(parseFloat(input.val())) && isFinite(input.val())) {
                                return true;
                        }
                        return false;
                    } 
                }
            };
            
            $("#UpdateClient").jqxButton({ theme: theme });
            $("#CancelUpdate").jqxButton({ theme: theme });
            // update the edited row when the user clicks the 'Save' button.
            $("#UpdateClient").click(function () {
                if(!$('#updateForm').jqxValidator('validate')) {
                        return false;
                }
                
                var recordid = $("#recordid").val();
                var clientname = $("#divAdvertiser").text();
                if($('#update_parentcompany').val()) {
                        var parentcompany = $('#update_parentcompany').val();
                } else {
                        var parentcompany = $("#divParentCompany").text();
                }
                if($('#update_region').val()) {
                        var region = $('#update_region').val();
                } else {
                        var region = $('#divRegion').text();
                }
                if($('#update_nameofentity').val()) {
                        var country = $('#update_nameofentity').val();
                } else {
                        var country = $('#divEntity').text();
                }
                if($('#update_city').val()) {
                        var city = $('#update_city').val();
                } else {
                        var city = $('#divCity').text();
                }
                if($('#update_agency').val()) {
                        var leadagency = $('#update_agency').val();
                } else {
                        var leadagency = $('#divAgency').text();
                }
                if($('#update_category').val()) {
                        var clientcategory = $('#update_category').val();
                } else {
                        var clientcategory = $('#divCategory').text();
                }
                if($('#update_pitchstart').val()) {
                        var pitchstart = $('#update_pitchstart').val();
                } else {
                        var pitchstart = $('#divPitchStart').text();
                }
                if($('#update_pitchleader').val()) {
                        var pitchleader = $('#update_pitchleader').val();
                } else {
                        var pitchleader = $('#divPitchLeader').text();
                }
                var pitchstage = $('#update_pitchstage').val();
                if($('#update_clientsincemonth').val()) {
                        var clientsincemonth = $('#update_clientsincemonth').val();
                } else {
                        var clientsincemonth = $('#divClientMonth').text();
                }
                if($('#update_clientsinceyear').val()) {
                        var clientsinceyear = $('#update_clientsinceyear').val();
                } else {
                        var clientsinceyear = $('#divClientYear').text();
                }
                if($('#update_lostdate').val()) {
                        var lostdate = $('#update_lostdate').val();
                } else {
                        var lostdate = $('#divLostDate').text();
                }
                if($('#update_service').val()) {
                        var service = $('#update_service').val();
                } else {
                        var service = $('#divService').text();
                }
                var activemarkets = $('#update_activemarket').val();
                if($('#update_currency').val()) {
                        var currency = $('#update_currency').val();
                } else {
                        var currency = $('#divCurrency').text();
                }
                if($('#update_estrevenue').val()) {
                        var estimatedrevenue = $('#update_estrevenue').val();
                } else {
                        var estimatedrevenue = $('#divEstRevenue').text();
                }
                if($('#update_actualrevenue').val()) {
                        var actualrevenue = $('#update_actualrevenue').val();
                } else {
                        var actualrevenue = $('#divActualRevenue').text();
                }
                var comments = $("#update_notes").val();
                
                var row = { RecordId: recordid, ClientName: clientname, ParentCompany: parentcompany, Region: region,
                    Country: country, City: city, LeadAgency: leadagency, ClientCategory: clientcategory, 
                    PitchStart: pitchstart, PitchLeader: pitchleader, PitchStage: pitchstage,
                    ClientSinceMonth: clientsincemonth, ClientSinceYear: clientsinceyear, LostDate: lostdate,
                    Service: service, ActiveMarkets: activemarkets, Currency: currency,
                    EstimatedRevenue: estimatedrevenue, ActualRevenue: actualrevenue, Comments: comments
                };
                $.ajax({
                    type: "POST",
                    url: "/reports/update_client_record/",
                    data: JSON.stringify(row),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#dataTable").jqxDataTable('updateBoundData');
                            $("#updateWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
                
            });
        });
    </script>
    <div id="tab-menu" align="left">
            <div id="-reports-client-report" class="light-grey">
                    <a href="/reports/client_report">Search</a>
            </div>
            <div id="-reports-client-data" class="light-grey selected">
                    <a href="/reports/client_data">Create/Update your records</a>
            </div>
    </div>
<script type="text/javascript">
        $(document).ready(function() {
                $('#tab-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
        });
</script>    
<div id='jqxWidget'>
                    
        <div id="dataTable"></div>
            <div style='margin-top: 20px;'>
            <div style='float: right; padding-right: 15px;'>
                <button value="Add a new record" id='createNew'>Add a new record</button>
            </div>
        </div>
    </div>
    
    <div id="popupWindow">
        <div>Add a new record</div>
        <div style="overflow: hidden;">
        <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelNew" value="Cancel">Cancel</button></div>
        <form id="testForm" action="./">
            <table>
                <tr>
                    <td align="right" style="width: 200px;">Region</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="region"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Entity</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="nameofentity"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">City</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="city"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Lead Agency</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="agency"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Advertiser Name</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="advertisername" autocomplete="off"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Parent Company</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="parentcompany"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Category</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="category"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Pitch Start</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="pitchstart"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Pitch Leader</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="pitchleader"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Stage</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="pitchstage"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Client Since Month</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="clientsincemonth"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Client Since Year</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="clientsinceyear"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Lost (M-Y)</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="lostdate"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Service</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="service"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Active Market</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="activemarket"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Currency</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="currency"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">iP estimated revenue</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="estrevenue"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Notes</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="notes"/></td>
                    <td style="width: 150px"></td>
                </tr>
            </table>
        </form>
        <div style="padding-top: 10px;" align="right"><button style="margin-right: 15px;" id="SaveNew" value="Add to existing records">Add to existing records</button></div>
        </div>
   </div>

    <div id="updateWindow">
        <div>Edit record</div>
        <div style="overflow: hidden;">
        <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelUpdate" value="Cancel">Cancel</button></div>
        <form id="updateForm" action="./">
            <table>
                <tr>
                    <td align="right" style="width: 200px; padding-bottom: 5px; padding-right: 5px">Region</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divRegion"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Entity</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divEntity"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">City</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divCity"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Lead Agency</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divAgency"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Advertiser Name</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divAdvertiser"></div>
                            <input type="hidden" id="recordid"/>
                    </td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Parent Company</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divParentCompany"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Category</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divCategory"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Pitch Start</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divPitchStart"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Pitch Leader</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divPitchLeader"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Stage</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="update_pitchstage"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Client since M</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divClientMonth"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Client since Y</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divClientYear"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Lost (M-Y)</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divLostDate"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Service</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divService"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Active Market</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="update_activemarket"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Currency</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divCurrency"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">iP estimated revenue</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divEstRevenue"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">iP 2014 actual revenue</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divActualRevenue"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Notes</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="update_notes"/></td>
                    <td style="width: 150px"></td>
                </tr>
            </table>
        </form>
        <div style="padding-top: 20px;" align="right"><button style="margin-right: 15px;" id="UpdateClient">Update existing record</button></div>
        </div>
   </div>
