<script type="text/javascript">
        $(document).ready(function () {
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
                        $('#newQuestionForm').jqxValidator({ position: 'right', rules: [
                                        { input: '#chapter', message: 'Chapter is required!', rule: function (input) {
                                                if (input.val() != '') {
                                                        return true;
                                                }
                                                return false;
                                            }
                                        },
                                        { input: '#question', message: 'Question is required!', rule: 'required' },
                                        { input: '#answer', message: 'Answer is required!', rule: function (input) {
                                                if (input.val() != '') {
                                                        return true;
                                                }
                                                return false;
                                            }
                                        }
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
                    url: '/help/get_chapters_list',
                    async: false
                };
                var dataAdapterChapters = new $.jqx.dataAdapter(sourceChapterList);
                $("#create_question").click(function () {
                        $("#questionWindow").jqxWindow({ position: { x: 220, y: 'top' }, height: "500px", width: "800px", isModal: true });
                        $("#chapter").jqxDropDownList({ source: dataAdapterChapters, displayMember: "chapterName", valueMember: "chapterId", selectedIndex: -1 });
                        $("#question").jqxInput({ height: 25, width: 550 }).val('');
                        $("#answer").jqxEditor({ tools: 'bold italic underline', width: '100%', height: '300px' });
                        // show the popup window.
                        $("#questionWindow").jqxWindow('open');
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

                        var data = { ChapterId: $("#chapter").val(), Question: $("#question").val(), Answer: $("#answer").val()};

                        $.ajax({
                                type: "POST",
                                url: "/help/save_new_question/",
                                data: JSON.stringify(data),
                                contentType: "application/json; charset=utf-8",
                                dataType: "json",
                                success : function(result) {
                                        if(result.success == true) {
                                                $("#SaveNewQuestion").attr('disabled', false);
                                                $('#newQuestionForm').trigger("reset");
                                                $("#questionWindow").jqxWindow('close');
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
                                <b>Q:</b> <?php echo $question['HelpQuestion']['question']; ?>
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
                        <td align="left" style="padding-bottom: 5px;"><div id="chapter"></div></td>
                        <td style="width: 100px"></td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 100px;">Question</td>
                        <td align="left" style="padding-bottom: 5px;"><input type="text" id="question"/></td>
                        <td style="width: 100px"></td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 100px; vertical-align: top">Answer</td>
                        <td align="left" style="padding-bottom: 5px;"><textarea id="answer"></textarea></td>
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
