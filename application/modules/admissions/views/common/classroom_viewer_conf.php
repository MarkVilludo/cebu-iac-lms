<script type="text/javascript">
    $(document).ready(function(){
       
         $(".Mon").each(function(){
            
            var st = $(this).val();
            var hourspan = $(this).attr('href'); 
            var text = $(this).attr('rel');
            var faculty = $(this).attr('data-faculty');
            $("#"+st+" :nth-child(2)").addClass("bg-teal");
            $("#"+st+" :nth-child(2)").css({'border-top':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
            $("#"+st+" :nth-child(2)").html("<div style='text-align:center;'>"+text+"</div>");
            //$("#"+st+" :nth-child(3)").attr('rowspan',hourspan);
            nxt = $("#"+st);
            nxt.next().children(":nth-child(2)").html("<div style='text-align:center;'>"+faculty+"</div>");
            for(i=1;i<hourspan;i++){
                nxt.next().children(":nth-child(2)").addClass("bg-teal");
                if(i==hourspan-1)
                nxt.next().children(":nth-child(2)").css({'border-top':'none','border-bottom':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
                else
                    nxt.next().children(":nth-child(2)").css({'border-top':'none','border-left':'1px solid #999','border-right':'1px solid #999'});
                
                nxt = nxt.next();
            }
            $("#sched-table").val($("#sched-table-container").html());
            
        });
        
        $(".Tue").each(function(){
            
            var st = $(this).val();
            var hourspan = $(this).attr('href'); 
            var text = $(this).attr('rel');
            var faculty = $(this).attr('data-faculty');
            $("#"+st+" :nth-child(3)").addClass("bg-teal");
            $("#"+st+" :nth-child(3)").css({'border-top':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
            $("#"+st+" :nth-child(3)").html("<div style='text-align:center;'>"+text+"</div>");
            
            //$("#"+st+" :nth-child(3)").attr('rowspan',hourspan);
            nxt = $("#"+st);
            nxt.next().children(":nth-child(3)").html("<div style='text-align:center;'>"+faculty+"</div>");
            for(i=1;i<hourspan;i++){
                nxt.next().children(":nth-child(3)").addClass("bg-teal");
                if(i==hourspan-1)
                nxt.next().children(":nth-child(3)").css({'border-top':'none','border-bottom':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
                else
                    nxt.next().children(":nth-child(3)").css({'border-top':'none','border-left':'1px solid #999','border-right':'1px solid #999'});
                
                nxt = nxt.next();
            }
            $("#sched-table").val($("#sched-table-container").html());
            
        });
        
        $(".Wed").each(function(){
            
            var st = $(this).val();
            var hourspan = $(this).attr('href'); 
            var text = $(this).attr('rel');
            var faculty = $(this).attr('data-faculty');
            $("#"+st+" :nth-child(4)").addClass("bg-teal");
            $("#"+st+" :nth-child(4)").css({'border-top':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
            $("#"+st+" :nth-child(4)").html("<div style='text-align:center;'>"+text+"</div>");
            //$("#"+st+" :nth-child(3)").attr('rowspan',hourspan);
            nxt = $("#"+st);
            nxt.next().children(":nth-child(4)").html("<div style='text-align:center;'>"+faculty+"</div>");
            for(i=1;i<hourspan;i++){
                nxt.next().children(":nth-child(4)").addClass("bg-teal");
                if(i==hourspan-1)
                nxt.next().children(":nth-child(4)").css({'border-top':'none','border-bottom':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
                else
                    nxt.next().children(":nth-child(4)").css({'border-top':'none','border-left':'1px solid #999','border-right':'1px solid #999'});
                
                nxt = nxt.next();
            }
            $("#sched-table").val($("#sched-table-container").html());
            
        });
        
        $(".Thu").each(function(){
            
            var st = $(this).val();
            var hourspan = $(this).attr('href'); 
            var text = $(this).attr('rel');
            var faculty = $(this).attr('data-faculty');
            $("#"+st+" :nth-child(5)").addClass("bg-teal");
            $("#"+st+" :nth-child(5)").css({'border-top':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
            $("#"+st+" :nth-child(5)").html("<div style='text-align:center;'>"+text+"</div>");
            //$("#"+st+" :nth-child(3)").attr('rowspan',hourspan);
            nxt = $("#"+st);
            nxt.next().children(":nth-child(5)").html("<div style='text-align:center;'>"+faculty+"</div>");
            for(i=1;i<hourspan;i++){
                nxt.next().children(":nth-child(5)").addClass("bg-teal");
                if(i==hourspan-1)
                nxt.next().children(":nth-child(5)").css({'border-top':'none','border-bottom':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
                else
                    nxt.next().children(":nth-child(5)").css({'border-top':'none','border-left':'1px solid #999','border-right':'1px solid #999'});
                
                nxt = nxt.next();
            }
            $("#sched-table").val($("#sched-table-container").html());
            
        });
        
        $(".Fri").each(function(){
            
            var st = $(this).val();
            var hourspan = $(this).attr('href'); 
            var text = $(this).attr('rel');
            var faculty = $(this).attr('data-faculty');
            $("#"+st+" :nth-child(6)").addClass("bg-teal");
            $("#"+st+" :nth-child(6)").css({'border-top':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
            $("#"+st+" :nth-child(6)").html("<div style='text-align:center;'>"+text+"</div>");
            //$("#"+st+" :nth-child(3)").attr('rowspan',hourspan);
            nxt = $("#"+st);
            nxt.next().children(":nth-child(6)").html("<div style='text-align:center;'>"+faculty+"</div>");
            for(i=1;i<hourspan;i++){
                nxt.next().children(":nth-child(6)").addClass("bg-teal");
                if(i==hourspan-1)
                nxt.next().children(":nth-child(6)").css({'border-top':'none','border-bottom':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
                else
                    nxt.next().children(":nth-child(6)").css({'border-top':'none','border-left':'1px solid #999','border-right':'1px solid #999'});
                
                nxt = nxt.next();
            }
            $("#sched-table").val($("#sched-table-container").html());
            
        });
        
        $(".Sat").each(function(){
            
            var st = $(this).val();
            var hourspan = $(this).attr('href'); 
            var text = $(this).attr('rel');
            var faculty = $(this).attr('data-faculty');
            $("#"+st+" :nth-child(7)").addClass("bg-teal");
            $("#"+st+" :nth-child(7)").css({'border-top':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
            $("#"+st+" :nth-child(7)").html("<div style='text-align:center;'>"+text+"</div>");
            //$("#"+st+" :nth-child(3)").attr('rowspan',hourspan);
            nxt = $("#"+st);
            nxt.next().children(":nth-child(7)").html("<div style='text-align:center;'>"+faculty+"</div>");
            for(i=1;i<hourspan;i++){
                nxt.next().children(":nth-child(7)").addClass("bg-teal");
                if(i==hourspan-1)
                nxt.next().children(":nth-child(7)").css({'border-top':'none','border-bottom':'1px solid #999','border-left':'1px solid #999','border-right':'1px solid #999'});
                else
                    nxt.next().children(":nth-child(7)").css({'border-top':'none','border-left':'1px solid #999','border-right':'1px solid #999'});
                
                nxt = nxt.next();
            }
            $("#sched-table").val($("#sched-table-container").html());
            
        });
        
        $(".trash-classroom").click(function(e){
                    conf = confirm("Are you sure you want to delete?");
                    if(conf)
                    {
                        $(".loading-img").show();
                        $(".overlay").show();
                        var id = $(this).attr('rel');
                        var parent = $(this).parent().parent().parent().parent().parent();
                        var data = {'table':'tb_mas_users','id':id};
                        $.ajax({
                            'url':'<?php echo base_url(); ?>index.php/classroom/delete_classroom',
                            'method':'post',
                            'data':data,
                            'dataType':'json',
                            'success':function(ret){
                                if(ret.message == "failed"){
                                    $(".alert-page").show();
                                    setTimeout(function() {
                                        $(".alert").hide('fade', {}, 500)
                                    }, 3000);
                                }
                                else
                                    parent.hide();

                                $(".loading-img").hide();
                                $(".overlay").hide();
                        }
                    });
                    }
                });
        
        $("#addSchedBtn").click(function(e){

            subject = $("#subject").val();
            section = $("#section").val();
            strDay = $("#strDay").val();
            intRoomID = $("#intRoomID").val();
            dteStart = $("#dteStart").val();
            dteEnd = $("#dteEnd").val();
            enumClassType = $("#enumClassType").val();
            if(section!=''){
            var data = {'subject':subject,'section':section,'strDay':strDay,'intRoomID':intRoomID,'dteStart':dteStart,'dteEnd':dteEnd,'enumClassType':enumClassType};
            $.ajax({
                'url':'<?php echo base_url(); ?>unity/submit_schedule_ajax',
                'method':'post',
                'data':data,
                'dataType':'json',
                'success':function(ret){
                    if(ret.message != "success"){
                        $("#sched-alert").html('<b>Alert!</b> '+ret.message);
                        $(".alert-modal").show();
                        setTimeout(function() {
                            $(".alert").hide('fade', {}, 500)
                        }, 3000);
                    }
                    else
                        document.location= "<?php echo current_url(); ?>";

            }
            });
            }
            else
            {
                alert("enter a value for section");
            }
        });
        
        $(".trash-schedule").click(function(e){
                    conf = confirm("Are you sure you want to delete?");
                    if(conf)
                    {
                        
                        var id = $(this).attr('rel');
                        var data = {'table':'tb_mas_schedule','id':id};
                        $.ajax({
                            'url':'<?php echo base_url(); ?>index.php/schedule/delete_schedule',
                            'method':'post',
                            'data':data,
                            'dataType':'json',
                            'success':function(ret){
                                if(ret.message == "failed"){
                                    alert("failed to delete");
                                }
                                else
                                    document.location= "<?php echo current_url(); ?>";

                        }
                    });
                    }
                });
        
    });
</script>