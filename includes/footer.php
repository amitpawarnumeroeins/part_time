<footer class="app-footer">
      <div class="row">
        <div class="col-xs-12">
          <div class="footer-copyright">Copyright © <?php echo date('Y');?> <a href="https://numeroeins.com/" target="_blank">NumeroEins</a>. All Rights Reserved.</div>
        </div>
      </div>
    </footer>
  </div>
</div>
<script type="text/javascript" src="assets/js/vendor.js"></script> 
<script type="text/javascript" src="assets/js/app.js"></script>

<!-- For Bootstrap Tags -->
<script type="text/javascript" src="assets/bootstrap-tag/bootstrap-tagsinput.js"></script>
<!-- End -->
<script type="text/javascript" src="assets/sweetalert/sweetalert.min.js"></script>    

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="assets/js/notify.min.js"></script>

<script>
  $( ".datepicker" ).datepicker({
      dateFormat: 'd-m-yy'
    });
  </script>

  
<script>
 $("#checkall").click(function () {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
</script>

<?php if(isset($_SESSION['msg'])){?>
<script type="text/javascript">
  $('.notifyjs-corner').empty();
  $.notify(
    '<?php echo $client_lang[$_SESSION["msg"]];?>',
    { position:"top center",className: '<?=$_SESSION["class"]?>'}
  );
</script>
<?php
  unset($_SESSION['msg']);
  unset($_SESSION['class']); 
  } 
?>
</body>
</html>


