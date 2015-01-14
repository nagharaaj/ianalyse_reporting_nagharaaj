    <script type="text/javascript">
         $(document).ready(function () {

             var cities = jQuery.parseJSON('<?php echo $cities; ?>');
             var categories = jQuery.parseJSON('<?php echo $categories; ?>');
             var countries = jQuery.parseJSON('<?php echo $countries; ?>');
             var currencies = jQuery.parseJSON('<?php echo $currencies; ?>');
             var agencies = jQuery.parseJSON('<?php echo $agencies; ?>');
             var services = jQuery.parseJSON('<?php echo $services; ?>');
             var markets = jQuery.parseJSON('<?php echo $markets; ?>');
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

             // Create a jqxDropDownList
             $("#jqxdropdownlist").jqxDropDownList({ source: markets, width: '200px', height: '25px', filterable: true });
             $('#jqxdropdownlist').bind('select', function (event) {
                    var args = event.args;
                    var item = $('#jqxdropdownlist').jqxDropDownList('getItem', args.index);
                    $('#market').text(item.value);
             });


             var theme = 'base';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }

             var source =
            {
                unboundmode: true,
                totalrecords: 10000,
                datafields: [
                    { name: 'ClientName', type: 'string' },
                    { name: 'ParentCompany', type: 'string' },
                    { name: 'ClientCategory', type: 'string' },
                    { name: 'ClientMonth', type: 'string' },
                    { name: 'ClientYear', type: 'number' },
                    { name: 'LeadAgency', type: 'string' },
                    { name: 'Country', type: 'string' },
                    { name: 'City', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Service', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'number' },
                    { name: 'ActualRevenue', type: 'number' },
                ],
                updaterow: function (rowid, rowdata) {
                    // synchronize with the server - send update command   
                }
            };

             var dataAdapter = new $.jqx.dataAdapter(source);
             
             // initialize jqxGrid
             $("#jqxgrid").jqxGrid(
            {
                width: 1250,
                height: 700,
                horizontalscrollbarstep: 300,
                source: dataAdapter,
                editable: true,
                columnsresize: true,
                selectionmode: 'multiplecellsadvanced',
                columns: [
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250 },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250 },
                  { text: 'Client Category', columntype: 'dropdownlist', datafield: 'ClientCategory', width: 200, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: categories });
                        }
                  },
                  { text: 'Client Since Month', columntype: 'dropdownlist', datafield: 'ClientMonth', width: 150, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, source: monthsAdapter, displayMember: 'label', valueMember: 'value' });
                        }
                  },
                  { text: 'Client Since Year', columntype: 'dropdownlist', datafield: 'ClientYear', width: 150, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, source: years });
                        }
                  },
                  { text: 'Lead Agency', columntype: 'dropdownlist', datafield: 'LeadAgency', width: 200, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, source: agencies });
                        }
                  },
                  { text: 'Managing Country', columntype: 'dropdownlist', datafield: 'Country', width: 200, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: countries });
                        }
                  },
                  { text: 'Managing City', columntype: 'dropdownlist', datafield: 'City', width: 200, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: cities });
                        }
                  },
                  { text: 'Active Markets', columntype: 'dropdownlist', checkboxes: true, columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 250, 
                        createeditor: function (row, value, editor) {
                            editor.jqxDropDownList({ source: markets, checkboxes: true });
                        },
                        initeditor: function (row, cellvalue, editor, celltext, pressedkey) {
                          // set the editor's current value. The callback is called each time the editor is displayed.
                          var items = editor.jqxDropDownList('getItems');
                          editor.jqxDropDownList('uncheckAll');
                          var values = cellvalue.split(/,\s*/);
                          for (var j = 0; j < values.length; j++) {
                              for (var i = 0; i < items.length; i++) {
                                  if (items[i].label === values[j]) {
                                      editor.jqxDropDownList('checkIndex', i);
                                  }
                              }
                          }
                        },
                        geteditorvalue: function (row, cellvalue, editor) {
                          // return the editor's value.
                          return editor.val();
                        }
                  },
                  { text: 'Service', columntype: 'dropdownlist', datafield: 'Service', width: 200, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: services });
                        }
                  },
                  { text: 'Currency', columntype: 'dropdownlist', datafield: 'Currency', width: 100, 
                        initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: currencies });
                        }
                  },
                  { text: 'Estimated Annual Revenue', columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 200, align: 'right', cellsalign: 'right' },
                  { text: 'Actual Annual Revenue', columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 200, align: 'right', cellsalign: 'right' },
                ]
            });
            $("#excelSave, #excelExport").jqxButton({ theme: theme });
            $("#excelSave").click(function () {
                //$("#jqxgrid").jqxGrid('exportdata', 'json', 'jqxGrid', true);
                
                var market = $('#market').text();
                if(market == '' || market == null) {
                        alert('Please select market first...');
                        return false;
                }
                
                var rows = [];
                for ( i = 0; i < 10000; i++ ) {
                        var data = $('#jqxgrid').jqxGrid('getrowdata', i);
                        if((data.ClientName != undefined && data.ClientName != '') || (data.ParentCompany != undefined && data.ParentCompany != '')) {
                                rows[rows.length] = data;
                        }
                }
                //console.log(rows);
                $.ajax({
                        type	: "POST",
                        url	: "/ianalyse_reporting/reports/save_data/" + market,
                        data	: JSON.stringify(rows),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success : function(data) {
                                alert("Data saved successfully...");
                        }
                });
            });
            
            $("#excelExport").click(function () {
                var market = $('#market').text();
                if(market == '' || market == null) {
                        alert('Please select market first...');
                        return false;
                }
                
                var rows = [];
                for ( i = 0; i < 10000; i++ ) {
                        var data = $('#jqxgrid').jqxGrid('getrowdata', i);
                        if((data.ClientName != undefined && data.ClientName != '') || (data.ParentCompany != undefined && data.ParentCompany != '')) {
                                rows[rows.length] = data;
                        }
                }
                //console.log(rows);
                $.ajax({
                        type	: "POST",
                        url	: "/ianalyse_reporting/reports/export_data/" + market,
                        data	: JSON.stringify(rows),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success : function(data) {
                                alert("Data saved successfully...");
                        }
                });
            });
        });
    </script>
    <div id='jqxWidget'>
        <div style='margin-top: 20px; margin-bottom: 25px'>
                <div id="jqxdropdownlist"></div>
                <div id="market" style="display: none"></div>
        </div>
                    
        <div id="jqxgrid"></div>
            <div style='margin-top: 20px;'>
            <div style='float: left;'>
                <input type="button" value="Save" id='excelSave' />
                <input type="button" value="Export" id='excelExport' />
            </div>
        </div>
    </div>
