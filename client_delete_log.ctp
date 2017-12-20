<script type="text/javascript">
         $(document).ready(function () {
             var theme = 'base';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }
             var cities = jQuery.parseJSON('<?php echo $cities; ?>');
             var arrCities = $.map(cities, function(el) { return el; });
             var markets = jQuery.parseJSON('<?php echo $markets; ?>');
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });

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
                url: "/reports/get_deleted_records/",
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
                    { name: 'PitchStage', type: 'string' },
                    { name: 'ClientSince', type: 'date' },
                    { name: 'Lost', type: 'date' },
                    { name: 'Service', type: 'string' },
                    { name: 'MarketScope', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'number' },
                    { name: 'FiscalRevenue',type:'number'},
                    { name: 'Comments', type: 'string' },
                    { name: 'Year', type: 'number' },
                    { name: 'Created', type: 'date' },
                    { name: 'SearchClientName', type: 'string' },
                    { name: 'SearchParentCompany', type: 'string' },
                    { name: 'Deleted', type: 'date' },
                    { name: 'DeletedBy', type: 'string' },
                    { name: 'SearchDeletedBy', type: 'string' }
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
                    var filtervalue = removeSpecialChars(textInput.val());
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
                  { text: 'Created', datafield: 'Created', hidden: true },
                  { text: 'Deleted', datafield: 'Deleted', hidden: true },
                  { text: 'Year', datafield: 'Year', hidden: true },
                  { text: '', datafield: 'SearchClientName', hidden: true },
                  { text: '', datafield: 'SearchParentCompany', hidden: true },
                  { text: '', datafield: 'SearchDeletedBy', hidden: true },
                  { text: 'Deleted By', datafield: 'DeletedBy', width: 150, pinned: true, filtertype: 'custom',
                      createfilterpanel: function (datafield, filterPanel) {
                          buildFilterPanel(filterPanel, datafield);
                      }
                  },
                  { text: 'Region', datafield: 'Region', width: 100, filtertype: 'checkedlist', filteritems: arrRegions, pinned: true },
                  { text: 'Country', datafield: 'Country', width: 120, filtertype: 'checkedlist', pinned: true },
                  { text: 'City', datafield: 'City', width: 120, filtertype: 'checkedlist', pinned: true },
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250, pinned: true, filtertype: 'custom',
                      createfilterpanel: function (datafield, filterPanel) {
                          buildFilterPanel(filterPanel, datafield);
                      }
                  },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250, filtertype: 'custom',
                      createfilterpanel: function (datafield, filterPanel) {
                          buildFilterPanel(filterPanel, datafield);
                      }
                  },
                  { text: 'Client Category', datafield: 'ClientCategory', width: 200, filtertype: 'checkedlist' },
                  { text: 'Lead Agency', datafield: 'LeadAgency', width: 130, filtertype: 'checkedlist' },
                  { text: 'Status', columntype: 'template', datafield: 'PitchStage', width: 130, filtertype: 'checkedlist' },
                  { text: 'Service', datafield: 'Service', width: 150, filtertype: 'checkedlist' },
                  { text: 'Client Since (M-Y)', datafield: 'ClientSince', width: 140, filtertype: 'date', cellsformat: 'MM/yyyy' },
                  { text: 'Lost Since (M-Y)', datafield: 'Lost', width: 140, filtertype: 'date', cellsformat: 'MM/yyyy' },
                  { text: 'Pitched (M-Y)', datafield: 'PitchStart', width: 140, filtertype: 'date', cellsformat: 'MM/yyyy' },
                  { text: 'Scope', datafield: 'MarketScope', width: 100, filtertype: 'checkedlist' },
                  { text: 'Active Markets', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 160, filtertype: 'checkedlist' },
                  { text: 'Currency', datafield: 'Currency', width: 100, filtertype: 'checkedlist' },
                  { text: 'Estimated Revenue', columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 200, align: 'left', cellsalign: 'right', cellsFormat: 'f2' },
                  { text: 'Fiscal Revenue', columngroup: 'FiscalRevenue', datafield: 'FiscalRevenue', width: 200, align: 'left', cellsalign: 'right', cellsFormat: 'f2' },
                  { text: 'Comments', columngroup: 'Comments', datafield: 'Comments', width: 230 }
                ],
                ready:function()
                {
                        horizontalScroll();
                } 
            });
            $("#jqxgrid").on("filter", function (event) {
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
            // clear the filtering.
            $('#clearfilteringbutton').click(function () {
                $("#jqxgrid").jqxGrid('clearfilters');
                if(textInput) {
                    textInput.val("");
                }
            });
            $("#loaderWindow").jqxWindow({
                width: 300, resizable: false,  isModal: true, autoOpen: false, maxWidth: 400, maxHeight: 250, showCloseButton: false, keyboardCloseKey: 'none' 
            });
            $('#exporttoexcelbutton').click(function () {
                $("#loaderWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "100px", maxWidth: 400, isModal: true, draggable: false });
                $("#loaderWindow").jqxWindow('open');

                var rows = $("#jqxgrid").jqxGrid('getrows');
                var tz = jstz.determine(); // Determines the time zone of the browser client
                $.ajax({
                    type: "POST",
                    url: "/reports/export_client_delete_log/",
                        data: JSON.stringify({datarows: rows, timezone: tz.name()
                    }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        $("#loaderWindow").jqxWindow('close');
                        if(result.success == true) {
                            window.open('/files/' + result.filename);
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            });
   });
    </script>
    
<div id='jqxWidget'>
        <div id="tab-menu" align="left">
            <div id="-client-delete-log" class="light-grey selected">
                    <a>DELETED PITCHES LOG</a>
            </div>
            <div style="float: right; margin-top: 35px;">
                <button value="Reset" id="clearfilteringbutton" title="Reset filters">RESET</button>
                <button style="margin-left: 5px" value="Export to Excel" id="exporttoexcelbutton">EXPORT .XLS</button>
            </div>
        </div>
        <div id="jqxgrid"></div>
        <div id="loaderWindow">
                <div>Export to excel</div>
                <div style="overflow: hidden;">
                        <div id="divLoader" align="center" style="padding-top: 15px; padding-left: 90px;">
                                <div class="jqx-grid-load" style="float: left; overflow: hidden; width: 32px; height: 32px;"></div>
                                <span style="margin-top: 10px; float: left; display: block; margin-left: 5px;">Please wait...</span>
                        </div>
                </div>
        </div>
</div>
