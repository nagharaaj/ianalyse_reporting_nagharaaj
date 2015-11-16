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

             var theme = 'base';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
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
                    { name: 'Address', type: 'string' },
                    { name: 'Telephone', type: 'string' },
                    { name: 'GeneralEmail', type: 'string' },
                    { name: 'Website', type: 'string' },
                    { name: 'SocialAccount', type: 'string' },
                    { name: 'Executive', type: 'string' },
                    { name: 'countExecutive', type: 'number' },
                    { name: 'FinanceHead', type: 'string' },
                    { name: 'countFinanceHead', type: 'number' },
                    { name: 'ProductHead', type: 'string' },
                    { name: 'countProductHead', type: 'number' },
                    { name: 'StrategyHead', type: 'string' },
                    { name: 'countStrategyHead', type: 'number' },
                    { name: 'ClientHead', type: 'string' },
                    { name: 'countClientHead', type: 'number' },
                    { name: 'BusinessHead', type: 'string' },
                    { name: 'countBusinessHead', type: 'number' },
                    { name: 'MarketingHead', type: 'string' },
                    { name: 'countMarketingHead', type: 'number' },
                    { name: 'totalKeyEmployeeCount', type: 'number' },
                    { name: 'Affiliates', type: 'string' },
                    { name: 'countAffiliates', type: 'number' },
                    { name: 'Attribution', type: 'string' },
                    { name: 'countAttribution', type: 'number' },
                    { name: 'Content', type: 'string' },
                    { name: 'countContent', type: 'number' },
                    { name: 'Conversion', type: 'string' },
                    { name: 'countConversion', type: 'number' },
                    { name: 'Data', type: 'string' },
                    { name: 'countData', type: 'number' },
                    { name: 'Development', type: 'string' },
                    { name: 'countDevelopment', type: 'number' },
                    { name: 'Display', type: 'string' },
                    { name: 'countDisplay', type: 'number' },
                    { name: 'Feeds', type: 'string' },
                    { name: 'countFeeds', type: 'number' },
                    { name: 'Lead', type: 'string' },
                    { name: 'countLead', type: 'number' },
                    { name: 'Mobile', type: 'string' },
                    { name: 'countMobile', type: 'number' },
                    { name: 'RTB', type: 'string' },
                    { name: 'countRTB', type: 'number' },
                    { name: 'Search', type: 'string' },
                    { name: 'countSearch', type: 'number' },
                    { name: 'SEO', type: 'string' },
                    { name: 'countSEO', type: 'number' },
                    { name: 'SocialPaid', type: 'string' },
                    { name: 'countSocialPaid', type: 'number' },
                    { name: 'SocialManagement', type: 'string' },
                    { name: 'countSocialManagement', type: 'number' },
                    { name: 'Strategy', type: 'string' },
                    { name: 'countStrategy', type: 'number' },
                    { name: 'Technology', type: 'string' },
                    { name: 'countTechnology', type: 'number' },
                    { name: 'Video', type: 'string' },
                    { name: 'countVideo', type: 'number' },
                    { name: 'totalServiceEmployeeCount', type: 'number' },
                    { name: 'countSupportedLanguages', type: 'number' },
                    { name: 'SupportedLanguages', type: 'string' }
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
                autoheight: true,
                enablemousewheel: false,
                source: dataAdapter,
                pageable: true,
                pageSize: 20,
                pagerMode: 'simple',
                sortable: true,
                filterable: true,
                editable: false,
                autorowheight: true,
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
                  { text: 'Address', columngroup: 'ContactDetails', datafield: 'Address', width: 250, cellClassName: cellclass, align: 'center', filterable: false,
                          cellsrenderer: function(row, cell, value) {
                                if(value != '') {
                                        return '<a style="text-decoration:none;color:#000" href="https://maps.google.com/?q='+value+'"/ target="_blank">'+value+'</a>'
                                }
                          }
                  },
                  { text: 'Telephone', columngroup: 'ContactDetails', datafield: 'Telephone', width: 120, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'General email', columngroup: 'ContactDetails', datafield: 'GeneralEmail', width: 150, cellClassName: cellclass, align: 'center', filterable: false,
                          cellsrenderer: function(row, cell, value) {
                                return '<a href="mailto:'+value+'"/ target="_blank">'+value+'</a>'
                          }
                  },
                  { text: 'Website', columngroup: 'ContactDetails', datafield: 'Website', width: 150, cellClassName: cellclass, align: 'center', filterable: false,
                          cellsrenderer: function(row, cell, value) {
                                if(value.indexOf('http') != -1) {
                                        return '<a href="'+value+'"/ target="_blank">'+value+'</a>'
                                } else {
                                        return '<a href="http://'+value+'"/ target="_blank">'+value+'</a>'
                                }
                          }
                  },
                  { text: 'Twitter', columngroup: 'ContactDetails', datafield: 'SocialAccount', width: 150, cellClassName: cellclass, align: 'center', filterable: false,
                          cellsrenderer: function(row, cell, value) {
                                if(value.indexOf('http') != -1) {
                                        return '<a href="'+value+'"/ target="_blank">'+value+'</a>'
                                }
                          }
                  },
                  { text: 'Executive contact', columngroup: 'KeyContacts', datafield: 'Executive', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'KeyContacts', datafield: 'countExecutive', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'CFO or finance lead', columngroup: 'KeyContacts', datafield: 'FinanceHead', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'KeyContacts', datafield: 'countFinanceHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Head of product<br/>and services', columngroup: 'KeyContacts', datafield: 'ProductHead', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'KeyContacts', datafield: 'countProductHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Head of strategy', columngroup: 'KeyContacts', datafield: 'StrategyHead', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'KeyContacts', datafield: 'countStrategyHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Head of client services', columngroup: 'KeyContacts', datafield: 'ClientHead', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'KeyContacts', datafield: 'countClientHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'New business', columngroup: 'KeyContacts', datafield: 'BusinessHead', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'KeyContacts', datafield: 'countBusinessHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Marketing', columngroup: 'KeyContacts', datafield: 'MarketingHead', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'KeyContacts', datafield: 'countMarketingHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Total # employees', datafield: 'totalKeyEmployeeCount', width: 110, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Affiliates', datafield: 'Affiliates', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Affiliates', datafield: 'countAffiliates', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Attribution', datafield: 'Attribution', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Attribution', datafield: 'countAttribution', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Content', datafield: 'Content', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Content', datafield: 'countContent', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Conversion', datafield: 'Conversion', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Conversion', datafield: 'countConversion', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Data', datafield: 'Data', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Data', datafield: 'countData', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Development', datafield: 'Development', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Development', datafield: 'countDevelopment', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Display', datafield: 'Display', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Display', datafield: 'countDisplay', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Feeds', datafield: 'Feeds', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Feeds', datafield: 'countFeeds', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Lead', datafield: 'Lead', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Lead', datafield: 'countLead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Mobile', datafield: 'Mobile', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Mobile', datafield: 'countMobile', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'RTB', datafield: 'RTB', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'RTB', datafield: 'countRTB', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Search', datafield: 'Search', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Search', datafield: 'countSearch', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'SEO', datafield: 'SEO', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'SEO', datafield: 'countSEO', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'SocialPaid', datafield: 'SocialPaid', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'SocialPaid', datafield: 'countSocialPaid', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'SocialManagement', datafield: 'SocialManagement', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'SocialManagement', datafield: 'countSocialManagement', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Strategy', datafield: 'Strategy', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Strategy', datafield: 'countStrategy', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Technology', datafield: 'Technology', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Technology', datafield: 'countTechnology', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Video', datafield: 'Video', width: 150, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: '# of employee<br/>or FTE', columngroup: 'Video', datafield: 'countVideo', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Total # employees', datafield: 'totalServiceEmployeeCount', width: 110, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: '# of supported<br/>languages', columngroup: 'Languages', datafield: 'countSupportedLanguages', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'List supported<br/>languages', columngroup: 'Languages', datafield: 'SupportedLanguages', width: 200, cellClassName: cellclass, filtertype: 'custom', filteritems: arrLanguages, align: 'center', 
                      createfilterpanel: function (datafield, filterPanel) {
                          buildFilterPanel(filterPanel, datafield);
                      }
                  }
                ],
                columngroups: 
                [
                  { text: 'General information', align: 'center', name: 'GeneralInfo' },
                  { text: 'Contact details', align: 'center', name: 'ContactDetails' },
                  { text: 'Key management contacts', align: 'center', name: 'KeyContacts' },
                  { text: 'Affiliates', align: 'center', name: 'Affiliates' },
                  { text: 'Attribution', align: 'center', name: 'Attribution' },
                  { text: 'Content', align: 'center', name: 'Content' },
                  { text: 'Conversion opt.', align: 'center', name: 'Conversion' },
                  { text: 'Data & insights', align: 'center', name: 'Data' },
                  { text: 'Development', align: 'center', name: 'Development' },
                  { text: 'Display', align: 'center', name: 'Display' },
                  { text: 'Feeds', align: 'center', name: 'Feeds' },
                  { text: 'Lead Gen', align: 'center', name: 'Lead' },
                  { text: 'Mobile', align: 'center', name: 'Mobile' },
                  { text: 'RTB', align: 'center', name: 'RTB' },
                  { text: 'Search - PPC', align: 'center', name: 'Search' },
                  { text: 'SEO', align: 'center', name: 'SEO' },
                  { text: 'Social - Paid', align: 'center', name: 'SocialPaid' },
                  { text: 'Social - Management', align: 'center', name: 'SocialManagement' },
                  { text: 'Strategy', align: 'center', name: 'Strategy' },
                  { text: 'Technology', align: 'center', name: 'Technology' },
                  { text: 'Video', align: 'center', name: 'Video' },
                  { text: 'Languages', align: 'center', name: 'Languages' }
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
            if($("#createNew").is(':visible')) {
                $("#createNew").jqxButton({ theme: theme });
                $("#createNew").click(function () {
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

                $("#divAddress").html('');
                var inpAddress = $("<input type=\"text\" id=\"address\" />");
                $("#divAddress").append(inpAddress);
                $("#address").jqxInput({ height: 25, width: 250 }).val((rowData ? rowData.Address : ''));

                $("#divTelephone").html('');
                var inpTelephone = $("<input type=\"text\" id=\"telephone\" />");
                $("#divTelephone").append(inpTelephone);
                $("#telephone").jqxInput({ height: 25, width: 150 }).val((rowData ? rowData.Telephone : ''));

                $("#divEmail").html('');
                var inpEmail = $("<input type=\"text\" id=\"contact_email\" />");
                $("#divEmail").append(inpEmail);
                $("#contact_email").jqxInput({ height: 25, width: 200 }).val((rowData ? rowData.GeneralEmail : ''));

                $("#divWebsite").html('');
                var inpWebsite = $("<input type=\"text\" id=\"website\" />");
                $("#divWebsite").append(inpWebsite);
                $("#website").jqxInput({ height: 25, width: 200 }).val((rowData ? rowData.Website : ''));

                $("#divSocialAccount").html('');
                var inpSocialAccount = $("<input type=\"text\" id=\"social_account\" />");
                $("#divSocialAccount").append(inpSocialAccount);
                $("#social_account").jqxInput({ height: 25, width: 250 }).val((rowData ? rowData.SocialAccount : ''));

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
                $("#divExecutiveEmpCount").html('');
                var inpExecutiveEmpCount = $("<input type=\"text\" id=\"executive_employee_count\" />");
                $("#divExecutiveEmpCount").append(inpExecutiveEmpCount);
                $("#executive_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val((rowData && rowData.countExecutive ? rowData.countExecutive : ''));

                if(rowData && rowData.FinanceHead) {
                        arrFinanceHead = rowData.FinanceHead.split("<br/>-------------------------<br/>");
                        $.each(arrFinanceHead, function(index, value) {
                                if(index == 0) {
                                        var financeHeadDetails = value.split("<br/>");
                                        $("#finance_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val(financeHeadDetails[0]);
                                        $("#finance_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val((financeHeadDetails[1] != 'title' ? financeHeadDetails[1] : ''));
                                        $("#finance_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val((financeHeadDetails[2] != 'email' ? $(financeHeadDetails[2]).text() : ''));
                                } else {
                                        addContactRow('finance', 0, value);
                                }
                        });
                } else {
                        $("#finance_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#finance_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#finance_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                }
                $("#divFinanceHeadEmpCount").html('');
                var inpFinanceHeadEmpCount = $("<input type=\"text\" id=\"finance_employee_count\" />");
                $("#divFinanceHeadEmpCount").append(inpFinanceHeadEmpCount);
                $("#finance_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val((rowData && rowData.countFinanceHead ? rowData.countFinanceHead : ''));

                if(rowData && rowData.ProductHead) {
                        arrProductHead = rowData.ProductHead.split("<br/>-------------------------<br/>");
                        $.each(arrProductHead, function(index, value) {
                                if(index == 0) {
                                        var productHeadDetails = value.split("<br/>");
                                        $("#product_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val(productHeadDetails[0]);
                                        $("#product_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val((productHeadDetails[1] != 'title' ? productHeadDetails[1] : ''));
                                        $("#product_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val((productHeadDetails[2] != 'email' ? $(productHeadDetails[2]).text() : ''));
                                } else {
                                        addContactRow('product', 0, value);
                                }
                        });
                } else {
                        $("#product_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#product_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#product_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                }
                $("#divProductHeadEmpCount").html('');
                var inpProductHeadEmpCount = $("<input type=\"text\" id=\"product_employee_count\" />");
                $("#divProductHeadEmpCount").append(inpProductHeadEmpCount);
                $("#product_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val((rowData && rowData.countProductHead ? rowData.countProductHead : ''));

                if(rowData && rowData.StrategyHead) {
                        arrStrategyHead = rowData.StrategyHead.split("<br/>-------------------------<br/>");
                        $.each(arrStrategyHead, function(index, value) {
                                if(index == 0) {
                                        var strategyHeadDetails = value.split("<br/>");
                                        $("#strategy_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val(strategyHeadDetails[0]);
                                        $("#strategy_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val((strategyHeadDetails[1] != 'title' ? strategyHeadDetails[1] : ''));
                                        $("#strategy_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val((strategyHeadDetails[2] != 'email' ? $(strategyHeadDetails[2]).text() : ''));
                                } else {
                                        addContactRow('strategy', 0, value);
                                }
                        });
                } else {
                        $("#strategy_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#strategy_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#strategy_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                }
                $("#divStrategyHeadEmpCount").html('');
                var inpStrategyHeadEmpCount = $("<input type=\"text\" id=\"strategy_employee_count\" />");
                $("#divStrategyHeadEmpCount").append(inpStrategyHeadEmpCount);
                $("#strategy_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val((rowData && rowData.countStrategyHead ? rowData.countStrategyHead : ''));

                if(rowData && rowData.ClientHead) {
                        arrClientHead = rowData.ClientHead.split("<br/>-------------------------<br/>");
                        $.each(arrClientHead, function(index, value) {
                                if(index == 0) {
                                        var clientHeadDetails = value.split("<br/>");
                                        $("#client_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val(clientHeadDetails[0]);
                                        $("#client_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val((clientHeadDetails[1] != 'title' ? clientHeadDetails[1] : ''));
                                        $("#client_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val((clientHeadDetails[2] != 'email' ? $(clientHeadDetails[2]).text() : ''));
                                } else {
                                        addContactRow('client', 0, value);
                                }
                        });
                } else {
                        $("#client_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#client_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#client_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                }
                $("#divClientHeadEmpCount").html('');
                var inpClientHeadEmpCount = $("<input type=\"text\" id=\"client_employee_count\" />");
                $("#divClientHeadEmpCount").append(inpClientHeadEmpCount);
                $("#client_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val((rowData && rowData.countClientHead ? rowData.countClientHead : ''));

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
                } else {
                        $("#business_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#business_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#business_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                }
                $("#divBusinessHeadEmpCount").html('');
                var inpBusinessHeadEmpCount = $("<input type=\"text\" id=\"business_employee_count\" />");
                $("#divBusinessHeadEmpCount").append(inpBusinessHeadEmpCount);
                $("#business_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val((rowData && rowData.countBusinessHead ? rowData.countBusinessHead : ''));

                if(rowData && rowData.MarketingHead) {
                        arrMarketingHead = rowData.MarketingHead.split("<br/>-------------------------<br/>");
                        $.each(arrMarketingHead, function(index, value) {
                                if(index == 0) {
                                        var marketingHeadDetails = value.split("<br/>");
                                        $("#marketing_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val(marketingHeadDetails[0]);
                                        $("#marketing_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val((marketingHeadDetails[1] != 'title' ? marketingHeadDetails[1] : ''));
                                        $("#marketing_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val((marketingHeadDetails[2] != 'email' ? $(marketingHeadDetails[2]).text() : ''));
                                } else {
                                        addContactRow('marketing', 0, value);
                                }
                        });
                } else {
                        $("#marketing_head_contact_name_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#marketing_head_contact_title_0").jqxInput({ height: 25, width: 310 }).val('');
                        $("#marketing_head_contact_email_0").jqxInput({ height: 25, width: 310 }).val('');
                }
                $("#divMarketingHeadEmpCount").html('');
                var inpMarketingHeadEmpCount = $("<input type=\"text\" id=\"marketing_employee_count\" />");
                $("#divMarketingHeadEmpCount").append(inpMarketingHeadEmpCount);
                $("#marketing_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val((rowData && rowData.countMarketingHead ? rowData.countMarketingHead : ''));

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
                        $("#div" + arrServices[i] + "EmpCount").html('');
                        var inpServiceEmpCount = $("<input type=\"text\" id=\"" + arrServices[i] + "_employee_count\" />");
                        $("#div" + arrServices[i] + "EmpCount").append(inpServiceEmpCount);
                        $("#" + arrServices[i] + "_employee_count").jqxInput({ height: 25, width: 110, placeHolder: "Ex: 1, 2.5, 0.2" }).val(rowData && rowData['count' + arrServices[i]] ? rowData['count' + arrServices[i]] : '');
                }

                $("#divLanguagesSupported").html('');
                var inpLanguages = $("<div id=\"languages\"></div>");
                $("#divLanguagesSupported").append(inpLanguages);
                $("#languages").jqxDropDownList({ source: languages, checkboxes: true }).val('');
                if(rowData && rowData.SupportedLanguages) {
                        var entities = rowData.SupportedLanguages.split(', ');
                        for(key in entities) {
                                if(arrLanguages.indexOf(entities[key]) != -1) {
                                       index = arrLanguages.indexOf(entities[key]);
                                       $("#languages").jqxDropDownList('checkIndex', index);
                                }
                        }
                }
                $("#divLanguagesCount").html('');
                var inpLanguagesCount = $("<input type=\"text\" id=\"languages_count\" readonly class=\"readonly\" />");
                $("#divLanguagesCount").append(inpLanguagesCount);
                $("#languages_count").jqxInput({ height: 25, width: 100 }).val((rowData && rowData.countSupportedLanguages ? rowData.countSupportedLanguages : ''));

                $("#languages").on('checkChange', function (event) {
                        var args = event.args;
                        if (args) {
                                var checkedItems = $("#languages").jqxDropDownList('getCheckedItems');
                                $("#languages_count").val(checkedItems.length);
                        }
                });

                $('#testForm').jqxValidator({ position: 'right', rules: rules});
                // show the popup window.
                $("#popupWindow").jqxWindow('open');
            }
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
                item['dept_emp_count'] = $("#executive_employee_count").val();
                keyContacts.push(item);
                var financeContacts = [];
                for (var i = 0; i <= parseInt($("#financeHeadCount").val()); i++) {
                        if($("#finance_head_contact_name_" + i).val() != '') {
                                financeContact = $("#finance_head_contact_name_" + i).val() + '/' + ($("#finance_head_contact_title_" + i).val() != '' ? $("#finance_head_contact_title_" + i).val() : 'title') + '/' + ($("#finance_head_contact_email_" + i).val() != '' ? $("#finance_head_contact_email_" + i).val() : 'email');
                                financeContacts.push(financeContact);
                        }
                }
                var item = {};
                item['dept_name'] = 'FinanceHead';
                item['dept_contacts'] = financeContacts;
                item['dept_emp_count'] = $("#finance_employee_count").val();
                keyContacts.push(item);
                var productContacts = [];
                for (var i = 0; i <= parseInt($("#productHeadCount").val()); i++) {
                        if($("#product_head_contact_name_" + i).val() != '') {
                                productContact = $("#product_head_contact_name_" + i).val() + '/' + ($("#product_head_contact_title_" + i).val() != '' ? $("#product_head_contact_title_" + i).val() : 'title') + '/' + ($("#product_head_contact_email_" + i).val() != '' ? $("#product_head_contact_email_" + i).val() : 'email');
                                productContacts.push(productContact);
                        }
                }
                var item = {};
                item['dept_name'] = 'ProductHead';
                item['dept_contacts'] = productContacts;
                item['dept_emp_count'] = $("#product_employee_count").val();
                keyContacts.push(item);
                var strategyContacts = [];
                for (var i = 0; i <= parseInt($("#strategyHeadCount").val()); i++) {
                        if($("#strategy_head_contact_name_" + i).val() != '') {
                                strategyContact = $("#strategy_head_contact_name_" + i).val() + '/' + ($("#strategy_head_contact_title_" + i).val() != '' ? $("#strategy_head_contact_title_" + i).val() : 'title') + '/' + ($("#strategy_head_contact_email_" + i).val() != '' ? $("#strategy_head_contact_email_" + i).val() : 'email');
                                strategyContacts.push(strategyContact);
                        }
                }
                var item = {};
                item['dept_name'] = 'StrategyHead';
                item['dept_contacts'] = strategyContacts;
                item['dept_emp_count'] = $("#strategy_employee_count").val();
                keyContacts.push(item);
                var clientContacts = [];
                for (var i = 0; i <= parseInt($("#clientHeadCount").val()); i++) {
                        if($("#client_head_contact_name_" + i).val() != '') {
                                clientContact = $("#client_head_contact_name_" + i).val() + '/' + ($("#client_head_contact_title_" + i).val() != '' ? $("#client_head_contact_title_" + i).val() : 'title') + '/' + ($("#client_head_contact_email_" + i).val() != '' ? $("#client_head_contact_email_" + i).val() : 'email');
                                clientContacts.push(clientContact);
                        }
                }
                var item = {};
                item['dept_name'] = 'ClientHead';
                item['dept_contacts'] = clientContacts;
                item['dept_emp_count'] = $("#client_employee_count").val();
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
                item['dept_emp_count'] = $("#business_employee_count").val();
                keyContacts.push(item);
                var marketingContacts = [];
                for (var i = 0; i <= parseInt($("#marketingHeadCount").val()); i++) {
                        if($("#marketing_head_contact_name_" + i).val() != '') {
                                marketingContact = $("#marketing_head_contact_name_" + i).val() + '/' + ($("#marketing_head_contact_title_" + i).val() != '' ? $("#marketing_head_contact_title_" + i).val() : 'title') + '/' + ($("#marketing_head_contact_email_" + i).val() != '' ? $("#marketing_head_contact_email_" + i).val() : 'email');
                                marketingContacts.push(marketingContact);
                        }
                }
                var item = {};
                item['dept_name'] = 'MarketingHead';
                item['dept_contacts'] = marketingContacts;
                item['dept_emp_count'] = $("#marketing_employee_count").val();
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
                        item['service_emp_count'] = $("#" + arrServices[i] + "_employee_count").val();
                        serviceContacts.push(item);
                }

                var row = {
                        RecordId: $("#recordid").val(), Region: $("#region").val(), Country: $("#country").val(), City: $("#city").val(),
                        YearEstablished: $("#year_established").val(), EmployeeCount: $("#employee_count").val(), Address: $("#address").val(), 
                        Telephone: $("#telephone").val(), ContactEmail: $("#contact_email").val(), Website: $("#website").val(), SocialAccount: $("social_account").val(),
                        KeyContacts: keyContacts, ServicesContacts: serviceContacts, SupportedLanguages: $("#languages").val()
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
                        $("#" + rowType + "_contact_email_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[2] && dataDetails[2] != 'email' ? dataDetails[2] : ''));
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
                        $("#" + rowType + "_head_contact_email_" + rowsCnt).jqxInput({ height: 25, width: 310 }).val((dataDetails[2] && dataDetails[2] != 'title' ? dataDetails[2] : ''));
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
            <button value="Add a new record" id='createNew'>ADD NEW LOCATION</button>
        </div>
        <?php } ?>

    <div id="popupWindow">
        <div>Create new location</div>
        <div>
            <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelNew" value="Cancel">CANCEL</button></div>
            <form id="testForm" action="./">
                <div><div style="width: 525px; display: inline-block; vertical-align: top">
                    <fieldset style="width: 490px">
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
                    </fieldset>
                </div>
                <div style="width: 525px; display: inline-block; vertical-align: top">
                    <fieldset style="width: 490px">
                        <legend>Contact details</legend>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Address</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divAddress"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Telephone</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divTelephone"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">General email</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divEmail"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Website</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divWebsite"></div></div>
                        </div>
                        <div>
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;">Twitter</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divSocialAccount"></div></div>
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
                                <td align="left"><input type="text" id="executive_head_contact_email_0" autocomplete="off"/></td>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;">
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divExecutiveEmpCount"></div></div>
                            <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('executive')">Add more...</a></div>
                            <input type="hidden" class="contact-row-count" id="executiveHeadCount" value="0"/>
                        </div>
                </fieldset>
                <fieldset style="width: 990px">
                        <legend>CFO or finance lead</legend>
                        <table align="center" id="tbl-finance">
                            <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                                <td align="center">Name</td>
                                <td align="center">Title</td>
                                <td align="center">Email</td>
                            </tr>
                            <tr>
                                <td align="left"><input type="text" id="finance_head_contact_name_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="finance_head_contact_title_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="finance_head_contact_email_0" autocomplete="off"/></td>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;">
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divFinanceHeadEmpCount"></div></div>
                            <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('finance')">Add more...</a></div>
                            <input type="hidden" class="contact-row-count" id="financeHeadCount" value="0"/>
                        </div>
                </fieldset>
                <fieldset style="width: 990px">
                        <legend>Head of product &AMP; services</legend>
                        <table align="center" id="tbl-product">
                            <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                                <td align="center">Name</td>
                                <td align="center">Title</td>
                                <td align="center">Email</td>
                            </tr>
                            <tr>
                                <td align="left"><input type="text" id="product_head_contact_name_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="product_head_contact_title_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="product_head_contact_email_0" autocomplete="off"/></td>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;">
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divProductHeadEmpCount"></div></div>
                            <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('product')">Add more...</a></div>
                            <input type="hidden" class="contact-row-count" id="productHeadCount" value="0"/>
                        </div>
                </fieldset>
                <fieldset style="width: 990px">
                        <legend>Head of strategy</legend>
                        <table align="center" id="tbl-strategy">
                            <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                                <td align="center">Name</td>
                                <td align="center">Title</td>
                                <td align="center">Email</td>
                            </tr>
                            <tr>
                                <td align="left"><input type="text" id="strategy_head_contact_name_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="strategy_head_contact_title_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="strategy_head_contact_email_0" autocomplete="off"/></td>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;">
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divStrategyHeadEmpCount"></div></div>
                            <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('strategy')">Add more...</a></div>
                            <input type="hidden" class="contact-row-count" id="strategyHeadCount" value="0"/>
                        </div>
                </fieldset>
                <fieldset style="width: 990px">
                        <legend>Head of client services</legend>
                        <table align="center" id="tbl-client">
                            <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                                <td align="center">Name</td>
                                <td align="center">Title</td>
                                <td align="center">Email</td>
                            </tr>
                            <tr>
                                <td align="left"><input type="text" id="client_head_contact_name_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="client_head_contact_title_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="client_head_contact_email_0" autocomplete="off"/></td>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;">
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divClientHeadEmpCount"></div></div>
                            <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('client')">Add more...</a></div>
                            <input type="hidden" class="contact-row-count" id="clientHeadCount" value="0"/>
                        </div>
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
                                <td align="left"><input type="text" id="business_head_contact_email_0" autocomplete="off"/></td>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;">
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divBusinessHeadEmpCount"></div></div>
                            <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('business')">Add more...</a></div>
                            <input type="hidden" class="contact-row-count" id="businessHeadCount" value="0"/>
                        </div>
                </fieldset>
                <fieldset style="width: 990px">
                        <legend>Marketing</legend>
                        <table align="center" id="tbl-marketing">
                            <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                                <td align="center">Name</td>
                                <td align="center">Title</td>
                                <td align="center">Email</td>
                            </tr>
                            <tr>
                                <td align="left"><input type="text" id="marketing_head_contact_name_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="marketing_head_contact_title_0" autocomplete="off"/></td>
                                <td align="left"><input type="text" id="marketing_head_contact_email_0" autocomplete="off"/></td>
                            </tr>
                        </table>
                        <div style="margin-top: 10px;">
                            <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                            <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divMarketingHeadEmpCount"></div></div>
                            <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('marketing')">Add more...</a></div>
                            <input type="hidden" class="contact-row-count" id="marketingHeadCount" value="0"/>
                        </div>
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
                <div style="margin-top: 10px; margin-left: 10px;">
                    <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of employee or FTE</div>
                    <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="div<?php echo $service; ?>EmpCount"></div></div>
                    <div align="right" style="padding-bottom: 5px; float: right; padding-right: 25px"><a style="text-decoration: none; cursor: pointer" href="javascript:addContactRow('<?php echo $service; ?>', '1')">Add more...</a></div>
                    <input type="hidden" class="contact-row-count" id="<?php echo $service; ?>Count" value="0"/>
                </div>
             </fieldset>
<?php
        }
?>
            <fieldset style="width: 1018px">
                <legend>Languages</legend>
                <div>
                    <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block; vertical-align: text-bottom;">Languages supported</div>
                    <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divLanguagesSupported"></div></div>
                </div>
                <div>
                    <div style="width: 150px; padding-bottom: 5px; padding-right: 5px; text-align: right; display: inline-block;"># of languages supported</div>
                    <div align="left" style="padding-bottom: 5px; display: inline-block;"><div id="divLanguagesCount"></div></div>
                </div>
            </fieldset>
        </form>
        <div style="padding-top: 20px; padding-bottom: 20px;" align="right"><button style="margin-right: 15px;" id="SaveNew">CREATE NEW LOCATION</button></div>
        </div>
   </div>
</div>
