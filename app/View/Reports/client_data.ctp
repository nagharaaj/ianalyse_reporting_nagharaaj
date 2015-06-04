    <script type="text/javascript">
         var editClick;
         var deleteClick;
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
             var arrMarkets = $.map(markets, function(el) { return el; });
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });
             var stages = jQuery.parseJSON('<?php echo $stages; ?>');
             var arrStages = $.map(stages, function(el) { return el; });
             var divisions = jQuery.parseJSON('<?php echo $divisions; ?>');
             var arrDivisions = $.map(divisions, function(el) { return el; });
             var arrMonths = ['Jan (1)', 'Feb (2)', 'Mar (3)', 'Apr (4)', 'May (5)', 'Jun (6)', 'Jul (7)', 'Aug (8)', 'Sep (9)', 'Oct (10)', 'Nov (11)', 'Dec (12)'];
             var currMonth = '<?php echo $currMonth; ?>';
             var currYear = '<?php echo $currYear; ?>';

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
                id: 'RecordId',
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
                    /*{ name: 'PitchLeader', type: 'string' },*/
                    { name: 'PitchStage', type: 'string' },
                    { name: 'ClientMonth', type: 'string' },
                    { name: 'ClientYear', type: 'number' },
                    { name: 'Lost', type: 'date' },
                    { name: 'Service', type: 'string' },
                    { name: 'Division', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'float' },
                    { name: 'ActualRevenue', type: 'float' },
                    { name: 'Comments', type: 'string' },
                    { name: 'ParentId', type: 'number' }
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
                autoheight: true,
                source: dataAdapter,
                enablemousewheel: false,
                pageable: true,
                pageSize: 20,
                pagerMode: 'simple',
                sortable: true,
                filterable: true,
                showfilterrow: true,
                editable: true,
                autoRowHeight: true,
                selectionmode: 'none',
                columnsresize: true,
                showpinnedcolumnbackground: false,
                enablehover: false,
                columns: [
                  {
                      text: '', cellsAlign: 'center', pinned: true, columntype: 'custom', width: 255, sortable: false, dataField: null, filterable: false, editable: false,
                      cellsRenderer: function (row, column, value) {
                          // render custom column.
                          return "<div align='center'><button style='margin-right: 5px;' data-row='" + row + "' class='deleteButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='deleteClick(event)'>DELETE</button><button style='margin-right: 5px;' data-row='" + row + "' class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='duplicateClick(event)'>DUPLICATE</button><button data-row='" + row + "' class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='editClick(event)'>EDIT</button></div>";
                      }
                  },
                  /*{
                      text: '', columntype: 'checkbox', width: 50, align: 'center', cellsalign: 'center', pinned: true, sortable: false, dataField: null, filterable: false
                  },*/
                  { text: 'RecordId', datafield: 'RecordId', hidden: true },
                  { text: 'ParentId', datafield: 'ParentId', hidden: true },
                  { text: 'Region', datafield: 'Region', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true, editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      }
                  },
                  { text: 'Country', datafield: 'Country', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true, editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'City', datafield: 'City', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', pinned: true, editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Client', datafield: 'ClientName', width: 250, cellClassName: cellclass, pinned: true, editable: false },
                  { text: 'Parent Company', datafield: 'ParentCompany', width: 250, cellClassName: cellclass, editable: false },
                  { text: 'Client Category', datafield: 'ClientCategory', width: 200, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 200 });
                      }
                  },
                  { text: 'Lead Agency', datafield: 'LeadAgency', width: 130, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Status', datafield: 'PitchStage', width: 130, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Service', datafield: 'Service', width: 150, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Division', datafield: 'Division', width: 150, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      }
                  },
                  { text: 'Client Since Month', datafield: 'ClientMonth', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      }
                  },
                  { text: 'Client Since Year', datafield: 'ClientYear', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      }
                  },
                  { text: 'Lost Since (M-Y)', datafield: 'Lost', width: 100, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy', editable: false },
                  { text: 'Pitched (M-Y)', datafield: 'PitchStart', width: 100, cellClassName: cellclass, filtertype: 'range', cellsformat: 'MM/yyyy', editable: false },
                  /*{ text: 'Pitch Leader', columngroup: 'PitchLeader', datafield: 'PitchLeader', width: 150, cellClassName: cellclass, editable: false },*/
                  { text: 'Active Markets', datafield: 'ActiveMarkets', width: 250, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 200 });
                      }
                  },
                  { text: 'Currency', datafield: 'Currency', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', editable: false,
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      }
                  },
                  { text: 'iP estimated revenue', datafield: 'EstimatedRevenue', width: 130, align: 'center', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', editable: false },
                  { text: 'iP 2014 Actual revenue', datafield: 'ActualRevenue', width: 150, align: 'center', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2', editable: false },
                  { text: 'Comments', datafield: 'Comments', width: 200, cellClassName: cellclass, editable: false }
                ]
            });
            $("#jqxgrid").on("filter", function (event) {
                    var paginginfo = $("#jqxgrid").jqxGrid('getpaginginformation');
                    if(paginginfo.pagescount <= 1) {
                        $('#pagerjqxgrid').hide();
                    } else {
                        $('#pagerjqxgrid').show();
                    }

                    if ($(".editButtons").length > 0) {
                        $( ".editButtons" ).each(function( i ) {
                                $(this).parent().parent().css('line-height', $(this).parent().parent().css('height'));
                        });
                    }
            });
            $('#jqxgrid').jqxGrid({ rendered: function() {
                    var paginginfo = $("#jqxgrid").jqxGrid('getpaginginformation');
                    if(paginginfo.pagescount <= 1) {
                        $('#pagerjqxgrid').hide();
                    } else {
                        $('#pagerjqxgrid').show();
                    }

                    if ($(".editButtons").length > 0) {
                        $( ".editButtons" ).each(function( i ) {
                                $(this).parent().parent().css('line-height', $(this).parent().parent().css('height'));
                        });
                    }
                }
            });
            /*$("#jqxgrid").on("pagechanged", function (event) {
                    if ($(".editButtons").length > 0) {
                        $( ".editButtons" ).each(function( i ) {
                                $(this).parent().parent().css('line-height', $(this).parent().parent().css('height'));
                        });
                    }
            });*/

                editClick = function (event) {
                        var target = $(event.target);
                        // get button's value.
                        var value = target.val();
                        // get clicked row.
                        var rowIndex = parseInt(event.target.getAttribute('data-row'));
                        if (isNaN(rowIndex)) {
                            return;
                        }
                        // begin edit.
                        var rules = new Array();
                        var data = $('#jqxgrid').jqxGrid('getrowdata', rowIndex);
                        var recordid = data.RecordId;
                        var parentrecordid = data.ParentId;
                        var clientname = data.ClientName;
                        var parentcompany = data.ParentCompany;
                        var region = data.Region;
                        var country = data.Country;
                        var city = data.City;
                        var leadagency = data.LeadAgency;
                        var clientcategory = data.ClientCategory;
                        var pitchstart = data.PitchStart;
                        /*var pitchleader = data.PitchLeader;*/
                        var pitchstage = data.PitchStage;
                        var clientsincemonth = data.ClientMonth;
                        var clientsinceyear = data.ClientYear;
                        var lostdate = data.Lost;
                        var service = data.Service;
                        var division = data.Division;
                        var activemarkets = data.ActiveMarkets;
                        var currency = data.Currency;
                        var estimatedrevenue = data.EstimatedRevenue;
                        var actualrevenue = data.ActualRevenue;
                        var comments = data.Comments;

                        var offset = $("#jqxgrid").offset();
                        $("#updateWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "750px", maxWidth: 700, isModal: true });
                        $("#updateWindow").attr('data-row', rowIndex);
                        $("#recordid").val(recordid);
                        $("#update_parentrecordid").val(parentrecordid);
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
                                //$("#divAgency").text(leadagency);
                                $("#divAgency").html('');
                                var inpAgency = $("<div id=\"update_agency\"></div>");
                                $("#divAgency").append(inpAgency);
                                $("#update_agency").jqxDropDownList({ source: agencies }).val(leadagency);
                                rules.push(validator.agency);
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
                                //$("#divCategory").text(clientcategory);
                                $("#divCategory").html('');
                                var inpCategory = $("<div id=\"update_category\"></div>");
                                $("#divCategory").append(inpCategory);
                                $("#update_category").jqxDropDownList({ source: categories }).val(clientcategory);
                                rules.push(validator.category);
                        } else {
                                $("#divCategory").html('');
                                var inpCategory = $("<div id=\"update_category\"></div>");
                                $("#divCategory").append(inpCategory);
                                $("#update_category").jqxDropDownList({ source: categories }).val(clientcategory);
                        }
                        if(pitchstart != '') {
                                pitchDate = new Date(pitchstart);
                                $("#divPitchStart").text((pitchDate.getMonth()+1) + '/' + pitchDate.getFullYear());
                        } else {
                                $("#divPitchStart").html('');
                                var inpPitchStart = $("<div id=\"update_pitchstart\"></div>");
                                $("#divPitchStart").append(inpPitchStart);
                                $("#update_pitchstart").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                        }
                        /*if(pitchleader != '') {
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
                        }*/
                        $("#update_pitchstage").jqxDropDownList({ source: stages }).val(pitchstage);
                        if(pitchstage != 'Current client') {
                                $("#update_pitchstage").jqxDropDownList('disableItem',"Current client");
                        }
                        if(!pitchstage.match(/Lost/g)) {
                                $("#update_pitchstage").jqxDropDownList('disableItem',"Lost - archive");
                        }
                        rules.push(validator.pitchstage);
                        if(clientsincemonth != '' && clientsincemonth != null) {
                                if(pitchstage.match(/Live/g)) {
                                        $("#divClientMonth").html('');
                                        var inpClientMonth = $("<div id=\"update_clientsincemonth\"></div>");
                                        $("#divClientMonth").append(inpClientMonth);
                                        $("#update_clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  }).val(currMonth);
                                        //rules.push(validator.clientsincemonth);
                                } else {
                                        //$("#divClientMonth").text(clientsincemonth);
                                        $("#divClientMonth").html('');
                                        var inpClientMonth = $("<div id=\"update_clientsincemonth\"></div>");
                                        $("#divClientMonth").append(inpClientMonth);
                                        $("#update_clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  }).val((arrMonths.indexOf(clientsincemonth)+1));
                                }
                        } else {
                                if(pitchstage.match(/Lost/g)) {
                                        $("#divClientMonth").text('');
                                } else {
                                        $("#divClientMonth").html('');
                                        var inpClientMonth = $("<div id=\"update_clientsincemonth\"></div>");
                                        $("#divClientMonth").append(inpClientMonth);
                                        $("#update_clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  }).val(currMonth);
                                        //rules.push(validator.clientsincemonth);
                                }
                        }
                        if(clientsinceyear != 0 && clientsinceyear != null) {
                                if(pitchstage.match(/Live/g)) {
                                        $("#divClientYear").html('');
                                        var inpClientYear = $("<div id=\"update_clientsinceyear\"></div>");
                                        $("#divClientYear").append(inpClientYear);
                                        $("#update_clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  }).val(currYear);
                                        rules.push(validator.clientsinceyear);
                                } else {
                                        //$("#divClientYear").text(clientsinceyear);
                                        $("#divClientYear").html('');
                                        var inpClientYear = $("<div id=\"update_clientsinceyear\"></div>");
                                        $("#divClientYear").append(inpClientYear);
                                        $("#update_clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  }).val(clientsinceyear);
                                        rules.push(validator.clientsinceyear);
                                }
                        } else {
                                if(pitchstage.match(/Lost/g)) {
                                        $("#divClientYear").text('');
                                } else {
                                        $("#divClientYear").html('');
                                        var inpClientYear = $("<div id=\"update_clientsinceyear\"></div>");
                                        $("#divClientYear").append(inpClientYear);
                                        $("#update_clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  }).val(currYear);
                                        rules.push(validator.clientsinceyear);
                                }
                        }
                        if(pitchstage.match(/Won/g) || pitchstage == 'Current client') {
                                $("#divLostDate").text('No');
                        } else {
                                $("#divLostDate").html('');
                                var inpLostDate = $("<div id=\"update_lostdate\"></div>");
                                $("#divLostDate").append(inpLostDate);
                                $("#update_lostdate").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                                if(lostdate != '') {
                                        var lostDate = new Date(lostdate);
                                } else {
                                        var lostDate = new Date();
                                }
                                $("#update_lostdate").val(lostDate);
                                rules.push(validator.lostdate);
                        }
                        if(service != null) {
                                //$("#divService").text(service);
                                $("#divService").html('');
                                var inpService = $("<div id=\"update_service\"></div>");
                                $("#divService").append(inpService);
                                $("#update_service").jqxDropDownList({ source: services }).val(service);
                                rules.push(validator.service);
                        } else {
                                $("#divService").html('');
                                var inpService = $("<div id=\"update_service\"></div>");
                                $("#divService").append(inpService);
                                $("#update_service").jqxDropDownList({ source: services }).val(service);
                        }
                        if(division != null) {
                                $("#divDivision").html('');
                                var inpDivision = $("<div id=\"update_division\"></div>");
                                $("#divDivision").append(inpDivision);
                                $("#update_division").jqxDropDownList({ source: divisions }).val(division);
                                rules.push(validator.division);
                        } else {
                                $("#divDivision").html('');
                                var inpDivision = $("<div id=\"update_division\"></div>");
                                $("#divDivision").append(inpDivision);
                                $("#update_division").jqxDropDownList({ source: divisions }).val(division);
                                rules.push(validator.division);
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
                                //$("#divCurrency").text(currency);
                                $("#divCurrency").html('');
                                var inpCurrency = $("<div id=\"update_currency\"></div>");
                                $("#divCurrency").append(inpCurrency);
                                $("#update_currency").jqxDropDownList({ source: currencies }).val(currency);
                                rules.push(validator.currency);
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
                                if(pitchstage.match(/Won/g) || pitchstage == 'Current client') {
                                        rules.push(validator.estrevenueRequired);
                                        rules.push(validator.estrevenueNumeric);
                                }
                                $("#divActualRevenue").html('');
                                var inpActualRevenue = $("<input type=\"text\" id=\"update_actualrevenue\" />");
                                $("#divActualRevenue").append(inpActualRevenue);
                                $("#update_actualrevenue").jqxInput({ height: 25, width: 150, rtl:true }).val(actualrevenue);
                                /*if(pitchstage == 'Current client') {
                                        rules.push(validator.actualrevenueRequired);
                                        rules.push(validator.actualrevenueNumeric);
                                }*/
                        }
                        $("#update_notes").jqxInput({ height: 25, width: 200 }).val(comments);
                        // show the popup window.
                        $("#updateWindow").jqxWindow('open');
                        $("#update_region").bind('select', function (event) {
                            var args = event.args;
                            var item = $('#update_region').jqxDropDownList('getItem', args.index);
                            if(item != null) {
                                arrRegionCountries = markets[item.label];
                                $("#update_nameofentity").jqxDropDownList({ source: arrRegionCountries, checkboxes: false, selectedIndex: -1 });
                            }
                        });
                        $("#update_nameofentity").bind('select', function (event) {
                            var args = event.args;
                            var item = $('#update_nameofentity').jqxDropDownList('getItem', args.index);
                            if(item != null) {
                                /*if(item.label == "Global") {
                                        $("#update_city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                                } else if(item.label.match(/Regional/g)) {
                                        $("#update_city").jqxDropDownList({ source: [item.label], checkboxes: false, selectedIndex: 0 });
                                } else {*/
                                        arrCities = cities[item.label];
                                        $("#update_city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                                /*}*/
                            }
                        });
                        $("#update_pitchstage").bind('select', function (event) {
                                var args = event.args;
                                var item = $('#update_pitchstage').jqxDropDownList('getItem', args.index);
                                if(item != null) {
                                        if(item.label.match(/Won/g) || item.label == "Current client") {
                                                $("#trUpdateClientSinceMonth").show();
                                                $("#trUpdateClientSinceYear").show();
                                                $("#trUpdatePitchedDate").show();

                                                $("#trUpdateLostSince").hide();
                                        }
                                        else if(item.label.match(/Live/g)) {
                                                $("#trUpdatePitchedDate").show();

                                                $("#trUpdateClientSinceMonth").hide();
                                                $("#trUpdateClientSinceYear").hide();
                                                $("#trUpdateLostSince").hide();
                                        }
                                        else if(item.label.match(/Lost/g)) {
                                                $("#trUpdateClientSinceMonth").show();
                                                $("#trUpdateClientSinceYear").show();
                                                $("#trUpdateLostSince").show();
                                                $("#trUpdatePitchedDate").show();
                                        }
                                        else if(item.label == 'Cancelled') {
                                                $("#trUpdateLostSince").show();
                                                $("#trUpdatePitchedDate").show();

                                                $("#trUpdateClientSinceMonth").hide();
                                                $("#trUpdateClientSinceYear").hide();
                                        }
                                }
                        });
                        if(pitchstage.match(/Won/g) || pitchstage == "Current client") {
                                $("#trUpdateClientSinceMonth").show();
                                $("#trUpdateClientSinceYear").show();

                                $("#trUpdateLostSince").hide();
                        }
                        else if(pitchstage.match(/Live/g)) {
                                $("#trUpdateClientSinceMonth").hide();
                                $("#trUpdateClientSinceYear").hide();
                                $("#trUpdateLostSince").hide();
                        }
                        else if(pitchstage.match(/Lost/g)) {
                                $("#trUpdateClientSinceMonth").show();
                                $("#trUpdateClientSinceYear").show();
                                $("#trUpdateLostSince").show();
                        }
                        else if(pitchstage == 'Cancelled') {
                                $("#trUpdateLostSince").show();

                                $("#trUpdateClientSinceMonth").hide();
                                $("#trUpdateClientSinceYear").hide();
                        }
                        $('#updateForm').jqxValidator({ position: 'right', rules: rules});
                }
                duplicateClick = function(event) {
                        var target = $(event.target);
                        // get button's value.
                        var value = target.val();
                        // get clicked row.
                        var rowIndex = parseInt(event.target.getAttribute('data-row'));
                        if (isNaN(rowIndex)) {
                            return;
                        }
                        // begin duplicate.
                        var data = $('#jqxgrid').jqxGrid('getrowdata', rowIndex);
                        var recordid = data.RecordId;
                        var parentrecordid = data.ParentId;
                        var clientname = data.ClientName;
                        var parentcompany = data.ParentCompany;
                        var region = data.Region;
                        var country = data.Country;
                        var city = data.City;
                        var leadagency = data.LeadAgency;
                        var clientcategory = data.ClientCategory;
                        var pitchstart = data.PitchStart;
                        /*var pitchleader = data.PitchLeader;*/
                        var pitchstage = data.PitchStage;
                        var clientsincemonth = data.ClientMonth;
                        var clientsinceyear = data.ClientYear;
                        var lostdate = data.Lost;
                        var division = data.Division;
                        var activemarkets = data.ActiveMarkets;
                        var currency = data.Currency;
                        var comments = data.Comments;

                        var offset = $("#jqxgrid").offset();
                        $("#popupWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "750px", maxWidth: 700, isModal: true });
                        if(parentrecordid == 0 || parentrecordid == null || parentrecordid == '') {
                                $("#parentrecordid").val(recordid);
                        } else {
                                $("#parentrecordid").val(parentrecordid);
                        }
                        $("#region").jqxDropDownList({ source: regions }).val(region);
                        arrRegionCountries = markets[region];
                        $("#nameofentity").jqxDropDownList({ source: arrRegionCountries }).val(country);
                        if(country == "Global") {
                                $("#city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                        } else if(country.match(/Regional/g)) {
                                $("#city").jqxDropDownList({ source: [country], checkboxes: false, selectedIndex: 0 });
                        } else {
                                arrCities = cities[country];
                                $("#city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                        }
                        $("#city").val(city);
                        $("#agency").jqxDropDownList({ source: agencies }).val(leadagency);
                        $("#advertisername").jqxInput({ height: 25, width: 200 }).val(clientname);
                        $("#parentcompany").jqxInput({ height: 25, width: 175 }).val(parentcompany);
                        $("#category").jqxDropDownList({ source: categories }).val(clientcategory);
                        $("#pitchstart").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                        $("#pitchstage").jqxDropDownList({ source: stages }).val(pitchstage);
                        if(pitchstage != 'Current client') {
                                $("#update_pitchstage").jqxDropDownList('disableItem',"Current client");
                        }
                        if(!pitchstage.match(/Lost/g)) {
                                $("#update_pitchstage").jqxDropDownList('disableItem',"Lost - archive");
                        }
                        $("#clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  }).val((arrMonths.indexOf(clientsincemonth)+1));
                        $("#clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  }).val(clientsinceyear);
                        $("#lostdate").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                        var lostDate = new Date(lostdate);
                        $("#lostdate").val(lostDate);
                        $("#service").jqxDropDownList({ source: services, selectedIndex: -1 });
                        $("#division").jqxDropDownList({ source: divisions, selectedIndex: -1 }).val(division);
                        $("#activemarket").jqxDropDownList({ source: countries, checkboxes: true });
                        var entities = activemarkets.split(',');
                        for(key in entities) {
                                if(arrCountries.indexOf(entities[key]) != -1) {
                                       index = arrCountries.indexOf(entities[key]);
                                       $("#activemarket").jqxDropDownList('checkIndex', index);
                                }
                        }
                        $("#currency").jqxDropDownList({ source: currencies }).val(currency);
                        $("#estrevenue").jqxInput({ height: 25, width: 100, rtl:true }).val('');
                        $("#notes").jqxInput({ height: 25, width: 200 }).val(comments);
                        // show the popup window.
                        $("#popupWindow").jqxWindow('open');
                        $("#region").bind('select', function (event) {
                            var args = event.args;
                            var item = $('#region').jqxDropDownList('getItem', args.index);
                            if(item != null) {
                                arrRegionCountries = markets[item.label];
                                $("#nameofentity").jqxDropDownList({ source: arrRegionCountries, checkboxes: false, selectedIndex: -1 });
                            }
                        });
                        $("#nameofentity").bind('select', function (event) {
                            var args = event.args;
                            var item = $('#nameofentity').jqxDropDownList('getItem', args.index);
                            if(item != null) {
                                /*if(item.label == "Global") {
                                        $("#city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                                } else if(item.label.match(/Regional/g)) {
                                        $("#city").jqxDropDownList({ source: [item.label], checkboxes: false, selectedIndex: 0 });
                                } else {*/
                                        arrCities = cities[item.label];
                                        $("#city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                                /*}*/
                            }
                        });
                        $("#pitchstage").bind('select', function (event) {
                                var args = event.args;
                                var item = $('#pitchstage').jqxDropDownList('getItem', args.index);
                                if(item != null) {
                                        if(item.label.match(/Won/g) || item.label == "Current client") {
                                                $("#trClientSinceMonth").show();
                                                $("#trClientSinceYear").show();
                                                $("#trPitchedDate").show();

                                                $("#trLostSince").hide();
                                        }
                                        else if(item.label.match(/Live/g)) {
                                                $("#trPitchedDate").show();

                                                $("#trClientSinceMonth").hide();
                                                $("#trClientSinceYear").hide();
                                                $("#trLostSince").hide();
                                        }
                                        else if(item.label.match(/Lost/g)) {
                                                $("#trClientSinceMonth").show();
                                                $("#trClientSinceYear").show();
                                                $("#trLostSince").show();
                                                $("#trPitchedDate").show();
                                        }
                                        else if(item.label == 'Cancelled') {
                                                $("#trLostSince").show();
                                                $("#trPitchedDate").show();

                                                $("#trClientSinceMonth").hide();
                                                $("#trClientSinceYear").hide();
                                        }
                                }
                        });
                        if(pitchstage.match(/Won/g) || pitchstage == "Current client") {
                                $("#trClientSinceMonth").show();
                                $("#trClientSinceYear").show();

                                $("#trLostSince").hide();
                        }
                        else if(pitchstage.match(/Live/g)) {
                                $("#trClientSinceMonth").hide();
                                $("#trClientSinceYear").hide();
                                $("#trLostSince").hide();
                        }
                        else if(pitchstage.match(/Lost/g)) {
                                $("#trClientSinceMonth").show();
                                $("#trClientSinceYear").show();
                                $("#trLostSince").show();
                        }
                        else if(pitchstage == 'Cancelled') {
                                $("#trLostSince").show();

                                $("#trClientSinceMonth").hide();
                                $("#trClientSinceYear").hide();
                        }
                }
                deleteClick = function (event) {
                    var target = $(event.target);
                    // get button's value.
                    var value = target.val();
                    // get clicked row.
                    var rowIndex = parseInt(event.target.getAttribute('data-row'));
                    if (isNaN(rowIndex)) {
                        return;
                    }
                    var recordid = $("#jqxgrid").jqxGrid('getCellValue', rowIndex, 'RecordId');
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
                                    $("#jqxgrid").jqxGrid('updateBoundData');
                                } else {
                                    alert(result.errors);
                                    return false;
                                }
                            }
                        });
                    }
                }
                /*applyFilters = function (filterGroups) {
                    for (var i = 0; i < filterGroups.length; i++) {
                        var filterGroup = filterGroups[i];
                        var filters = filterGroup.filter.getfilters();
                        for (var j = 0; j < filters.length; j++) {
                            var filtergroup = new $.jqx.filter();
                            var filter_or_operator = filters[j].operator;
                            var filtervalue = filters[j].value;
                            var filtercondition = filters[j].condition;
                            if(filterGroup.filtercolumn == 'PitchStart' || filterGroup.filtercolumn == 'Lost') {
                                var filter = filtergroup.createfilter('datefilter', filtervalue, filtercondition);
                            } else {
                                var filter = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
                            }
                            filtergroup.addfilter(filter_or_operator, filter);
                        }
                        $("#jqxgrid").jqxGrid('addfilter', filterGroup.filtercolumn, filtergroup);
                    }
                    $("#jqxgrid").jqxGrid('applyfilters');
                }*/

            $("#popupWindow").jqxWindow({
                width: 600, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelNew"), maxWidth: 700, maxHeight: 750, showCloseButton: false 
            });
            $("#updateWindow").jqxWindow({
                width: 600, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelUpdate"), maxWidth: 700, maxHeight: 750, showCloseButton: false 
            });
            $("#createNew").jqxButton({ theme: theme });
            $("#createNew").click(function () {
                var offset = $("#jqxgrid").offset();
                $("#popupWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "750px", maxWidth: 700, isModal: true });
                $("#parentrecordid").val('');
                $("#region").jqxDropDownList({ source: regions, selectedIndex: -1 });
                $("#nameofentity").jqxDropDownList();
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
                /*$("#pitchleader").jqxInput({ height: 25, width: 200 }).val('');*/
                $("#pitchstage").jqxDropDownList({ source: stages, selectedIndex: -1 });
                $("#clientsincemonth").jqxDropDownList({ source: monthsAdapter, displayMember: 'label', valueMember: 'value', selectedIndex: -1  }).val(currMonth);
                $("#clientsinceyear").jqxDropDownList({ source: years, selectedIndex: -1  }).val(currYear);
                $("#lostdate").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                $("#service").jqxDropDownList({ source: services, selectedIndex: -1 });
                $("#division").jqxDropDownList({ source: divisions, selectedIndex: -1 });
                $("#activemarket").jqxDropDownList({ source: countries, checkboxes: true, selectedIndex: -1 });
                $("#currency").jqxDropDownList({ source: currencies, selectedIndex: -1 });
                $("#estrevenue").jqxInput({ height: 25, width: 100, rtl:true }).val('');
                $("#notes").jqxInput({ height: 25, width: 200 }).val('');
                // show the popup window.
                $("#popupWindow").jqxWindow('open');
                $("#region").bind('select', function (event) {
                    var args = event.args;
                    var item = $('#region').jqxDropDownList('getItem', args.index);
                    if(item != null) {
                        arrRegionCountries = markets[item.label];
                        $("#nameofentity").jqxDropDownList({ source: arrRegionCountries, checkboxes: false, selectedIndex: -1 });
                    }
                });
                $("#nameofentity").bind('select', function (event) {
                    var args = event.args;
                    var item = $('#nameofentity').jqxDropDownList('getItem', args.index);
                    if(item != null) {
                        /*if(item.label == "Global") {
                                $("#city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                        } else if(item.label.match(/Regional/g)) {
                                $("#city").jqxDropDownList({ source: [item.label], checkboxes: false, selectedIndex: 0 });
                        } else {*/
                                arrCities = cities[item.label];
                                $("#city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                        /*}*/
                    }
                });
                $("#pitchstage").bind('select', function (event) {
                        var args = event.args;
                        var item = $('#pitchstage').jqxDropDownList('getItem', args.index);
                        if(item != null) {
                                if(item.label.match(/Won/g) || item.label == "Current client") {
                                        $("#trClientSinceMonth").show();
                                        $("#trClientSinceYear").show();
                                        $("#trPitchedDate").show();
                                        
                                        $("#trLostSince").hide();
                                }
                                else if(item.label.match(/Live/g)) {
                                        $("#trPitchedDate").show();
                                        
                                        $("#trClientSinceMonth").hide();
                                        $("#trClientSinceYear").hide();
                                        $("#trLostSince").hide();
                                }
                                else if(item.label.match(/Lost/g)) {
                                        $("#trClientSinceMonth").show();
                                        $("#trClientSinceYear").show();
                                        $("#trLostSince").show();
                                        $("#trPitchedDate").show();
                                }
                                else if(item.label == 'Cancelled') {
                                        $("#trLostSince").show();
                                        $("#trPitchedDate").show();
                                        
                                        $("#trClientSinceMonth").hide();
                                        $("#trClientSinceYear").hide();
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
                        /*{ input: '#pitchleader', message: 'Pitch leader is required!', action: 'keyup, blur', rule: 'required' },*/
                        { input: '#pitchstage', message: 'Stage is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#clientsinceyear', message: 'Client Since Year is required!', action: 'change', rule: function (input) {
                                var pitchstage = $('#pitchstage').val();
                                if ((pitchstage.match(/Won/g) || pitchstage == 'Current client') && input.val() == '') {
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
                        { input: '#division', message: 'Division is required!', action: 'change', rule: function (input) {
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
                    PitchStart: $("#pitchstart").val(), /*PitchLeader: $("#pitchleader").val(),*/ PitchStage: $("#pitchstage").val(),
                    ClientSinceMonth: $("#clientsincemonth").val(), ClientSinceYear: $("#clientsinceyear").val(), LostDate: $("#lostdate").val(),
                    Service: $("#service").val(), Division: $("#division").val(), ActiveMarkets: $("#activemarket").val(), Currency: $("#currency").val(),
                    EstimatedRevenue: $("#estrevenue").val(), Comments: $("#notes").val(), parentId: $('#parentrecordid').val()
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
                            $("#jqxgrid").jqxGrid('updateBoundData');
                            $("#popupWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });

            });

            var validator = {

                agency : {
                        input: '#update_agency', message: 'Lead agency is required!', action: 'change', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        } 
                },
                category: {
                        input: '#update_category', message: 'Category is required!', action: 'change', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        } 
                },
                service : {
                        input: '#update_service', message: 'Service is required!', action: 'change', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        } 
                },
                division : {
                        input: '#update_division', message: 'Division is required!', action: 'change', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        }
                },
                pitchstage : {
                        input: '#update_pitchstage', message: 'Stage is required!', action: 'change', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        }
                },
                /*pitchleader : {
                        input: '#update_pitchleader', message: 'Pitch Leader is required!', action: 'blur', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        } 
                },*/
                clientsincemonth: { input: "#update_clientsincemonth", message: 'Client Since Month is required!', action: 'change', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if ((pitchstage.match(/Live/g) || pitchstage.match(/Won/g) || pitchstage == 'Current client') && input.val() == '') {
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
                        if ((pitchstage.match(/Won/g) || pitchstage == 'Current client') && input.val() == '') {
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
                currency: {
                        input: '#update_currency', message: 'Currency is required!', action: 'change', rule: function (input) {
                            if (input.val() != '') {
                                    return true;
                            }
                            return false;
                        } 
                },
                estrevenueRequired: { input: '#update_estrevenue', message: 'iP estimated revenue is required!', action: 'keyup, blur', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if ((pitchstage.match(/Won/g) || pitchstage == 'Current client') && input.val() == '') {
                                return false;
                        }
                        return true;
                    }
                },
                estrevenueNumeric: { input: '#update_estrevenue', message: 'iP estimated revenue should be numeric!', action: 'keyup, blur', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if ((pitchstage.match(/Won/g) || pitchstage == 'Current client')) {
                                if (!isNaN(parseFloat(input.val())) && isFinite(input.val())) {
                                        return true;
                                }
                        } else {
                                return true;
                        }
                        return false;
                    } 
                },
                actualrevenueRequired: { input: '#update_actualrevenue', message: 'iP Actual revenue is required!', action: 'keyup, blur', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if ((pitchstage.match(/Won/g) || pitchstage == 'Current client') && input.val() == '') {
                                return false;
                        }
                        return true;
                    }
                },
                actualrevenueNumeric: { input: '#update_actualrevenue', message: 'iP Actual revenue should be numeric!', action: 'keyup, blur', rule: function (input) {
                        if($('#update_pitchstage').val()) {
                                var pitchstage = $('#update_pitchstage').val();
                        } else {
                                var pitchstage = $('#update_pitchstage').text();
                        }
                        if ((pitchstage.match(/Won/g) || pitchstage == 'Current client')) {
                                if (!isNaN(parseFloat(input.val())) && isFinite(input.val())) {
                                        return true;
                                }
                        } else {
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
                //var filterGroups = $('#jqxgrid').jqxGrid('getfilterinformation');

                var recordid = $("#recordid").val();
                var parentrecordid = $("#update_parentrecordid").val();
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
                /*if($('#update_pitchleader').val()) {
                        var pitchleader = $('#update_pitchleader').val();
                } else {
                        var pitchleader = $('#divPitchLeader').text();
                }*/
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
                if($('#update_division').val()) {
                        var division = $('#update_division').val();
                } else {
                        var division = $('#divDivision').text();
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
                    PitchStart: pitchstart, /*PitchLeader: pitchleader,*/ PitchStage: pitchstage,
                    ClientSinceMonth: clientsincemonth, ClientSinceYear: clientsinceyear, LostDate: lostdate,
                    Service: service, Division: division, ActiveMarkets: activemarkets, Currency: currency,
                    EstimatedRevenue: estimatedrevenue, ActualRevenue: actualrevenue, Comments: comments, ParentId: parentrecordid
                };
                
                var updateRow = { RecordId: recordid, ClientName: clientname, ParentCompany: parentcompany, Region: region,
                    Country: country, City: city, LeadAgency: leadagency, ClientCategory: clientcategory,
                    PitchStart: pitchstart, PitchStage: pitchstage,
                    ClientMonth: arrMonths[(parseInt(clientsincemonth)-1)], ClientYear: clientsinceyear, LostDate: lostdate,
                    Service: service, Division: division, ActiveMarkets: activemarkets, Currency: currency,
                    EstimatedRevenue: estimatedrevenue, ActualRevenue: actualrevenue, Comments: comments, ParentId: parentrecordid
                };

                $.ajax({
                    type: "POST",
                    url: "/reports/update_client_record/",
                    data: JSON.stringify(row),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#jqxgrid").jqxGrid('updateBoundData');
                            /*$("#jqxgrid").on("bindingcomplete", function (event) {
                                    applyFilters(filterGroups);
                            });*/
                            //$('#jqxgrid').jqxGrid('updaterow', recordid, updateRow);
                            $("#updateWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });

            });
            
            /*$("#addassociation").click(function () {
                
            });*/
        });
    </script>
    <div id="tab-menu" align="left">
            <div id="-reports-client-report" class="light-grey">
                    <a href="/reports/client_report">SEARCH</a>
            </div>
            <div id="-reports-client-data" class="light-grey selected">
                    <a href="/reports/client_data">CREATE/UPDATE YOUR RECORDS</a>
            </div>
    </div>
<script type="text/javascript">
        $(document).ready(function() {
                $('#tab-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
                $('#nav-menu div#-reports-client_report').addClass('selected');
        });
</script>    
<div id='jqxWidget'>
        <!--<div style="margin-right: 7px; margin-bottom: 5px" align="right">
            <button style="margin-left: 5px" value="Manage Association" id="addassociation">MANAGE ASSOCIATIONS</button>
        </div>-->
                    
        <div id="jqxgrid"></div>
        <div style='margin-top: 20px;'></div>
        <div style='float: right; padding-right: 7px; padding-bottom: 30px'>
            <button value="Add a new record" id='createNew'>ADD A NEW RECORD</button>
        </div>
    
    <div id="popupWindow">
        <div>Add a new record</div>
        <div style="overflow: hidden;">
        <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelNew" value="Cancel">CANCEL</button></div>
        <form id="testForm" action="./">
            <table>
                <tr>
                    <td align="right" style="width: 200px;">Region</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="region"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Country</td>
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
                    <td style="width: 150px"><input type="hidden" id="parentrecordid"/></td>
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
                    <td align="right">Status</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="pitchstage"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Service</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="service"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Division</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="division"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr id="trClientSinceMonth" style="display: none">
                    <td align="right">Client Since Month</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="clientsincemonth"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr id="trClientSinceYear" style="display: none">
                    <td align="right">Client Since Year</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="clientsinceyear"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr id="trLostSince" style="display: none">
                    <td align="right">Lost Since (M-Y)</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="lostdate"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Pitched (M-Y)</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="pitchstart"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <!--<tr>
                    <td align="right">Pitch Leader</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="pitchleader"/></td>
                    <td style="width: 150px"></td>
                </tr>-->
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
        <div style="padding-top: 10px;" align="right"><button style="margin-right: 15px;" id="SaveNew" value="Add to existing records">ADD TO EXISTING RECORDS</button></div>
        </div>
   </div>

    <div id="updateWindow">
        <div>Edit record</div>
        <div style="overflow: hidden;">
        <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelUpdate" value="Cancel">CANCEL</button></div>
        <form id="updateForm" action="./">
            <table>
                <tr>
                    <td align="right" style="width: 200px; padding-bottom: 5px; padding-right: 5px">Region</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divRegion"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Country</td>
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
                            <input type="hidden" id="update_parentrecordid"/>
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
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Status</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="update_pitchstage"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Service</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divService"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Division</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divDivision"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr id="trUpdateClientSinceMonth" style="display: none">
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Client since M</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divClientMonth"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr id="trUpdateClientSinceYear" style="display: none">
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Client since Y</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divClientYear"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr id="trUpdateLostSince" style="display: none">
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Lost Since (M-Y)</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divLostDate"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Pitched (M-Y)</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divPitchStart"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <!--<tr>
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">Pitch Leader</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="divPitchLeader"/></td>
                    <td style="width: 150px"></td>
                </tr>-->
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
                    <td align="right" style="padding-bottom: 5px; padding-right: 5px">iP <?php echo (date('Y')-1); ?> actual revenue</td>
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
        <div style="padding-top: 20px;" align="right"><button style="margin-right: 15px;" id="UpdateClient">UPDATE EXISTING RECORD</button></div>
        </div>
   </div>
</div>
