<div class="wrap">
	<form method="post" action="options.php">
	    <?php settings_fields( 'thing' ); ?>
	    <?php do_settings_sections( 'thing' ); ?>

	    <h1>Thing Settings</h1>

	    <table class="form-table">
	    	<tr>
	    		<th scope="row">Broker URL</th>
	    		<td>
	    			<input type="text" class="regular-text"
	    				name="thing_brokerurl"
	    				value="<?php echo esc_attr(get_option("thing_brokerurl")); ?>">
	    			<p class="description">
	    				The restbroker url used to communicate with the things.
	    			</p>
	    		</td>
	    	</tr>

	    	<tr>
	    		<th scope="row">Api Key</th>
	    		<td>
	    			<input type="text" class="regular-text"
	    				name="thing_apikey"
	    				value="<?php echo esc_attr(get_option("thing_apikey")); ?>">
	    			<p class="description">
	    				Used when connecting to the restbroker, and
	    				required for things to connect to the site.
	    			</p>
	    		</td>
	    	</tr>
	    </table>

	    <?php submit_button(); ?>
	</form>
</div>