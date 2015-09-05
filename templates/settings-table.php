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
					<tr>
						<td>
							<?php _e('Import your JSON file with your feeds', "fb_rss"); ?>
							<p class="description"><?php _e('Create and Import a JSON file with your Feeds', "fb_rss"); ?></p>
						</td>
						<td>
							<?php
							$disabled = '';
							?>
							<input type="file" name="import_json"<?php echo $disabled; ?> />
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>