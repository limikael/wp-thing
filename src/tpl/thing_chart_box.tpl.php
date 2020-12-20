<?php namespace thing; ?>

<p>
	<select id="thingVarSelect" disabled>
		<?php HtmlUtil::display_select_options($varoptions); ?>
	</select>
	<button disabled id="thingChartPrev" class="button">&lt;&lt; Prev</button>
	<select id="thingChartSelect" disabled>
		<?php HtmlUtil::display_select_options($scopeoptions); ?>
	</select>
	<button disabled id="thingChartNext" class="button">Next &gt;&gt;</button>
</p>

<p>
	<b><span id="thingSpanLabel"></span></b>
</p>

<div id="chartContainer">
	<canvas id="thingChart" width="100" height="50"></canvas>
</div>
<script>
	var thingAjaxUrl="<?php echo esc_js(admin_url('admin-ajax.php')); ?>";
	var thingChartTimestamp=<?php echo $timestamp; ?>;
	var thingVar="<?php echo esc_js($var); ?>";
	var thingPostId="<?php echo esc_js($postId); ?>";
</script>
