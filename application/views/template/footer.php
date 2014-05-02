<div class="container">
  <hr>
  <footer>
    <p>&copy; TCFM 2013</p>
  </footer>
</div>
<script type="text/javascript">
	
	function cek() {
		$.post('<?php echo base_url('user/latihan/cek_latihan')?>', null, function(data) {
	      if(data.sukses == true)
	      {
	      	$('.top-right').notify({
			    message: { text: data.pesan },
		        fadeOut: {
		        	delay: 5000
		        }
			}).show();
	      }
	    }, "json");

	    $.post('<?php echo base_url('user/fasilitas/cek_stadion')?>', null, function(data) {
	      if(data.sukses == true)
	      {
	      	$('.top-right').notify({
			    message: { text: data.pesan },
		        fadeOut: {
		        	delay: 5000
		        }
			}).show();
	      }
	    }, "json");

	    setTimeout("cek()", 10000);
	}

	$(document).ready(function() {
		cek();	
	});
</script>
  </body>
</html>