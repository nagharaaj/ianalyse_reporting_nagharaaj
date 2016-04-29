<script type="text/javascript">
        var editClick;
        $(document).ready(function () {
                 $(".que-and-ans").hide();  
                 $(".chapter-head").click(function(){
                       $('.answer').slideUp();
                       $(this).nextAll('div').slideToggle();
                 });
                 $(".question").click(function(){
                       $(this).next('div').slideToggle();
                 });
                 $(".question button").click(function(event){
                  event.stopPropagation();
                 });
                var theme = 'base';
                var userRole = '<?php echo $userRole; ?>';

                if(userRole == 'Global') {
                        $("#create_chapter").jqxButton({ theme: theme });
                        $("#create_question").jqxButton({ theme: theme });
                        $("#list_questions").jqxButton({ theme: theme });

                        $('#newChapterForm').jqxValidator({ position: 'right', rules: [
                                        { input: '#chapter_name', message: 'Chapter name is required!', rule: 'required' }
                                ]
                        });
                } else {
                        $("#new_question").jqxInput({ height: 25, width: "99%" }).val('');
                        $("#user_name").jqxInput({ height: 25, width: "20%" }).val('');
                        $("#submit_question").jqxButton({ theme: theme });

                        $('#testForm').jqxValidator({ position: 'bottom', rules: [
                                        { input: '#new_question', message: 'Please input a question!', rule: 'required' },
                                        { input: '#user_name', message: 'Your name is required!', rule: 'required' },
                                ]
                        });
                }
                $("#chapterWindow").jqxWindow({
                        resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelNewChapter"), maxWidth: 900, maxHeight: 750, showCloseButton: false 
                });
                $("#questionWindow").jqxWindow({
                        resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelNewQuestion"), maxWidth: 900, maxHeight: 750, showCloseButton: false 
                });

                $("#create_chapter").click(function () {
                        $("#chapterWindow").jqxWindow({ position: { x: 220, y: 'top' }, height: "200px", width: "600px", isModal: true });
                        $("#chapter_name").jqxInput({ height: 25, width: 250 }).val('');
                        $("#chapter_description").jqxInput({ height: 25, width: 400 }).val('');
                        // show the popup window.
                        $("#chapterWindow").jqxWindow('open');
                });
                var sourceChapterList =
                {
                    datatype: "json",
                    datafields: [
                        { name: 'chapterId' },
                        { name: 'chapterName' }
                    ],
                     async: false,
                    url: '/help/get_chapters_list'
                };
                var dataAdapterChapters = new $.jqx.dataAdapter(sourceChapterList);
                $("#create_question").click(function () {
                       id = null;
                       openPopup();
                });

                $("#SaveNewChapter").click(function (e) {
                        e.preventDefault();
                        $("#SaveNewChapter").attr('disabled', true);
                        if(!$('#newChapterForm').jqxValidator('validate')) {
                                $("#SaveNewChapter").attr('disabled', false);
                                return false;
                        }

                        var data = { Chapter: $("#chapter_name").val(), Description: $("#chapter_description").val() };

                        $.ajax({
                                type: "POST",
                                url: "/help/save_new_chapter/",
                                data: JSON.stringify(data),
                                contentType: "application/json; charset=utf-8",
                                dataType: "json",
                                success : function(result) {
                                        if(result.success == true) {
                                                $("#SaveNewChapter").attr('disabled', false);
                                                $('#newChapterForm').trigger("reset");
                                                $("#chapterWindow").jqxWindow('close');
                                        } else {
                                                alert(result.errors);
                                                $("#SaveNewChapter").attr('disabled', false);
                                                return false;
                                        }
                                }
                        });
                });

                $("#SaveNewQuestion").click(function (e) {
                        e.preventDefault();
                        $("#SaveNewQuestion").attr('disabled', true);
                        if(!$('#newQuestionForm').jqxValidator('validate')) {
                                $("#SaveNewQuestion").attr('disabled', false);
                                return false;
                        }
                        var url="/help/save_new_question/";
                        if(id){
                                var url="/help/update_question/";
                        }
                        var data = { ChapterId: $("#chapter").val(), Question: $("#question").val(), Answer: $("#answer").val(), id:id };
                        $.ajax({
                                type: "POST",
                                url: url,
                                data: JSON.stringify(data),
                                contentType: "application/json; charset=utf-8",
                                dataType: "json",
                                success : function(result) {
                                        if(result.success == true) {
                                                $("#SaveNewQuestion").attr('disabled', false);
                                                $('#newQuestionForm').trigger("reset");
                                                $("#questionWindow").jqxWindow('close');
                                                if(id) {
                                                        target.parent().parent().find(".que").html(data.Question);
                                                        target.parent().parent().parent().find(".answer").html(data.Answer);
                                                 }
                                        } else {
                                                alert(result.errors);
                                                $("#SaveNewQuestion").attr('disabled', false);
                                                return false;
                                        }
                                }
                        });
                });

                $("#submit_question").click(function (e) {
                        e.preventDefault();
                        $("#submit_question").attr('disabled', true);
                        if(!$('#testForm').jqxValidator('validate')) {
                                $("#submit_question").attr('disabled', false);
                                return false;
                        }

                        var data = { Question: $("#new_question").val(), UserName: $("#user_name").val()};

                        $.ajax({
                                type: "POST",
                                url: "/help/save_user_question/",
                                data: JSON.stringify(data),
                                contentType: "application/json; charset=utf-8",
                                dataType: "json",
                                success : function(result) {
                                        if(result.success == true) {
                                                alert("Thanks for submitting your question");
                                                $("#submit_question").attr('disabled', false);
                                                $('#testForm').trigger("reset");
                                        } else {
                                                alert(result.errors);
                                                $("#submit_question").attr('disabled', false);
                                                return false;
                                        }
                                }
                        });
                });
                
                $("#newQuestionsList").jqxWindow({
                        resizable: false,  isModal: true, autoOpen: false, maxWidth: 900, maxHeight: 900, showCloseButton: true
                });
                
                $("#list_questions").click(function () {
                        $("#newQuestionsList").jqxWindow({ position: { x: 220, y: 'top' }, height: "700px", width: "800px", isModal: true });
                        // show the popup window.
                        $("#newQuestionsList").jqxWindow('open');
                });
                var id;
                var target;
                editClick = function (event) {
                        target = $(event.target);
                        // get button's value.
                        var value = target.val();
                        id = target.attr('data-row');
                        var chapter = target.parent().parent().parent().parent().find('.chapter-head').text();
                        var que = target.parent().parent().find('.que').text();
                        var ans = target.parent().parent().parent().find('.answer').html();
                        var data = { Chapter: chapter, Question: que, Answer: ans };
                        openPopup(data);
                }
            
            function openPopup(rowData) {
               $("#questionWindow").jqxWindow({ position: { x: 220, y: 'top' }, height: "500px", width: "800px", isModal: true });
               if(rowData) {
                        $('#questionWindow').jqxWindow('setTitle', 'Edit');
                        $("#SaveNewQuestion").html('UPDATE');
                } else {
                        $("#SaveNewQuestion").html('SAVE');
                }
                var rules =[];
                if(rowData){
                    $("#divChapter").html('');
                    $("#divChapter").text(rowData.Chapter);
                } else {
                $("#divChapter").html('');
                var inpChapter = $("<div id=\"chapter\"></div>");
                $("#divChapter").append(inpChapter);
                inpChapter.jqxDropDownList({ source :dataAdapterChapters , displayMember :'chapterName', valueMember :'chapterId' }).val(rowData ? rowData.Chapter : '');
                rules.push(validator.chapter);
                }
                $("#divQuestion").html('');
                var inpQuestion = $("<input type='text' id=\"question\">");
                $("#divQuestion").append(inpQuestion);
                inpQuestion.jqxInput({ height: 25, width: 550 });
                $("#question").val(rowData ? rowData.Question : '');
                rules.push(validator.question);
                if($("#divAnswer").html()) {
                        $('#answer').jqxEditor('destroy');
                }
                $("#divAnswer").html('');
                var inpAnswer = $("<textarea id=\"answer\"></textarea>");
                $("#divAnswer").append(inpAnswer);
                inpAnswer.jqxEditor({ tools: 'bold italic underline', width: '100%', height: '300px' });
                $("#answer").val(rowData ? rowData.Answer : '');
                rules.push(validator.answer);
                $('#newQuestionForm').jqxValidator({ position: 'right', rules: rules });
                // show the popup window.
                $("#questionWindow").jqxWindow('open');
            }
            var validator = {
                chapter : {
                        input: '#chapter', message: 'Chapter is required!', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            }
                         },
                question : {
                        input: '#question', message: 'Question is required!', rule: 'required' },
                answer : {
                        input: '#answer', message: 'Answer is required!', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            }
                }
            }
        });
</script>
<div class="help-section">
<?php
        $chapterId = null;
        foreach($questions as $question) {
                if($chapterId != $question['HelpQuestion']['chapter_id']) {
                        if($chapterId != null) {
?>
        </div>
<?php
                        }
?>
        <div class="chapter">
                <div class="chapter-head">
                        <?php echo $question['HelpChapter']['chapter_name']; ?>
                </div>
<?php
                        $chapterId = $question['HelpQuestion']['chapter_id'];
                }
?>
                <div class="que-and-ans">
                        <div class="question">
                                <b>Q:</b> <span class="que"><?php echo $question['HelpQuestion']['question']; ?></span>
                         <?php
                                if($userRole == 'Global') {
                        ?>
                            <div style="float: right"><button data-row="<?php echo $question['HelpQuestion']['id']?>" class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='editClick(event)'>EDIT</button></div>
                        <?php
                                  }
                        ?>
                        </div>
                        <div class="answer">
                                <!--<b>A:</b>--> <?php echo $question['HelpQuestion']['answer']; ?>
                        </div>
                </div>
<?php
        }
?>
        </div>
<?php
        if($userRole == 'Global') {
?>
        <div>
                <button id="create_chapter" class="btn-create-chapter">Create a chapter</button>
                <button id="create_question" class="btn-create-question">Create a question</button><br/><br/>
                <button id="list_questions" class="btn-list-questions">Access the list of questions</button>
        </div>
        <div id="chapterWindow">
            <div>Add a new chapter</div>
            <div style="overflow: hidden;">
            <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelNewChapter" value="Cancel">CANCEL</button></div>
            <form id="newChapterForm" action="./">
                <table>
                    <tr>
                        <td align="right" style="width: 100px;">Chapter Name</td>
                        <td align="left" style="padding-bottom: 5px;"><input type="text" id="chapter_name"/></td>
                        <td style="width: 100px"></td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 100px;">Description</td>
                        <td align="left" style="padding-bottom: 5px;"><input type="text" id="chapter_description"/></td>
                        <td style="width: 100px"></td>
                    </tr>
                </table>
            </form>
            <div style="padding-top: 10px;" align="right"><button style="margin-right: 15px;" id="SaveNewChapter">SAVE</button></div>
            </div>
        </div>
        <div id="questionWindow">
            <div>Add a new question</div>
            <div style="overflow: hidden;">
            <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelNewQuestion" value="Cancel">CANCEL</button></div>
            <form id="newQuestionForm" action="./">
                <table>
                    <tr>
                        <td align="right" style="width: 100px;">Chapter</td>
                        <td align="left" style="padding-bottom: 5px;"><div id="divChapter"></div></td>
                        <td style="width: 100px"></td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 100px;">Question</td>
                        <td align="left" style="padding-bottom: 5px;"><div id="divQuestion"></div></td>
                        <td style="width: 100px"></td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 100px; vertical-align: top">Answer</td>
                        <td align="left" style="padding-bottom: 5px;"><div id="divAnswer"></div></td>
                        <td style="width: 100px"></td>
                    </tr>
                </table>
            </form>
            <div style="padding-top: 10px;" align="right"><button style="margin-right: 15px;" id="SaveNewQuestion">SAVE</button></div>
            </div>
       </div>
       <div id="newQuestionsList">
            <div>Questions asked</div>
            <div style="overflow: hidden;">
               <table border="1" cellspacing="0" cellpadding="2" width="99%">
                       <tr>
                               <td width="7%" style="height:30px" align="center"><b>Sr no.</b></td>
                               <td width="68%" align="center"><b>Question</b></td>
                               <td width="25%" align="center"><b>Submitted by</b></td>
                       </tr>
<?php
                $srNo = 1;
                foreach($newQuestionsList as $newQuestion) {
?>
                       <tr>
                               <td style="height:25px" align="right"><?php echo $srNo; ?></td>
                               <td><?php echo $newQuestion['UserAskedQuestion']['question']; ?></td>
                               <td><?php echo $newQuestion['UserAskedQuestion']['user_name']; ?></td>
                       </tr>
<?php
                        $srNo++;
                }
                if($srNo == 1) {
?>
                       <tr>
                               <td colspan="3" align="center" style="height: 25px">No data available</td>
                       </tr>
<?php
                }
?>
               </table>
            </div>
       </div>
<?php
        } else {
?>
        <div>
                <form id="testForm" action="./">
                        <div class="ask-question">
                                <input type="text" id="new_question" class="inp-new-question" placeholder="If you didn't find an answer to your question, you can ask it here" autocomplete="off">
                                <input type="text" id="user_name" placeholder="Your Name" class="inp-user-name" autocomplete="off">
                                <button id="submit_question" class="btn-submit-question">Submit</button>
                        </div>
                </form>
        </div>
<?php
        }
?>
</div>
