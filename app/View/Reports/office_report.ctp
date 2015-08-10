    <script type="text/javascript">
         var editClick;
         $(document).ready(function () {

             var userRole = '<?php echo $userRole;?>';
             var languages = jQuery.parseJSON('<?php echo $languages; ?>');
             var arrLanguages = $.map(languages, function(el) { return el; });

             var theme = 'base';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }
             
             var calculateStats = function () {
                var dataRows = $('#jqxgrid').jqxGrid('getrows');
                var rowscount = dataRows.length;
                $('#no_of_records span').text(rowscount);
                var employeesCount = 0;
                var keyContactsCount = 0;
                var servicesContactsCount = 0;
                for(var i = 0; i < rowscount; i++) {
                        if(!isNaN(parseFloat(dataRows[i].TotalEmployee))) {
                                employeesCount = employeesCount + parseFloat(dataRows[i].TotalEmployee);
                        }
                        if(!isNaN(parseFloat(dataRows[i].TotalEmployee))) {
                                keyContactsCount = keyContactsCount + parseFloat(dataRows[i].totalKeyEmployeeCount);
                        }
                        if(!isNaN(parseFloat(dataRows[i].TotalEmployee))) {
                                servicesContactsCount = servicesContactsCount + parseFloat(dataRows[i].totalServiceEmployeeCount);
                        }
                }
                $('#no_of_employees span').text(Math.round(employeesCount));
                $('#no_of_key_employees span').text(Math.round(keyContactsCount));
                $('#no_of_service_employees span').text(Math.round(servicesContactsCount));
             }
             
             var source =
             {
                dataType: "json",
                id: 'id',
                url: "/reports/get_office_data/",
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
                enableellipsis: false,
                columnmenuopening: function (menu, datafield, height) {
                    var column = $("#jqxgrid").jqxGrid('getcolumn', datafield);
                    if (column.filtertype === "custom") {
                        menu.height(265);
                    }
                    else menu.height(height);
                },
                columns: [
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
                listInput.jqxListBox('checkAll');
                calculateStats();
            });

            $("#loaderWindow").jqxWindow({
                width: 300, resizable: false,  isModal: true, autoOpen: false, maxWidth: 400, maxHeight: 250, showCloseButton: false, keyboardCloseKey: 'none' 
            });
            $('#exporttoexcelbutton').click(function () {
                $("#loaderWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "100px", maxWidth: 400, isModal: true, draggable: false });
                $("#loaderWindow").jqxWindow('open');

                var rows = $("#jqxgrid").jqxGrid('getrows');
                $.ajax({
                    type: "POST",
                    url: "/reports/export_office_data/",
                    data: JSON.stringify(rows),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#loaderWindow").jqxWindow('close');
                            window.open('/files/Office_Data_<?php echo date('m-d-Y'); ?>.xlsx');
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
        <?php
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/office_report') && !preg_match('/Viewer/', $loggedUser['role'])) {
        ?>
            <div id="-reports-client-report" class="light-grey selected">
                    <a href="/reports/office_report">SEARCH</a>
            </div>
        <?php
                }
                if($userAcl->check(array('User' => $loggedUser), 'controllers/reports/office_data')) {
        ?>
            <div id="-reports-client-data" class="light-grey">
                    <a href="/reports/office_data">UPDATE YOUR RECORDS</a>
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
        <div style="margin-right: 7px; margin-bottom: 5px;" align="right">
            <button value="Reset" id="clearfilteringbutton" title="Reset filters">RESET</button>
            <button style="margin-left:5px" value="Export to Excel" id="exporttoexcelbutton">EXPORT .XLS</button>
        </div>
        <div id="jqxgrid"></div>
            <div style='margin-top: 20px;'>
        </div>
        
        <div style="margin-right: 5px; margin-top: 5px; margin-bottom: 10px;" align="right">
                <fieldset style="width: 260px">
                        <legend>Quick stats</legend>
                        <div id="no_of_records" style="padding-bottom: 5px">Number of records <span style="display: inline-block; width: 70px;"></span></div>
                        <div id="no_of_employees" style="padding-bottom: 5px">Employees <span style="display: inline-block; width: 70px;"></span></div>
                        <div id="no_of_key_employees" style="padding-bottom: 5px">Key Management Contacts <span style="display: inline-block; width: 70px;"></span></div>
                        <div id="no_of_service_employees" style="padding-bottom: 5px">Services Contacts <span style="display: inline-block; width: 70px;"></span></div>
                </fieldset>
        </div>
        
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
