<?php
	$value = 0;
?>
<div class="text-center">
	<h1 class="tick"></h1>
</div>

<script type="text/javascript">

	$( '.tick' ).ticker({
	  incremental: 2,
	  delay: 500,
	  onstart: function(){
	  	alert(1);
	  }
	});

</script>