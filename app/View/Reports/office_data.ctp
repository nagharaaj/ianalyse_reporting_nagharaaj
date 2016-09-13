    <script type="text/javascript">
         var editClick;
         $(document).ready(function () {

             var countries = jQuery.parseJSON('<?php echo $countries; ?>');
             var arrCountries = $.map(countries, function(el) { return el; });
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });
             var languages = jQuery.parseJSON('<?php echo $languages; ?>');
             var arrLanguages = $.map(languages, function(el) { return el; });
             var services = jQuery.parseJSON('<?php echo $json_services; ?>');
             var arrServices = $.map(services, function(el) { return el; });
             var userRole = '<?php echo $userRole;?>';
             var userMarkets = jQuery.parseJSON('<?php echo $userMarkets;?>');
             var arrUserMarkets = $.map(userMarkets, function(el) { return el; });
             var widthPreferences_office_data = jQuery.parseJSON('<?php echo $widthPreferences_office_data; ?>');
             var defaultState;
             var theme = 'base';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }
             
             var horizontalScroll=function(){
                 var mousewheel = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel" //FF doesn't recognize mousewheel as of FF3.x
                 $("#jqxScrollWraphorizontalScrollBarjqxgrid").bind(mousewheel, function(e){
                        e.preventDefault();
                        var position = $('#jqxgrid').jqxGrid('scrollposition');
                        var left = position.left;
                        var top = position.top;
                        var evt = window.event || e //equalize event object
                        evt = evt.originalEvent ? evt.originalEvent : evt; //convert to originalEvent
                        var delta = evt.detail ? evt.detail*(-40) : evt.wheelDelta //check for it is used by Opera and FF
                        if(delta > 0) {
                                top=top+25;
                                left=left-25;
                                $('#jqxgrid').jqxGrid('scrolloffset',top,left);
                        }
                         else{
                                top=top-25;
                                left=left+25;
                                $('#jqxgrid').jqxGrid('scrolloffset', top,left);
                        }
                });
           }
           
             var source =
             {
                dataType: "json",
                id: 'id',
                url: "/reports/get_office_data/",
                data: { mode: 'edit' },
                datafields: [
                    { name: 'RecordId', type: 'number' },
                    { name: 'Region', type: 'string' },
                    { name: 'Country', type: 'string' },
                    { name: 'City', type: 'string' },
                    { name: 'YearEstablished', type: 'number' },
                    { name: 'TotalEmployee', type: 'number' },
                    { name: 'MarketsCovered',type:'string'},
                    { name: 'Executive', type: 'string' },
                    { name: 'BusinessHead', type: 'string' },
                    { name: 'Affiliates', type: 'string' },
                    { name: 'Content', type: 'string' },
                    { name: 'Data', type: 'string' },
                    { name: 'Display', type: 'string' },
                    { name: 'SEO', type: 'string' },
                    { name: 'Search', type: 'string' },
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
             var cellclass = function (row, column, value, data) {
                if (value == '' || value == null) {
                    return "empty-cell";
                }
             }

             var dataAdapter = new $.jqx.dataAdapter(source);
             var listInput;
             var buildFilterPanel = function (filterPanel, datafield) {
                listInput = $("<div id='languageList'></div>");
                var filterheader = $('<div style="margin-top: 3px; margin-bottom: 3px;" class="filter">Show rows where:</div>');
                var applyinput = $("<div class='filter' style='height: 25px; margin-left: 20px; margin-top: 7px;'></div>");
                var filterbutton = $('<span tabindex="0" style="padding: 4px 12px; margin-left: 2px;">Filter</span>');
                applyinput.append(filterbutton);
                var filterclearbutton = $('<span tabindex="0" style="padding: 4px 12px; margin-left: 5px;">Clear</span>');
                applyinput.append(filterclearbutton);
                filterPanel.append(filterheader);
                filterPanel.append(listInput);
                filterPanel.append(applyinput);
                filterbutton.jqxButton({ theme: theme, height: 20 });
                filterclearbutton.jqxButton({ theme: theme, height: 20 });
                listInput.jqxListBox({ theme: theme, width: 170, source: languages, checkboxes: true, height: 125 });
                listInput.jqxListBox('insertAt', { label: '(Select All)', value: 'all' }, 0 );
                listInput.jqxListBox('checkAll');
                var handleCheckChange = true;
                listInput.on('checkChange', function (event) {
                    if (!handleCheckChange)
                        return;

                    if (event.args.label != '(Select All)') {
                        handleCheckChange = false;
                        listInput.jqxListBox('checkIndex', 0);
                        var checkedItems = listInput.jqxListBox('getCheckedItems');
                        var items = listInput.jqxListBox('getItems');

                        if (checkedItems.length == 1) {
                            listInput.jqxListBox('uncheckIndex', 0);
                        }
                        else if (items.length != checkedItems.length) {
                            listInput.jqxListBox('indeterminateIndex', 0);
                        }
                        handleCheckChange = true;
                    }
                    else {
                        handleCheckChange = false;
                        if (event.args.checked) {
                            listInput.jqxListBox('checkAll');
                        }
                        else {
                            listInput.jqxListBox('uncheckAll');
                        }
                        handleCheckChange = true;
                    }
                });
                filterbutton.click(function () {
                    var filtergroup = new $.jqx.filter();
                    var filter_or_operator = 1;
                    //var filtervalue = textInput.val();
                    var arrSelectedLang = listInput.jqxListBox('getCheckedItems');
                    var filtervalue = null;
                    var filtercondition = 'contains';
                    $.each(arrSelectedLang, function (index) {
                        filtervalue = this.label;
                        var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);            
                        filtergroup.addfilter(filter_or_operator, filter1);
                    });
                    // add the filters.
                    $("#jqxgrid").jqxGrid('addfilter', datafield, filtergroup);
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
                    $("#jqxgrid").jqxGrid('removefilter', datafield);
                    // apply the filters.
                    $("#jqxgrid").jqxGrid('applyfilters');
                    $("#jqxgrid").jqxGrid('closemenu');
                    listInput.jqxListBox('checkAll');
                });
                filterclearbutton.keydown(function (event) {
                    if (event.keyCode === 13) {
                        filterclearbutton.trigger('click');
                    }
                    listInput.jqxListBox('checkAll');
                });
             }
             // initialize jqxGrid
             $("#jqxgrid").jqxGrid(
             {
                width: (parseInt(screen.availWidth) - 30),
                autoheight:false,
                height:600,
                enablemousewheel: true,
                source: dataAdapter,
                pageable: true,
                pagerMode: 'simple',
                sortable: true,
                filterable: true,
                editable: false,
                autorowheight:true,
                selectionmode: 'none',
                columnsresize: true,
                columnsheight: 50,
                horizontalscrollbarstep: 50,
                horizontalscrollbarlargestep: 200,
                showpinnedcolumnbackground: false,
                enablebrowserselection: true,
                enablehover: false,
                columnmenuopening: function (menu, datafield, height) {
                    var column = $("#jqxgrid").jqxGrid('getcolumn', datafield);
                    if (column.filtertype === "custom") {
                        menu.height(265);
                    }
                    else menu.height(height);
                },
                 ready:function()
                 {
                       defaultState = $("#jqxgrid").jqxGrid('savestate');
                       var dataRows = $('#jqxgrid').jqxGrid('getrows');
                       var rowscount = dataRows.length;
                       $("#jqxgrid").jqxGrid('pagesize',rowscount);
                       horizontalScroll();
                        var columns = widthPreferences_office_data.columns;
                        if(columns) {
                                $.each(columns, function(columnName, columnSettings) {
                                       $('#jqxgrid').jqxGrid('setcolumnproperty',columnName,'width',columnSettings.width);
                                });
                        }
                 },
                columns: [
                  {
                      text: '', cellsalign: 'center', pinned: true, columntype: 'custom', width: 150, editable: false, sortable: false, filterable: false, datafield: null, hidden: ((userRole == 'Viewer' || userRole == 'Country - Viewer') ? true : false),
                      cellsrenderer: function (row, column, value) {
                          // render custom column.
                          var showButton = true;
                          if(userRole == 'Viewer' || userRole == 'Country - Viewer') {
                                showButton = false;
                          } else {
                                if(userRole == 'Regional') {
                                        var regionName = $('#jqxgrid').jqxGrid('getcellvaluebyid', row, "Region");
                                        if(arrUserMarkets.indexOf(regionName) == -1) {
                                                showButton = false;
                                        }
                                } else if(userRole == 'Country') {
                                        var countryName = $('#jqxgrid').jqxGrid('getcellvaluebyid', row, "Country");
                                        if(arrUserMarkets.indexOf(countryName) == -1) {
                                                showButton = false;
                                        }
                                }
                          }
                          if(userRole == 'Global') {
                                return "<div align='center'><button style='margin-right: 5px;' data-row='" + row + "' class='deleteButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='deleteClick(event)'>DELETE</button><button data-row='" + row + "' class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='editClick(event)'>EDIT</button></div>";
                          } else {
                                if(showButton == true) {
                                      return "<div align='center'><button data-row='" + row + "' class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='editClick(event)'>EDIT</button></div>";
                                } else {
                                      return "<div align='center' style='display:none'><button data-row='" + row + "' class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='editClick(event)'>EDIT</button></div>";
                                }
                          }
                      }
                  },
                  { text: 'RecordId', datafield: 'RecordId', hidden: true },
                  { text: 'Region', datafield: 'Region', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', pinned: true },
                  { text: 'Market', datafield: 'Country', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', pinned: true },
                  { text: 'Location Name (City)', datafield: 'City', width: 130, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', pinned: true },
                  { text: 'Year established', columngroup: 'GeneralInfo', datafield: 'YearEstablished', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', cellsalign: 'right', align: 'center' },
                  { text: 'Total employee', columngroup: 'GeneralInfo', datafield: 'TotalEmployee', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Markets Covered', columngroup: 'GeneralInfo', datafield: 'MarketsCovered', width: 110, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', cellsalign: 'right', align: 'center' },
                  { text: 'Head of Office', datafield: 'Executive', width: 175, cellClassName: cellclass, align: 'center', filterable: false},
                  { text: 'Head of New Business', datafield: 'BusinessHead', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of PPC', datafield: 'Search', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of SEO', datafield: 'SEO', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of Display', datafield: 'Display', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of Affiliates', datafield: 'Affiliates', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of Content', datafield: 'Content', width: 175, cellClassName: cellclass, align: 'center', filterable: false},
                  { text: 'Head of Data & Insights', datafield: 'Data', width: 175, cellClassName: cellclass, align: 'center', filterable: false},
                ],
                
                columngroups: 
                [
                  { text: 'General information', align: 'center', name: 'GeneralInfo' }
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
                                $(this).parent().parent().css('line-height', $(this).parent().parent().parent().css('height'));
                        });
                    }
            });
            $('#jqxgrid').jqxGrid({ rendered: function(){
                    var paginginfo = $("#jqxgrid").jqxGrid('getpaginginformation');
                    if(paginginfo.pagescount <= 1) {
                        $('#pagerjqxgrid').hide();
                    } else {
                        $('#pagerjqxgrid').show();
                    }

                    if ($(".editButtons").length > 0) {
                        $( ".editButtons" ).each(function( i ) {
                                $(this).parent().parent().css('line-height', $(this).parent().parent().parent().css('height'));
                        });
                    }
                }
            }); 
            $("#jqxgrid").on("pagechanged", function (event) {
                    if ($(".editButtons").length > 0) {
                        $( ".editButtons" ).each(function( i ) {
                                $(this).parent().parent().css('line-height', $(this).parent().parent().parent().css('height'));
                        });
                    }
            });
            $("#jqxgrid").on("columnresized", function (event) {
                    if ($(".editButtons").length > 0) {
                        $( ".editButtons" ).each(function( i ) {
                                $(this).parent().parent().css('line-height', $(this).parent().parent().parent().css('height'));
                        });
                    }
                    var state=null;
                    state = $("#jqxgrid").jqxGrid('savestate');
                    var obj=[];
                    obj= {
                            state:state,
                            formname:'office_data'
                         };
                $.ajax({
                            type: "POST",
                            url: "/reports/user_grid_preferences/",
                            data: JSON.stringify(obj),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json"
                      });
            });
            editClick = function (event) {
                var target = $(event.target);
                // get button's value.
                var value = target.val();
                // get clicked row.
                var rowIndex = parseInt(event.target.getAttribute('data-row'));
                if (isNaN(rowIndex)) {
                    return;
                }
                var data = $('#jqxgrid').jqxGrid('getrowdata', rowIndex);
                openPopup(data);
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
                    var state = $("#jqxgrid").jqxGrid('savestate');
                    $.ajax({
                        type: "POST",
                        url: "/reports/delete_office_record/",
                        data: JSON.stringify(row),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success : function(result) {
                            if(result.success == true) {
                                alert("Data deleted successfully...");
                                $("#jqxgrid").jqxGrid('updateBoundData');
                                if (state) {
                                    $("#jqxgrid").jqxGrid('loadstate', state);
                                }
                            } else {
                                alert(result.errors);
                                return false;
                            }
                        }
                    });
                }
            }

            $("#popupWindow").jqxWindow({
                width: 1100, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelNew"), maxWidth: 1200, maxHeight: 750, showCloseButton: false 
            });
            if($(".createNew").is(':visible')) {
                $(".createNew").jqxButton({ theme: theme });
                $(".createNew").click(function () {
                    openPopup();
                });
            }
            function openPopup(rowData) {
                $("#popupWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "750px", maxWidth: 1200, isModal: true });
                if(rowData) {
                        $('#popupWindow').jqxWindow('setTitle', 'Edit location');
                        $("#SaveNew").html('UPDATE EXISTING LOCATION');
                } else {
                        $("#SaveNew").html('CREATE NEW LOCATION');
                }
                $("tr").remove(".more-contacts");
                $(".contact-row-count").val('0');

                var rules = [];

                $("#recordid").val((rowData ? rowData.RecordId : ''));

                $("#divRegion").html('');
                var inpRegion = $("<div id=\"region\"></div>");
                $("#divRegion").append(inpRegion);
                $("#region").jqxDropDownList({ source: regions }).val((rowData ? rowData.Region : ''));
                rules.push(validator.region);

                $("#divMarket").html('');
                var inpMarket = $("<div id=\"country\"></div>");
                $("#divMarket").append(inpMarket);
                $("#country").jqxDropDownList({ source: countries }).val((rowData ? rowData.Country : ''));
                rules.push(validator.country);

                $("#divCity").html('');
                var inpLocation = $("<input type=\"text\" id=\"city\" />");
                $("#divCity").append(inpLocation);
                $("#city").jqxInput({ height: 25, width: 175 }).val((rowData ? rowData.City : ''));
                rules.push(validator.city);

                $("#divYear").html('');
                var inpYearEstablished = $("<input type=\"text\" id=\"year_established\" />");
                $("#divYear").append(inpYearEstablished);
                $("#year_established").jqxInput({ height: 25, width: 100 }).val((rowData ? rowData.YearEstablished : ''));

                $("#divEmployeeCount").html('');
                var inpEmpCount = $("<input type=\"text\" id=\"employee_count\" />");
                $("#divEmployeeCount").append(inpEmpCount);
                $("#employee_count").jqxInput({ height: 25, width: 100 }).val((rowData ? rowData.TotalEmployee : ''));
                
                $("#divMarketsCovered").html('');
                var inpMarketsCovered = $("<div id=\"markets_covered\" />");
                $("#divMarketsCovered").append(inpMarketsCovered);
                if(rowData && rowData.MarketsCovered){
                        $("#markets_covered").jqxDropDownList('uncheckAll');
                        $("#markets_covered").jqxDropDownList({ source: countries, checkboxes: true });
                        var entities = rowData.MarketsCovered.split(',');
                                for(key in entities) {
                                        if(arrCountries.indexOf(entities[key]) != -1) {
                                               index = arrCountries.indexOf(entities[key]);
                                               $("#markets_covered").jqxDropDownList('checkIndex', index);
                                        }
                                }
               } else {
                        $("#markets_covered").jqxDropDownList({ source: countries, checkboxes :true }).val();
                      }
                      
                if(rowData && rowData.Executive) {
                        arrExecutive = rowData.Executive.split("<br/>-------------------------<br/>");
                        $.each(arrExecutive, function(index, value) {
                                if(index == 0) {
                                        var executiveDetails = value.split("<br/>");
                                        $("#executive_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val(executiveDetails[0]);
                                        $("#executive_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val((executiveDetails[1] != 'title' ? executiveDetails[1] : ''));
                                        $("#executive_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val((executiveDetails[2] != 'email' ? $(executiveDetails[2]).text() : ''));
                                } else {
                                        addContactRow('executive', 0, value);
                                }
                        });
                } else {
                        $("#executive_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#executive_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#executive_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                }
                if(rowData && rowData.BusinessHead) {
                        arrBusinessHead = rowData.BusinessHead.split("<br/>-------------------------<br/>");
                        $.each(arrBusinessHead, function(index, value) {
                                if(index == 0) {
                                        var businessHeadDetails = value.split("<br/>");
                                        $("#business_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val(businessHeadDetails[0]);
                                        $("#business_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val((businessHeadDetails[1] != 'title' ? businessHeadDetails[1] : ''));
                                        $("#business_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val((businessHeadDetails[2] != 'email' ? $(businessHeadDetails[2]).text() : ''));
                                } else {
                                        addContactRow('business', 0, value);
                                }
                        });
                }else{
                        $("#business_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#business_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#business_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                } 
                
                for (i = 0; i < arrServices.length; i++) {
                        if(rowData && rowData[arrServices[i]]) {
                                arrServiceContact = rowData[arrServices[i]].split("<br/>-------------------------<br/>");
                                $.each(arrServiceContact, function(index, value) {
                                        if(index == 0) {
                                                var ServiceContactDetails = value.split("<br/>");
                                                $("#" + arrServices[i] + "_contact_name_0").jqxInput({ height: 25, width: 310 }).val(ServiceContactDetails[0]);
                                                $("#" + arrServices[i] + "_contact_title_0").jqxInput({ height: 25, width: 310 }).val((ServiceContactDetails[1] != 'title' ? ServiceContactDetails[1] : ''));
                                                $("#" + arrServices[i] + "_contact_email_0").jqxInput({ height: 25, width: 310 }).val((ServiceContactDetails[2] != 'email' ? $(ServiceContactDetails[2]).text() : ''));
                                        } else {
                                                addContactRow(arrServices[i], 1, value);
                                        }
                                });
                        } else {
                                $("#" + arrServices[i] + "_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                                $("#" + arrServices[i] + "_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                                $("#" + arrServices[i] + "_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                        }
             }

                $('#testForm').jqxValidator({ position: 'right', rules: rules});
                // show the popup window.
                $("#popupWindow").jqxWindow('open');
            }
            if(userRole == 'Global'){
            $('#clearfilteringbutton').jqxButton({ theme: theme });
            }
            $('#clearfilteringbutton').click(function () {
                $("#jqxgrid").jqxGrid('clearfilters');
                $.ajax({
                            type: "POST",
                            url: "/reports/delete_grid_preferences/",
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            data: JSON.stringify({
                                formname: 'office_data'
                            })
                      });
                      $('#jqxgrid').jqxGrid('loadstate', defaultState);
            });
            $("#CancelNew").jqxButton({ theme: theme });
            $("#SaveNew").jqxButton({ theme: theme });
            $("#SaveNew").click(function () {
                if(!$('#testForm').jqxValidator('validate')) {
                        return false;
                }
                $("#SaveNew").attr('disabled', true);
                var state = $("#jqxgrid").jqxGrid('savestate');
                
                var keyContacts = [];
                var executiveContacts = [];
                for (var i = 0; i <= parseInt($("#executiveHeadCount").val()); i++) {
                        if($("#executive_head_contact_name_" + i).val() != '') {
                                executiveContact = $("#executive_head_contact_name_" + i).val() + '/' + ($("#executive_head_contact_title_" + i).val() != '' ? $("#executive_head_contact_title_" + i).val() : 'title') + '/' + ($("#executive_head_contact_email_" + i).val() != '' ? $("#executive_head_contact_email_" + i).val() : 'email');
                                executiveContacts.push(executiveContact);
                        }
                }
                var item = {};
                item['dept_name'] = 'Executive';
                item['dept_contacts'] = executiveContacts;
                keyContacts.push(item);
                
             var businessContacts = [];
                for (var i = 0; i <= parseInt($("#businessHeadCount").val()); i++) {
                        if($("#business_head_contact_name_" + i).val() != '') {
                                businessContact = $("#business_head_contact_name_" + i).val() + '/' + ($("#business_head_contact_title_" + i).val() != '' ? $("#business_head_contact_title_" + i).val() : 'title') + '/' + ($("#business_head_contact_email_" + i).val() != '' ? $("#business_head_contact_email_" + i).val() : 'email');
                                businessContacts.push(businessContact);
                        }
                }   
                var item = {};
                item['dept_name'] = 'BusinessHead';
                item['dept_contacts'] = businessContacts;
                keyContacts.push(item);
                
                var serviceContacts = [];
                for (i = 0; i < arrServices.length; i++) {
                        var serviceContact = [];
                        for (var j = 0; j <= parseInt($("#" + arrServices[i] + "Count").val()); j++) {
                                if($("#" + arrServices[i] + "_contact_name_" + j).val() != '') {
                                        var contact = $("#" + arrServices[i] + "_contact_name_" + j).val() + '/' + ($("#" + arrServices[i] + "_contact_title_" + j).val() != '' ? $("#" + arrServices[i] + "_contact_title_" + j).val() : 'title') + '/' + ($("#" + arrServices[i] + "_contact_email_" + j).val() != '' ? $("#" + arrServices[i] + "_contact_email_" + j).val() : 'email');
                                        serviceContact.push(contact);
                                }
                        }
                        var item = {};
                        item['service_name'] = arrServices[i];
                        item['service_contacts'] = serviceContact;
                        serviceContacts.push(item);
                }

              var row = {
                        RecordId: $("#recordid").val(), Region: $("#region").val(), Country: $("#country").val(), City: $("#city").val(),
                        YearEstablished: $("#year_established").val(), EmployeeCount: $("#employee_count").val(), MarketsCovered: $("#markets_covered").val(),
                        KeyContacts: keyContacts, ServicesContacts: serviceContacts
                };

                $.ajax({
                    type: "POST",
                    url: "/reports/save_office_record/",
                    data: JSON.stringify(row),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#jqxgrid").jqxGrid('updateBoundData');
                            if (state) {
                                $("#jqxgrid").jqxGrid('loadstate', state);
                            }
                            $("#SaveNew").attr('disabled', false);
                            $("#popupWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            });

            var validator = {
                region : {
                        input: '#region', message: 'Region is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                        } 
                },
                country : {
                        input: '#country', message: 'Market is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                        }
                },
                city : {
                        input: '#city', message: 'Location (City) is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                        } 
                }
            }

      });

        var addContactRow = function (rowType, serviceFlag, data) {
                if(data) {
                        var dataDetails = data.split("<br/>");
                } else {
                        var dataDetails = [];
                }

                if(serviceFlag) {
                        var rowsCnt = parseInt($("#" + rowType + "Count").val());
                        rowsCnt++;

                        var html = "<tr class=\"more-contacts\">"
                                 +  "<td align=\"left\"><input type=\"text\" id=\"" + rowType + "_contact_name_" + rowsCnt + "\" autocomplete=\"off\"/></td>"
                                 +  "<td align=\"left\"><input type=\"text\" id=\"" + rowType + "_contact_title_" + rowsCnt + "\" autocomplete=\"off\"/></td>"
                                 +  "<td align=\"left\"><input type=\"text\" id=\"" + rowType + "_contact_email_" + rowsCnt + "\" autocomplete=\"off\"/></td>"
                                + "</tr>";

                        $("#tbl-" + rowType).append(html);

                        $("#" + rowType + "Count").val(rowsCnt);

                        $("#" + rowType + "_contact_name_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[0] ? dataDetails[0] : ''));
                        $("#" + rowType + "_contact_title_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[1] && dataDetails[1] != 'title' ? dataDetails[1] : ''));
                        $("#" + rowType + "_contact_email_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[2] && dataDetails[2] != 'email' ? $(dataDetails[2]).text() : ''));
                } else {
                        var rowsCnt = parseInt($("#" + rowType + "HeadCount").val());
                        rowsCnt++;

                        var html = "<tr class=\"more-contacts\">"
                                 +  "<td align=\"left\"><input type=\"text\" id=\"" + rowType + "_head_contact_name_" + rowsCnt + "\" autocomplete=\"off\"/></td>"
                                 +  "<td align=\"left\"><input type=\"text\" id=\"" + rowType + "_head_contact_title_" + rowsCnt + "\" autocomplete=\"off\"/></td>"
                                 +  "<td align=\"left\"><input type=\"text\" id=\"" + rowType + "_head_contact_email_" + rowsCnt + "\" autocomplete=\"off\"/></td>"
                                + "</tr>";

                        $("#tbl-" + rowType).append(html);

                        $("#" + rowType + "HeadCount").val(rowsCnt);

                        $("#" + rowType + "_head_contact_name_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[0] ? dataDetails[0] : ''));
                        $("#" + rowType + "_head_contact_title_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[1] && dataDetails[1] != 'title' ? dataDetails[1] : ''));
                        $("#" + rowType + "_head_contact_email_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[2] && dataDetails[2] != 'email' ? $(dataDetails[2]).text() : ''));
                }
        }
    </script>
    <div id="tab-menu" align="left">
            <div id="-reports-client-report" class="light-grey">
                    <a href="/reports/office_report">SEARCH</a>
            </div>
            <div id="-reports-client-data" class="light-grey selected">
                    <a href="/reports/office_data">UPDATE YOUR RECORDS</a>
            </div>
        
         <?php if($userRole == 'Global') { ?>
        <div style='float: right; padding-right: 7px; margin-top: 35px;'>
            <button value="Reset" id="clearfilteringbutton" title="Reset filters">RESET</button>
            <button value="Add a new record" class='createNew'>ADD NEW LOCATION</button>
        </div>
        <?php } ?>
    </div>
    
<script type="text/javascript">
        $(document).ready(function() {
                $('#tab-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
                $('#nav-menu div#-reports-office_report').addClass('selected');
        });
</script>

<div id='jqxWidget'>
        <div id="jqxgrid"></div>
            <div style='margin-top: 20px;'>
        </div>
         <?php if($userRole == 'Global') { ?>
        <div style='float: right; padding-right: 7px; padding-bottom: 30px'>
            <button value="Add a new record" class='createNew'>ADD NEW LOCATION</button>
        </div>
        <?php } ?>

    <div id="popupWindow">
        <div>Create new location</div>
        <div>
            <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelNew" value="Cancel">CANCEL</button></div>
            <form id="testForm" action="./">
                <div><div style="width: 525px; display: inline-block; vertical-align: top">
                    <fieldset style="width: 1018px">
                        <legend>General Information</legend>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block; vertical-align: text-bottom;">Region</div>
                            <div style="padding-bottom: 5px; display: inline-block;"><div id="divRegion"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block; vertical-align: text-bottom;">Market</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divMarket"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Location Name (City)</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divCity"></div>
                                    <input type="hidden" id="recordid"/>
                            </div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Year established</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divYear"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Total employee</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divEmployeeCount"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Markets Covered</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divMarketsCovered"></div></div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <fieldset style="width: 1018px">
                <legend>Key management contacts</legend>
                <fieldset style="width: 990px">
                        <legend>Executive Contact</legend>
                        <table align="center" id="tbl-executive">
                            <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                                <td align="center">Name</td>
                                <td align="center">Title</td>
                                <td align="center">Email</td>
                            </tr>
                             <tr>
                                <td align="left"><input type="text" id="executive_head_contact_name_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="executive_head_contact_title_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="executive_head_contact_email_0" autocomplete="off"/>
                                <input type="hidden" class="contact-row-count" id="executiveHeadCount" value="0"/></td>
                            </tr>
                        </table>
                        <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('executive')">Add more...</a></div>
                </fieldset>
                <fieldset style="width: 990px">
                        <legend>New Business</legend>
                        <table align="center" id="tbl-business">
                            <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                                <td align="center">Name</td>
                                <td align="center">Title</td>
                                <td align="center">Email</td>
                            </tr>
                            <tr>
                                <td align="left"><input type="text" id="business_head_contact_name_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="business_head_contact_title_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="business_head_contact_email_0" autocomplete="off"/>
                                <input type="hidden" class="contact-row-count" id="businessHeadCount" value="0"/></td>
                            </tr>
                        </table>
                         <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('business')">Add more...</a></div>

                </fieldset>
        </fieldset>
<?php
        foreach($services as $service) {
?>
            <fieldset style="width: 1018px">
                <legend><?php echo $service; ?></legend>
                <table align="center" id="tbl-<?php echo $service; ?>">
                    <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                        <td align="center">Key Contact Name</td>
                        <td align="center">Key Contact Title</td>
                        <td align="center">Key Contact Email</td>
                    </tr>
                    <tr>
                        <td align="left"><input type="text" id="<?php echo $service; ?>_contact_name_0" autocomplete="off"/></td>
                        <td align="left"><input type="text" id="<?php echo $service; ?>_contact_title_0" autocomplete="off"/></td>
                        <td align="left"><input type="text" id="<?php echo $service; ?>_contact_email_0" autocomplete="off"/></td>
                    </tr>
                </table>
                <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('<?php echo $service; ?>', '1')">Add more...</a></div>
                    <input type="hidden" class="contact-row-count" id="<?php echo $service; ?>Count" value="0"/>
           </fieldset>
<?php
        }
?>
        </form>
        <div style="padding-top: 20px; padding-bottom: 20px;" align="right"><button style="margin-right: 15px;" id="SaveNew">CREATE NEW LOCATION</button></div>
        </div>
   </div>
</div>
