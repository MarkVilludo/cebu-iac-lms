<script src="<?php echo $js_dir; ?>jquery.tokeninput.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(function () {
        
        $("#select-student-number").tokenInput("<?php echo base_url(); ?>unity/userToken/",{"theme":"facebook","tokenLimit":1,"hintText":'Enter Name or Student Number'});
          
      });
    </script>