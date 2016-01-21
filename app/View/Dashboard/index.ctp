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
        <div class="section-title" <?php if($loggedUserRole == 'Global') {?>contenteditable="true"<?php }?>><?php echo $section_detail['OverviewSection']['section_title']; ?></div>
<?php
        if(isset($section_detail['OverviewSectionBrand']) && !empty($section_detail['OverviewSectionBrand'])) {
            foreach($section_detail['OverviewSectionBrand'] as $brand_detail) {
?>
        <div class="brand-container" brandId="<?php echo $brand_detail['id']; ?>" brandNo="<?php echo $brandCnt; ?>">
        <?php if($brand_detail['brand_logo'] != null) {?>
            <div class="brand-logo" title="Click to change logo"><img class="brand-logo-img" src="<?php echo $brand_detail['brand_logo']?>" height="50px" width="180px"></div>
        <?php } else {?>
            <div class="brand-logo" title="Click to upload logo">LOGO</div>
        <?php }?>
            <div class="brand-client" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>><?php echo $brand_detail['brand_name']; ?></div>
            <div class="brand-service" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>><?php echo $brand_detail['brand_services']; ?></div>
            <div class="brand-market" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>><?php echo $brand_detail['brand_markets']; ?></div>
            <div class="brand-synopsis">
                <div class="synopsis-title">SYNOPSIS</div>
                <div class="synopsis-detail" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>><?php echo $brand_detail['brand_synopsis']; ?></div>
            </div>
        </div>
<?php
                $brandCnt++;
            }
            if($loggedUserRole == 'Global' && $brandCnt < 10) {
?>
        <div class="btn-add-brand">Add new brand...</div>
<?php
            }
        } else {
?>
        <div class="brand-container" brandNo="1">
            <div class="brand-logo" title="Click to upload logo">LOGO</div>
            <div class="brand-client" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>CLIENT NAME</div>
            <div class="brand-service" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>SERVICES</div>
            <div class="brand-market" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>MARKETS</div>
            <div class="brand-synopsis">
                <div class="synopsis-title">SYNOPSIS</div>
                <div class="synopsis-detail" <?php if($loggedUserRole == 'Global') {?> contenteditable="true"<?php }?>>
                    click to add text here...
                </div>
            </div>
        </div>
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
            <div class="brand-logo" title="Click to upload logo">LOGO</div>
            <div class="brand-client" contenteditable="true">CLIENT NAME</div>
            <div class="brand-service" contenteditable="true">SERVICES</div>
            <div class="brand-market" contenteditable="true">MARKETS</div>
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
<script>
$(document).ready(function () {
    var loggedUserRole = "<?php echo $loggedUserRole; ?>";

    if(loggedUserRole == "Global") {
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
        $('.brand-market').jqxEditor({
            tools: ''
        });
        $('.synopsis-detail').jqxEditor({
            tools: ''
        });
    }

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

        $(newBrandElm).find('.brand-logo').on('click', addBrandLogo);
        $(newBrandElm).find('.brand-client').text('CLIENT NAME').jqxEditor({
            tools: ''
        });
        $(newBrandElm).find('.brand-service').text('SERVICES').jqxEditor({
            tools: ''
        });
        $(newBrandElm).find('.brand-market').text('MARKETS').jqxEditor({
            tools: ''
        });
        $(newBrandElm).find('.synopsis-detail').text('click to add text here...').jqxEditor({
            tools: ''
        });
        $(newBrandElm).find('.brand-client, .brand-service, .brand-market').on('change', saveBrandDetail);
        $(newBrandElm).find('.synopsis-detail').on('change', saveBrandSynopsis);
        $(newBrandElm).attr('brandId', '');
    }

    $('.btn-add-brand').click(brandClick);

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

        $(newSectionElm).find('.brand-logo').on('click', addBrandLogo);
        $(newSectionElm).find('.brand-client').text('CLIENT NAME').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.brand-service').text('SERVICES').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.brand-market').text('MARKETS').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.synopsis-detail').text('click to add text here...').jqxEditor({
            tools: ''
        });
        $(newSectionElm).find('.section-title').on('change', saveSection);
        $(newSectionElm).find('.brand-client, .brand-service, .brand-market').on('change', saveBrandDetail);
        $(newSectionElm).find('.synopsis-detail').on('change', saveBrandSynopsis);
        $(newSectionElm).attr('sectionId', '');
        $(newSectionElm).find('.brand-container').attr('brandId', '');
    });

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

    /*$('.brand-logo').on('click', function (event) {
        $(this).jqxFileUpload({ width: 180, uploadUrl: '/dashboard/upload_brand_logo', fileInputName: 'fileToUpload', 'autoUpload': true, 'accept': 'image/*' });
    });*/

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

    var saveBrandDetail = function (event) {
        var sectionContainer = $(this).parent().parent();
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
    $('.brand-client, .brand-service, .brand-market').on('change', saveBrandDetail);

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

    var addBrandLogo = function (event) {
        $(this).unbind( "click" );
        var logoContainer = $(this);
        // Variable to store your files
        var files;
        var brandId = $(this).parent().attr('brandId');
        var sectionId = $(this).parent().parent().attr('sectionId');

        if(brandId == '') {
            alert('Enter CLIENT NAME first...!');
            return false;
        }

        $(logoContainer).empty();
        $(logoContainer).append('<form id="logoUploadForm" action="#" enctype="multipart/form-data" method="post"></form>');
        $('#logoUploadForm').append('<input type="file" name="logo_image" id="logo_image" accept="image/*">');
        $('#logoUploadForm').append('<button id="save-logo-upload">Upload</button>');
        $('#logoUploadForm').append('<button id="cancel-logo-upload">Cancel</button>');

        $('#cancel-logo-upload').on('click', function (event) {
                event.stopPropagation();
                $(logoContainer).empty();
                $(logoContainer).text('LOGO');
                $('.brand-logo').on('click', addBrandLogo);
        });

        // Add events
        $('#logo_image').on('change', prepareUpload);
        // Grab the files and set them to our variable
        function prepareUpload(event) {
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
                    //console.log(data);
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
    $('.brand-logo').on('click', addBrandLogo);
});
</script>
<?php } ?>
