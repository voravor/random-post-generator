<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>Post Generator</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-1">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						<div class="inside">
                            
                            <form action="" method="post">
                                
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="num_posts">Number of posts: </label></th>
                                        <td><input class="small-text" type="text" name="num_posts" value="1" /></td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row"><label for="post_format">Post format:</label></th>
                                        <td>
                                            <input type="radio" name="post_format" value="standard" checked />Standard
                                            <input type="radio" name="post_format" value="aside" />Aside
                                            <input type="radio" name="post_format" value="image" />Image
                                            <input type="radio" name="post_format" value="video" />Video
                                            <input type="radio" name="post_format" value="quote" />Quote
                                            <input type="radio" name="post_format" value="link" />Link
                                            <input type="radio" name="post_format" value="gallery" />Gallery

                                        </td>
                                    </tr>

                                     <tr>
                                        <th scope="row"><label for="headers">Maximum number of Comments:</label></th>
                                        <td><input type="text" class="small-text" name="comments" value="10" /></td>
                                     </tr>

                                     <tr>
                                        <th scope="row"><label for="headers">Maximum number of categories:</label></th>
                                        <td><input type="text" class="small-text" name="max_categories" value="1" /></td>
                                     </tr>
                                     
                                     <tr>
                                        <th scope="row"><label for="headers">Maximum number of tags:</label></th>
                                        <td><input type="text" class="small-text" name="max_tags" value="5" /></td>
                                     </tr>

                                </table>
                                
                                <?php submit_button(
                                        'Generate',
                                        $type = 'submit',
                                        $name = 'generate',
                                        $wrap = true,
                                        $other_attributes = null );
                                ?>
                                
                            </form>
						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->
			
			
			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->