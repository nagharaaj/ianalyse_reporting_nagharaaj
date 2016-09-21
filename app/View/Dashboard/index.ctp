<?php if($loggedUserRole != 'Global' && (isset($section_details) && empty($section_details)) && isset($announcement_details) && empty($announcement_details)) {?>
<div style="margin: auto; text-align: center; font-size: 21px; color: #444e53">This page is under construction.</div>
<div style="background-image:url('../img/layout/under_construction.jpg'); background-size: 97%; background-repeat: no-repeat; display: block; height: 700px; width: 700px; margin: auto" align="center"></div>
<?php } else { ?>
<div>
    <div class="announcements-container">
        <div class="announcement-title">
            ANNOUNCEMENTS
        </div>
        <div class="announcement-detail" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>
            <?php if(isset($announcement_details) && !empty($announcement_details)) {
                echo $announcement_details['OverviewAnnouncement']['announcement_details'];
            } else { ?>
            click to add text here...
            <?php } ?>
        </div>
    </div>
<?php if(isset($section_details) && !empty($section_details)) {
    $sectionCnt = 1;
    foreach($section_details as $section_detail) {
        $brandCnt = 1;
?>
    <div class="section-container" sectionId="<?php echo $section_detail['OverviewSection']['id']; ?>" sectionNo="<?php echo $sectionCnt; ?>">
        <div class="btn-remove-section" style="display: none; position: absolute;">Remove section</div>
        <div class="section-title" <?php if($loggedUserRole == 'Global') {?>contenteditable="true"<?php }?>><?php echo $section_detail['OverviewSection']['section_title']; ?></div>
<?php
        if(isset($section_detail['OverviewSectionBrand']) && !empty($section_detail['OverviewSectionBrand'])) {
            foreach($section_detail['OverviewSectionBrand'] as $brand_detail) {
?>
        <div class="brand-container" brandId="<?php echo $brand_detail['id']; ?>" brandNo="<?php echo $brandCnt; ?>">
            <div class="btn-remove-brand" style="display: none; position: absolute;">Remove brand</div>
        <?php if($brand_detail['brand_logo'] != null) {?>
            <div class="brand-logo" <?php if($loggedUserRole == 'Global') {?>title="Click to change logo"<?php }?>><img class="brand-logo-img" src="<?php echo $brand_detail['brand_logo']?>" height="50px" width="180px"></div>
        <?php } else {?>
            <div class="brand-logo" <?php if($loggedUserRole == 'Global') {?>title="Click to upload logo"<?php }?>>LOGO</div>
        <?php }?>
            <div class="brand-client" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>><?php echo $brand_detail['brand_name']; ?></div>
            <div class="brand-service" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>><?php echo $brand_detail['brand_services']; ?></div>
            <div class="brand-market"><?php echo $brand_detail['brand_markets']; ?></div>
            <div class="brand-synopsis">
                <div class="synopsis-title">SYNOPSIS</div>
                <div class="synopsis-detail" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>><?php echo $brand_detail['brand_synopsis']; ?></div>
            </div>
        </div>
<?php
                $brandCnt++;
            }
        } else {
?>
        <div class="brand-container" brandNo="1">
            <div class="brand-logo" <?php if($loggedUserRole == 'Global') {?>title="Click to upload logo"<?php }?>>LOGO</div>
            <div class="brand-client" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>CLIENT NAME</div>
            <div class="brand-service" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>SERVICES</div>
            <div class="brand-market">MARKETS</div>
            <div class="brand-synopsis">
                <div class="synopsis-title">SYNOPSIS</div>
                <div class="synopsis-detail" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>
                    click to add text here...
                </div>
            </div>
        </div>
<?php
        }
        if($loggedUserRole == 'Global' && $brandCnt < 10) {
?>
        <div class="btn-add-brand">Add new brand...</div>
<?php
        }
?>
        </div>
<?php
    }
?>
<?php } else {?>
    <div class="section-container" sectionNo="1">
        <div class="section-title" contenteditable="true">
            TITLE
        </div>
        <div class="brand-container" brandNo="1">
            <div class="brand-logo" <?php if($loggedUserRole == 'Global') {?>title="Click to upload logo"<?php }?>>LOGO</div>
            <div class="brand-client" contenteditable="true">CLIENT NAME</div>
            <div class="brand-service" contenteditable="true">SERVICES</div>
            <div class="brand-market">MARKETS</div>
            <div class="brand-synopsis">
                <div class="synopsis-title">SYNOPSIS</div>
                <div class="synopsis-detail" contenteditable="true">
                    click to add text here...
                </div>
            </div>
        </div>
<?php if($loggedUserRole == 'Global') { ?>
        <div class="btn-add-brand">Add new brand...</div>
<?php } ?>
    </div>
<?php } ?>
<?php if($loggedUserRole == 'Global') { ?>
    <div class="btn-add-section">Add new section...</div>
<?php } ?>
</div>
<script type="text/javascript">
$(document).ready(function () {
    var loggedUserRole = "<?php echo $loggedUserRole; ?>"; // logged user role: page is editable only if role is Global
    var markets = jQuery.parseJSON('<?php echo $markets; ?>'); // markets list for markets selection dropdown
    var arrMarkets = $.map(markets, function(el) { return el.toLowerCase(); });

    if(loggedUserRole == "Global") { // if user is Global attach editable to all the section and brands data
        $('.announcement-detail').jqxEditor({
            tools: 'bold italic underline | left center right | link'
        });

        $('.section-title').jqxEditor({
            tools: ''
        });

        $('.brand-client').jqxEditor({
            tools: ''
        });
        $('.brand-service').jqxEditor({
            tools: ''
        });
        $('.synopsis-detail').jqxEditor({
            tools: ''
        });
    }

    // function to add new brand html under a section
    var brandClick = function () {
        var brandElm = $(this).parent().find('.brand-container:last');
        var brandCnt = brandElm.attr('brandNo');
        brandCnt++;
        var newBrandElm = brandElm.clone();
        newBrandElm.attr('brandNo', brandCnt);
        brandElm.after(newBrandElm);
        if(brandCnt >= 10) {
            $(this).hide();
        }

        $(newBrandElm).find('.brand-logo').empty().text('LOGO').on('click', addBrandLogo);
        $(newBrandElm).find('.brand-client').text('CLIENT NAME').jqxEditor({
            tools: ''
        });
        $(newBrandElm).find('.brand-service').text('SERVICES').jqxEditor({
            tools: ''
        });
        $(newBrandElm).find('.brand-market').text('MARKETS');
        $(newBrandElm).find('.synopsis-detail').text('click to add text here...').jqxEditor({
            tools: ''
        });
        $(newBrandElm).find('.brand-client, .brand-service').on('change', saveBrandDetail);
        $(newBrandElm).find('.brand-market').bind('click', brandMarketSelection);
        $(newBrandElm).find('.brand-market').bind('contentchanged', saveBrandDetail);
        $(newBrandElm).find('.synopsis-detail').on('change', saveBrandSynopsis);
        $(newBrandElm).attr('brandId', '');
        $(newBrandElm).find('.btn-remove-brand').on('click', removeBrand);
    }
    $('.btn-add-brand').click(brandClick);

    // function to add new section html
    $('.btn-add-section').click(function () {
        var sectionElm = $(this).parent().find('.section-container:last');
        var sectionCnt = sectionElm.attr('sectionNo');
        sectionCnt++;
        var newSectionElm = sectionElm.clone();
        newSectionElm.attr('sectionNo', sectionCnt);
        $(newSectionElm).find('.brand-container').slice(1).remove();
        sectionElm.after(newSectionElm);

        $(newSectionElm).find('.btn-add-brand').show().bind('click', brandClick);

        $(newSectionElm).find('.section-title').text('TITLE').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.btn-remove-section').on('click', removeSection);

        $(newSectionElm).find('.brand-logo').on('click', addBrandLogo);
        $(newSectionElm).find('.brand-client').text('CLIENT NAME').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.brand-service').text('SERVICES').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.brand-market').text('MARKETS');
        $(newSectionElm).find('.synopsis-detail').text('click to add text here...').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.section-title').on('change', saveSection);
        $(newSectionElm).find('.brand-client, .brand-service').on('change', saveBrandDetail);
        $(newSectionElm).find('.brand-market').bind('click', brandMarketSelection);
        $(newSectionElm).find('.brand-market').bind('contentchanged', saveBrandDetail);
        $(newSectionElm).find('.synopsis-detail').on('change', saveBrandSynopsis);
        $(newSectionElm).attr('sectionId', '');
        $(newSectionElm).find('.brand-container').attr('brandId', '');
    });

    // save annoucement details when changed
    $('.announcement-detail').on('change', function (event) {
        $.ajax({
            type: "POST",
            url: '/dashboard/save_announcements',
            data: JSON.stringify({
                announcement: $(".announcement-detail").html()
            }),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success : function(result) {
                if(result.success == true) {
                    return true;
                } else {
                    alert(result.errors);
                    return false;
                }
            }
        });
    });

    // save section details when changed
    var saveSection = function (event) {
        var sectionContainer = $(this).parent();
        var sectionTitle = $(this).text();
        var sectionId = $(sectionContainer).attr('sectionId');
        var sectionNo = $(sectionContainer).attr('sectionNo');

        var brandData = new Array();
        $(sectionContainer).find('.brand-container').each(function () {
            var brandId = $(this).attr('brandId');
            var brandNo = $(this).attr('brandNo');
            var clientName = $(this).find('.brand-client').text();
            var services = $(this).find('.brand-service').text();
            var markets = $(this).find('.brand-market').text();
            var synopsis = $(this).find('.synopsis-detail').text();

            brandData[brandNo] = {
                brandId: brandId,
                brandNo: brandNo,
                clientName: clientName,
                services: services,
                markets: markets,
                synopsis: synopsis
            };
        });

        var data = {
            sectionId: sectionId,
            sectionNo: sectionNo,
            sectionTitle: sectionTitle,
            brandData: brandData
        };
        $.ajax({
            type: "POST",
            url: '/dashboard/save_section_data',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success : function(result) {
                if(result.success == true) {
                    $(sectionContainer).attr('sectionId', result.sectionId);
                    return true;
                } else {
                    alert(result.errors);
                    return false;
                }
            }
        });
    }
    $('.section-title').on('change', saveSection);

    // save all brands details under section when any of the brand detail is changed
    var saveBrandDetail = function (event) {
        var sectionContainer = $(this).parent().parent();
        var sectionTitle = $(sectionContainer).find('.section-title').text();
        var sectionId = $(sectionContainer).attr('sectionId');
        var sectionNo = $(sectionContainer).attr('sectionNo');

        var brandData = new Array();
        var index = 0;
        $(sectionContainer).find('.brand-container').each(function () {
            var brandId = $(this).attr('brandId');
            var brandNo = $(this).attr('brandNo');
            var clientName = $(this).find('.brand-client').text();
            var services = $(this).find('.brand-service').text();
            var markets = $(this).find('.brand-market').text();
            var synopsis = $(this).find('.synopsis-detail').text();

            brandData[index] = {
                brandId: brandId,
                brandNo: brandNo,
                clientName: clientName,
                services: services,
                markets: markets,
                synopsis: synopsis
            };
            index++;
        });

        var data = {
            sectionId: sectionId,
            sectionNo: sectionNo,
            sectionTitle: sectionTitle,
            brandData: brandData
        };
        $.ajax({
            type: "POST",
            url: '/dashboard/save_section_data',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success : function(result) {
                if(result.success == true) {
                    $(sectionContainer).attr('sectionId', result.sectionId);
                    $.each(result.brandIds, function (index, value) {
                        $(sectionContainer).find('.brand-container[brandNo='+index+']').attr('brandId', value);
                    });
                    return true;
                } else {
                    alert(result.errors);
                    return false;
                }
            }
        });
    }
    $('.brand-client, .brand-service').on('change', saveBrandDetail);

    // save brand synopsis details
    var saveBrandSynopsis = function (event) {
        var sectionContainer = $(this).parent().parent().parent();
        var sectionTitle = $(sectionContainer).find('.section-title').text();
        var sectionId = $(sectionContainer).attr('sectionId');
        var sectionNo = $(sectionContainer).attr('sectionNo');

        var brandData = new Array();
        $(sectionContainer).find('.brand-container').each(function () {
            var brandId = $(this).attr('brandId');
            var brandNo = $(this).attr('brandNo');
            var clientName = $(this).find('.brand-client').text();
            var services = $(this).find('.brand-service').text();
            var markets = $(this).find('.brand-market').text();
            var synopsis = $(this).find('.synopsis-detail').text();

            brandData[brandNo] = {
                brandId: brandId,
                brandNo: brandNo,
                clientName: clientName,
                services: services,
                markets: markets,
                synopsis: synopsis
            };
        });

        var data = {
            sectionId: sectionId,
            sectionNo: sectionNo,
            sectionTitle: sectionTitle,
            brandData: brandData
        };
        $.ajax({
            type: "POST",
            url: '/dashboard/save_section_data',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success : function(result) {
                if(result.success == true) {
                    $(sectionContainer).attr('sectionId', result.sectionId);
                    return true;
                } else {
                    alert(result.errors);
                    return false;
                }
            }
        });
    }
    $('.synopsis-detail').on('change', saveBrandSynopsis);

    // save brand logo images
    var addBrandLogo = function (event) {
        $(this).unbind( "click" );
        var logoContainer = $(this);
        // Variable to store your files
        var files;
        var brandId = $(this).parent().attr('brandId');
        var sectionId = $(this).parent().parent().attr('sectionId');

        if(brandId == '' || brandId == undefined) {
            alert('Enter CLIENT NAME first...!');
            $(this).on('click', addBrandLogo);
            return false;
        }

        $(logoContainer).empty();
        $(logoContainer).append('<form id="logoUploadForm" action="#" enctype="multipart/form-data" method="post"></form>');
        $('#logoUploadForm').append('<input type="file" name="logo_image" id="logo_image" accept="image/*">');
        $('#logoUploadForm').append('<button id="save-logo-upload">Upload</button>');
        $('#logoUploadForm').append('<button id="cancel-logo-upload">Cancel</button>');

        $('#cancel-logo-upload').on('click', function (event) {
            event.stopPropagation(); // Stop stuff happening
            event.preventDefault(); // Totally stop stuff happening
            $(logoContainer).empty();
            $(logoContainer).text('LOGO');
            $('.brand-logo').on('click', addBrandLogo);
        });

        // Add events
        $('#logo_image').on('change', prepareUpload);
        // Grab the files and set them to our variable
        function prepareUpload (event) {
            files = event.target.files;
        }

        $('#save-logo-upload').on('click', function (event) {
            event.stopPropagation(); // Stop stuff happening
            event.preventDefault(); // Totally stop stuff happening

            // Create a formdata object and add the files
            var data = new FormData();
            $.each(files, function(key, value) {
                data.append(key, value);
            });
            $.ajax({
                url: "/dashboard/brand_logo_upload?sectionId=" + sectionId + "&brandId=" + brandId, // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data: data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                dataType: 'json',
                processData:false,        // To send DOMDocument or non processed data file it is set to false
                success: function(data)   // A function to be called if request succeeds
                {
                    if(data.success) {
                        alert(data.success);
                        $(logoContainer).empty();
                        $(logoContainer).append('<img class="brand-logo-img" height="50px" width="180px">');
                        $(logoContainer).find('.brand-logo-img').attr('src', data.filepath);
                        $(logoContainer).find('.brand-logo').on('click', addBrandLogo);
                    } else {
                        alert(data.error);
                    }
                }
            });
        });
    }
    if(loggedUserRole == "Global") {
        $('.brand-logo').on('click', addBrandLogo);
    }

    // show remove brand/section button on hover if user role is Global
    if(loggedUserRole == "Global") {
        $(document).on('mouseenter', '.brand-container', function () {
            $(this).find(".btn-remove-brand").show(700);
        }).on('mouseleave', '.brand-container', function () {
            $(this).find(".btn-remove-brand").hide(700);
        });

        $(document).on('mouseenter', '.section-container', function () {
            $(this).find(".btn-remove-section").show(700);
        }).on('mouseleave', '.section-container', function () {
            $(this).find(".btn-remove-section").hide(700);
        });
    }
    // function to remove a brand under section
    var removeBrand = function (event) {
        var brandContainer = $(this).parent();
        var sectionContainer = $(brandContainer).parent();
        var brandId = $(brandContainer).attr('brandid');
        var sectionId = $(sectionContainer).attr('sectionid');

        if(brandId == '' || brandId == undefined) {
            $(brandContainer).remove();
        } else {
            if(confirm('Are you sure?')) {
                var data = {
                    sectionId: sectionId,
                    brandId: brandId
                }
                $.ajax({
                    type: "POST",
                    url: '/dashboard/remove_brand',
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $(brandContainer).remove();
                            $.each(result.brandCnts, function (index, value) {
                                $(sectionContainer).find('.brand-container[brandid='+index+']').attr('brandno', value);
                            });
                            return true;
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            }
        }
    }
    if(loggedUserRole == "Global") {
        $(".btn-remove-brand").on('click', removeBrand);
    }

    // function to remove entire section
    var removeSection = function (event) {
        var sectionContainer = $(this).parent();
        var sectionId = $(sectionContainer).attr('sectionid');

        if(sectionId == '' || sectionId == undefined) {
            $(sectionContainer).remove();
        } else {
            if(confirm('Are you sure?')) {
                var data = {
                    sectionId: sectionId
                }
                $.ajax({
                    type: "POST",
                    url: '/dashboard/remove_section',
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $(sectionContainer).remove();
                            return true;
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            }
        }
    }
    if(loggedUserRole == "Global") {
        $(".btn-remove-section").on('click', removeSection);
    }

    /*
    * function for generating dropdown for selecting markets
    */
    var brandMarketSelection = function(event) {
        var clickedBrandMarket = this; // variable to store the object of div on which user has clicked

        var country = $(clickedBrandMarket).text(); // get the existing markets from the div
        var eleWidth = $(clickedBrandMarket).width(); // div width to assign to dropdown
        $(clickedBrandMarket).html('');
        var inpEntity = $("<div id=\"brandMarketDropDown\"></div>"); // create element for dropdown
        $(clickedBrandMarket).append(inpEntity); // attach the dropdown div to the market element user has clicked
        $("#brandMarketDropDown").on('bindingComplete', function (event) {
            $(clickedBrandMarket).unbind('click', brandMarketSelection); // remove the click event to stop event propogation when user selects markets from dropdown
        });
        $("#brandMarketDropDown").on('close', function (event) {
            var checkedItems = "";
            var items = $("#brandMarketDropDown").jqxDropDownList('getCheckedItems');
                $.each(items, function (index) {
                    checkedItems += (checkedItems) ? ", " + this.label : this.label;
            });
            $("#brandMarketDropDown").jqxDropDownList('destroy'); // remove the dropdown when user closes it
            $(clickedBrandMarket).html(checkedItems ? checkedItems : "MARKETS"); // show selected markets in the market element for the brand
            $(clickedBrandMarket).bind('click', brandMarketSelection);
            $(clickedBrandMarket).trigger('contentchanged');
        });
        $("#brandMarketDropDown").jqxDropDownList({ source: markets, checkboxes: true, width: eleWidth+"px" });
        // auto select the existing markets in the dropdown
        if(country && country != "MARKETS") {
            var arrCountry = country.split(', ');
            for(var key in arrCountry) {
                index = arrMarkets.indexOf(arrCountry[key].toLowerCase());
                if(index != -1) {
                   $("#brandMarketDropDown").jqxDropDownList('checkIndex', index);
                }
            }
        }
        $("#brandMarketDropDown").jqxDropDownList('open');
    }
    $('.brand-market').bind('click', brandMarketSelection);
     // attach custom change event to market div to save data when user selects markets from dropdown
    $('.brand-market').bind('contentchanged', saveBrandDetail);
});
</script>
<?php } ?>
