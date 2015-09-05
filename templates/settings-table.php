<table class="widefat fb_rss-table" id="fb_rss-settings-table">
	<thead>
		<tr>
			<th colspan="5"><?php _e('Settings', 'fb_rss'); ?></th>
		</tr>
	</thead>
	<tbody class="setting-rows">
		<tr class="edit-row show">
			<td colspan="4">
				<table class="widefat edit-table">
					<tr>
						<td>
							<label for="frequency"><?php _e('Frequency', "fb_rss"); ?></label>
							<p class="description"><?php _e('How often will the import run.', "fb_rss"); ?></p>
						</td>
						<td>
							<select name="frequency" id="frequency">
								<?php $x = wp_get_schedules(); ?>
								<?php foreach (array_keys($x) as $interval) : ?>
									<option value="<?php echo $interval; ?>" <?php
									if ($this->options['settings']['frequency'] == $interval) : echo('selected="selected"');
									endif;
									?>><?php echo $x[$interval]['display']; ?></option>
										<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="feeds_api_key"><?php _e('Full Text RSS Feed API Key', "fb_rss"); ?></label>
							<?php if ( ! $this->is_key_valid ) : ?>
							<p class="description">
								<?php _e('Boost Your traffic with Full RSS Content - ', "fb_rss"); ?> 
								Request a Free 14 Days <a href="http://www.feedsapi.com/?utm_source=rsspi-full-rss-key-here" target="_blank"> Full RSS Key Here !</a> 
							</p>
							<?php endif; ?>
						</td>
						<td>
							<?php $feeds_api_key = isset($this->options['settings']["feeds_api_key"]) ? $this->options['settings']["feeds_api_key"] : ""; ?>
							<input type="text" name="feeds_api_key" id="feeds_api_key" value="<?php echo $feeds_api_key; ?>" />
						</td>
					</tr>

					<tr>
						<td>
							<label for="post_template"><?php _e('Template', 'fb_rss'); ?></label>
							<p class="description"><?php _e('This is how the post will be formatted.', "fb_rss"); ?></p>
							<p class="description">
								<?php _e('Available tags:', "fb_rss"); ?>
							<dl>
								<dt><code>&lcub;$content&rcub;</code></dt>
								<dt><code>&lcub;$permalink&rcub;</code></dt>
								<dt><code>&lcub;$title&rcub;</code></dt>
								<dt><code>&lcub;$feed_title&rcub;</code></dt>
								<dt><code>&lcub;$excerpt:n&rcub;</code></dt>
								<dt><code>&lcub;$inline_image&rcub;</code> <small>insert the featured image inline into the post content</small></dt>
							</dl>
							</p>
						</td>
						<td>
							<textarea name="post_template" id="post_template" cols="30" rows="10"><?php
								$value = (
										$this->options['settings']['post_template'] != '' ? $this->options['settings']['post_template'] : '{$content}' . "\nSource: " . '{$feed_title}'
										);

								$value = str_replace(array('\r', '\n'), array(chr(13), chr(10)), $value);

								echo esc_textarea(stripslashes($value));
								?></textarea>
						</td>
					</tr>

				</table>
			</td>
		</tr>
	</tbody>
</table>