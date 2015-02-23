    <script type="text/javascript">
         $(document).ready(function () {

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
                    { name: 'countAffiliates', type: 'string' },
                    { name: 'Conversion', type: 'string' },
                    { name: 'countConversion', type: 'string' },
                    { name: 'Data', type: 'string' },
                    { name: 'countData', type: 'string' },
                    { name: 'Development', type: 'string' },
                    { name: 'countDevelopment', type: 'string' },
                    { name: 'Display', type: 'string' },
                    { name: 'countDisplay', type: 'string' },
                    { name: 'Feeds', type: 'string' },
                    { name: 'countFeeds', type: 'string' },
                    { name: 'Lead', type: 'string' },
                    { name: 'countLead', type: 'string' },
                    { name: 'Mobile', type: 'string' },
                    { name: 'countMobile', type: 'string' },
                    { name: 'RTB', type: 'string' },
                    { name: 'countRTB', type: 'string' },
                    { name: 'Search', type: 'string' },
                    { name: 'countSearch', type: 'string' },
                    { name: 'SEO', type: 'string' },
                    { name: 'countSEO', type: 'string' },
                    { name: 'SocialPaid', type: 'string' },
                    { name: 'countSocialPaid', type: 'string' },
                    { name: 'SocialMangement', type: 'string' },
                    { name: 'countSocialMangement', type: 'string' },
                    { name: 'Strategy', type: 'string' },
                    { name: 'countStrategy', type: 'string' },
                    { name: 'Technology', type: 'string' },
                    { name: 'countTechnology', type: 'string' },
                    { name: 'Video', type: 'string' },
                    { name: 'countVideo', type: 'string' },
                    { name: 'totalServiceEmployeeCount', type: 'number' },
                    { name: 'countSupportedLanguages', type: 'number' },
                    { name: 'SupportedLanguages', type: 'string' },
                    { name: 'RecentAwards', type: 'string' },
                    { name: 'News', type: 'string' }
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
             // initialize jqxGrid
             $("#jqxgrid").jqxGrid(
             {
                width: 1250,
                autoheight: true,
                source: dataAdapter,
                pageable: true,
                pageSize: 20,
                pagerMode: 'simple',
                sortable: true,
                filterable: true,
                showfilterrow: true,
                editable: false,
                autoRowHeight: true,
                selectionmode: 'none',
                columns: [
                  { text: 'RecordId', datafield: 'RecordId', hidden: true },
                  { text: 'Region', columngroup: 'GeneralInfo', datafield: 'Region', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 120 });
                      } 
                  },
                  { text: 'Market', columngroup: 'GeneralInfo', datafield: 'Country', width: 120, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Location Name (City)', columngroup: 'GeneralInfo', datafield: 'City', width: 130, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Year established', columngroup: 'GeneralInfo', datafield: 'YearEstablished', width: 100, cellClassName: cellclass, filtertype: 'checkedlist', cellsalign: 'right', align: 'center', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 150 });
                      } 
                  },
                  { text: 'Total employee', columngroup: 'GeneralInfo', datafield: 'TotalEmployee', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Address', columngroup: 'ContactDetails', datafield: 'Address', width: 250, cellClassName: cellclass, align: 'center' },
                  { text: 'Telephone', columngroup: 'ContactDetails', datafield: 'Telephone', width: 120, cellClassName: cellclass, align: 'center' },
                  { text: 'General email', columngroup: 'ContactDetails', datafield: 'GeneralEmail', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: 'Website', columngroup: 'ContactDetails', datafield: 'Website', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: 'Twitter', columngroup: 'ContactDetails', datafield: 'SocialAccount', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: 'Executive contact', columngroup: 'KeyContacts', datafield: 'Executive', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'KeyContacts', datafield: 'countExecutive', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'CFO or finance lead', columngroup: 'KeyContacts', datafield: 'FinanceHead', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'KeyContacts', datafield: 'countFinanceHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Head of product and services', columngroup: 'KeyContacts', datafield: 'ProductHead', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'KeyContacts', datafield: 'countProductHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Head of strategy', columngroup: 'KeyContacts', datafield: 'StrategyHead', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'KeyContacts', datafield: 'countStrategyHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Head of client services', columngroup: 'KeyContacts', datafield: 'ClientHead', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'KeyContacts', datafield: 'countClientHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'New business', columngroup: 'KeyContacts', datafield: 'BusinessHead', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'KeyContacts', datafield: 'countBusinessHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Marketing', columngroup: 'KeyContacts', datafield: 'MarketingHead', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'KeyContacts', datafield: 'countMarketingHead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Total # employees', datafield: 'totalKeyEmployeeCount', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Affiliates', datafield: 'Affiliates', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Affiliates', datafield: 'countAffiliates', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Content', datafield: 'Content', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Content', datafield: 'countContent', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Conversion', datafield: 'Conversion', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Conversion', datafield: 'countConversion', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Data', datafield: 'Data', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Data', datafield: 'countData', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Development', datafield: 'Development', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Development', datafield: 'countDevelopment', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Display', datafield: 'Display', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Display', datafield: 'countDisplay', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Feeds', datafield: 'Feeds', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Feeds', datafield: 'countFeeds', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Lead', datafield: 'Lead', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Lead', datafield: 'countLead', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Mobile', datafield: 'Mobile', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Mobile', datafield: 'countMobile', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'RTB', datafield: 'RTB', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'RTB', datafield: 'countRTB', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Search', datafield: 'Search', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Search', datafield: 'countSearch', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'SEO', datafield: 'SEO', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'SEO', datafield: 'countSEO', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'SocialPaid', datafield: 'SocialPaid', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'SocialPaid', datafield: 'countSocialPaid', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'SocialManagement', datafield: 'SocialManagement', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'SocialManagement', datafield: 'countSocialManagement', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Strategy', datafield: 'Strategy', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Strategy', datafield: 'countStrategy', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Technology', datafield: 'Technology', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Technology', datafield: 'countTechnology', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Key contact', columngroup: 'Video', datafield: 'Video', width: 150, cellClassName: cellclass, align: 'center' },
                  { text: '# of employee or % FTE', columngroup: 'Video', datafield: 'countVideo', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'Total # employees', datafield: 'totalServiceEmployeeCount', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: '# of supported languages', columngroup: 'Languages', datafield: 'countSupportedLanguages', width: 100, cellClassName: cellclass, cellsalign: 'right', align: 'center' },
                  { text: 'List supported languages', columngroup: 'Languages', datafield: 'SupportedLanguages', width: 200, cellClassName: cellclass, filtertype: 'checkedlist', align: 'center', 
                      createfilterwidget: function (column, columnElement, widget) {
                          widget.jqxDropDownList({ itemHeight: 30, dropDownWidth: 200 });
                      } 
                  },
                  { text: 'Recent awards', columngroup: 'Other', datafield: 'RecentAwards', width: 200, align: 'center' },
                  { text: 'Interesting news', columngroup: 'Other', datafield: 'News', width: 200, align: 'center' }
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
                  { text: 'Languages', align: 'center', name: 'Languages' },
                  { text: 'Other', align: 'center', name: 'Other' }
                ]
                //,
                //ready: calculateStats
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
            //$('#exporttoexcelbutton').jqxButton({ theme: theme });
            // clear the filtering.
            $('#clearfilteringbutton').click(function () {
                $("#jqxgrid").jqxGrid('clearfilters');
            });
            
            /*$('#exporttoexcelbutton').click(function () {
                var rows = $("#jqxgrid").jqxGrid('getrows');
                $.ajax({
                    type: "POST",
                    url: "/reports/export_client_data/",
                    data: JSON.stringify(rows),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            //alert("Data saved successfully...");
                            window.open('/files/Client_Data_<?php echo date('m-d-Y'); ?>.xlsx');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            });*/
            
        });
    </script>

<div id='jqxWidget'>
        <div style="margin-right: 7px; margin-bottom: 5px" align="right">
            <button value="Reset" id="clearfilteringbutton">Reset</button>
        </div>
        <div id="jqxgrid"></div>
            <div style='margin-top: 20px;'>
        </div>
        
        <!--<div style="margin-right: 7px; margin-top: 15px" align="right">
                <button value="Export to Excel" id="exporttoexcelbutton">Export to Excel</button>
        </div>-->
</div>
