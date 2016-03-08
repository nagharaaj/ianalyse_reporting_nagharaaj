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
                for(var i = 0; i < rowscount; i++) {
                        if(!isNaN(parseFloat(dataRows[i].TotalEmployee))) {
                                employeesCount = employeesCount + parseFloat(dataRows[i].TotalEmployee);
                        }
                }
                $('#no_of_employees span').text(Math.round(employeesCount));
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
                height:600,
                autoheight:false,
                enablemousewheel: true,
                source: dataAdapter,
                pageable: true,
                pageSize: 79,
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
                  { text: 'Head of Office', datafield: 'Executive', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of New Business', datafield: 'BusinessHead', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of PPC', datafield: 'Search', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of SEO', datafield: 'SEO', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of Display', datafield: 'Display', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of Affiliates', datafield: 'Affiliates', width: 175, cellClassName: cellclass, align: 'center', filterable: false },
                  { text: 'Head of Content', datafield: 'Content', width: 175, cellClassName: cellclass, align: 'center', filterable: false},
                  { text: 'Head of Data & Insights', datafield: 'Data', width: 175, cellClassName: cellclass, align: 'center', filterable: false ,
                      createfilterpanel: function (datafield, filterPanel) {
                          buildFilterPanel(filterPanel, datafield);
                      }
                  }
                ],
                columngroups: 
                [
                  { text: 'General information', align: 'center', name: 'GeneralInfo' },
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
