<script language="javascript" type="text/javascript">
    function getDocHeight(doc) {
        doc = doc || document;
        var body = doc.body, html = doc.documentElement;
        var height = Math.max( body.scrollHeight, body.offsetHeight, 
        html.clientHeight, html.scrollHeight, html.offsetHeight );
        return height;
    }

    function setIframeHeight(id) {
        var ifrm = document.getElementById(id);
        var doc = ifrm.contentDocument? ifrm.contentDocument: 
        ifrm.contentWindow.document;
        ifrm.style.visibility = 'hidden';
        ifrm.style.height = "10px"; // reset to minimal height ...
        // IE opt. for bing/msn needs a bit added or scrollbar appears
        ifrm.style.height = getDocHeight( doc ) + 4 + "px";
        ifrm.style.visibility = 'visible';
    }
</script>
<div class="visualize-iframe" align="center">
<?php if($loggedUser['role'] == 'Global') { ?>
        <!-- Global View -->
        <iframe id="global_view_frame" src="https://www.ianalysereports.com/index.html#/page/dashboardpage/show?embedpage=d698d7ca-bd80-46dd-ad2b-2c211e08ad83" style="border:0px" width="97%" height="3475px" scrolling="No" onload='javascript:setIframeHeight(this.id);'></iframe>
<?php } else if($loggedUser['role'] == 'Regional') { ?>
<?php   if($userRegion == 'APAC') { ?>
        <!-- Regional View: APAC -->
        <iframe id="global_view_frame" src="https://www.ianalysereports.com/index.html#/page/dashboardpage/show?embedpage=60118f9b-2bae-405b-8bb7-719c2156b116" style="border:0px" width="97%" height="3475px" scrolling="No" onload='javascript:setIframeHeight(this.id);'></iframe>
<?php   } else if($userRegion == 'EMEA') { ?>
        <!-- Regional View: EMEA -->
        <iframe id="global_view_frame" src="https://www.ianalysereports.com/index.html#/page/dashboardpage/show?embedpage=f3529ebb-66c5-49de-a99d-2952875e182c" style="border:0px" width="97%" height="3475px" scrolling="No" onload='javascript:setIframeHeight(this.id);'></iframe>
<?php   } else if($userRegion == 'Latin America') { ?>
        <!-- Regional View: Latin America -->
        <iframe id="global_view_frame" src="https://www.ianalysereports.com/index.html#/page/dashboardpage/show?embedpage=46dc4c56-74fd-427c-b910-142e575d715a" style="border:0px" width="97%" height="3475px" scrolling="No" onload='javascript:setIframeHeight(this.id);'></iframe>
<?php   } else if($userRegion == 'North America') { ?>
        <!-- Regional View: North America -->
        <iframe id="global_view_frame" src="https://www.ianalysereports.com/index.html#/page/dashboardpage/show?embedpage=30e8db45-a2fe-479f-94e2-806f411d6028" style="border:0px" width="97%" height="3475px" scrolling="No" onload='javascript:setIframeHeight(this.id);'></iframe>
<?php   } ?>
<?php } else { ?>
        <!-- Generic View -->
        <iframe id="global_view_frame" src="https://www.ianalysereports.com/index.html#/page/dashboardpage/show?embedpage=b8eac78d-96e6-423c-9b10-8e700da34e8e" style="border:0px" width="97%" height="2845px" scrolling="No" onload='javascript:setIframeHeight(this.id);'></iframe>
<?php } ?>
</div>
