<?php
/**
 * Plugin Name: Upload User Avatars
 * Description: Enables upload of local avatar from admin or frontend
 * Version:     1.0.0
 * Author:      Tim Kaye
 * Author URI:  https://timkaye.org
 * Text domain: upload-user-avatars
 */

/**
 * Add settings to Discussion page in admin
 */
function kts_avatar_admin_init() {	

	register_setting( 'discussion', 'upload_user_avatars_caps', 'kts_avatar_checkbox_options' );
	add_settings_field( 'upload-user-avatars-caps', __( 'Local Avatar Permissions', 'upload-user-avatars' ), 'kts_avatar_settings_field', 'discussion', 'avatars', array( 'label_for' => 'upload_user_avatars_caps' ) );

	register_setting( 'discussion', 'upload_user_avatars_folder', 'sanitize_text_field' );
	add_settings_field( 'upload-user-avatars-folder', __( 'Choose Avatar Folder', 'upload-user-avatars' ), 'kts_avatar_uploads_folder', 'discussion', 'avatars', array( 'label_for' => 'upload_user_avatars_folder' ) );

}
add_action( 'admin_init', 'kts_avatar_admin_init' );

/**
 * Discussion settings option
 */
function kts_avatar_settings_field( $args ) {
	$caps = get_option( 'upload_user_avatars_caps' ); ?>

	<input type="checkbox" name="upload_user_avatars_caps" id="upload_user_avatars_caps" <?php if ( ! empty( $caps ) ) { echo 'checked'; } ?>>

	<?php _e( 'Only allow users with file upload capabilities (authors and above) to upload local avatars', 'upload-user-avatars' );
}

/**
 * Decide whether to limit who can upload an avatar to just authors and above
 */
function kts_avatar_checkbox_options( $input ) {
	return empty( $_POST['upload_user_avatars_caps'] ) ? 0 : 1;
}

/**
 * Avatar uploads folder choice
 */
function kts_avatar_uploads_folder( $args ) {
	$folder = get_option( 'upload_user_avatars_folder' ); ?>

	<?php _e( '<p>Choose the folder to which you want avatars to be uploaded, relative to the uploads folder. If the folder does not exist, it will be created.</p>', 'upload-user-avatars' ); ?>

	<input type="text" class="regular-text" name="upload_user_avatars_folder" id="upload_user_avatars_folder" value="<?php echo esc_attr( $folder ); ?>"> &nbsp;

	<?php _e( '<p>You might, for example, type <code>avatars</code> to have avatars uploaded to the <code>/uploads/avatars</code> folder, or <code>users/avatars</code> to have avatars uploaded to the <code>/uploads/users/avatars</code> folder.</p>', 'upload-user-avatars' );
	_e( '<p>Leave blank to use the uploads folder itself (or month- and year-based folders if you have selected that option on the Media Settings page).</p>', 'upload-user-avatars' );
}

/**
 * Sanitize the uploads folder choice
 */
function kts_avatar_sanitize_folders( $input ) {
	return sanitize_text_field( $_POST['upload_user_avatars_folder'] );	 
}

/**
 * Change the upload folder for avatars
 */
function kts_avatar_change_upload_dir( $dirs ) {
	$folder = get_option( 'upload_user_avatars_folder' );
	$folder = ltrim( $folder, '/' );
	$folder = rtrim( $folder, '/' );
	if ( ! empty( $folder ) ) {
		$dirs['subdir'] = '/' . $folder;
		$dirs['path'] = $dirs['basedir'] . '/' . $folder;
		$dirs['url'] = $dirs['baseurl'] . '/' . $folder;
	}
    return $dirs;
}

/**
 * Filter the avatar returned
 */
function kts_get_avatar( $avatar = '', $id_or_email, $size = 96, $default = '', $alt = false ) {

	# Determine if we receive an ID, user object, or email
	if ( is_numeric( $id_or_email ) ) {
		$user_id = (int) $id_or_email;
	}
	else if ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
		$user_id = (int) $id_or_email->user_id;
	}
	else if ( is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
		if ( ! empty( $user ) ) {
			$user_id = $user->ID;
		}
	}
	else { // no user ID
		return $avatar;
	}

	$local_avatars = get_user_meta( $user_id, 'upload_user_avatar', true );

	if ( empty( $local_avatars ) || empty( $local_avatars['full'] ) ) {
		return $avatar;
	}

	# Generate a new size
	$size = (int) $size;

	if ( ! array_key_exists( $size, $local_avatars ) ) {
		$local_avatars[$size] = $local_avatars['full']; // in case of failure elsewhere

		$upload_path = wp_upload_dir();

		# Get path for image by converting URL
		$avatar_full_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $local_avatars['full'] );

		# Generate the new size
		$editor = wp_get_image_editor( $avatar_full_path );
		if ( ! is_wp_error( $editor ) ) {
			$resized = $editor->resize( $size, $size, true );
			if ( ! is_wp_error( $resized ) ) {
				$dest_file = $editor->generate_filename();
				$saved = $editor->save( $dest_file );
				if ( ! is_wp_error( $saved ) ) {
					$local_avatars[$size] = str_replace( $upload_path['basedir'], $upload_path['baseurl'], $dest_file );
				}
			}
		}

		# Save updated avatar sizes
		update_user_meta( $user_id, 'upload_user_avatar', $local_avatars );
	}

	if ( substr( $local_avatars[$size], 0, 4 ) !== 'http' ) {
		$local_avatars[$size] = home_url( $local_avatars[$size] );
	}

	if ( empty( $alt ) ) {
		$alt = get_the_author_meta( 'display_name', $user_id );
	}

	$author_class = is_author( $user_id ) ? ' current-author' : '' ;
	$avatar = '<img alt="' . esc_attr( $alt ) . '" src="' . set_url_scheme($local_avatars[$size] ) . '" class="avatar avatar-' . $size . $author_class . ' photo" height="' . $size . '" width="' . $size . '" />';

	return apply_filters( 'upload_user_avatar', $avatar );
}
add_filter( 'get_avatar', 'kts_get_avatar', 10, 5 );

/**
 * Form to display on the user profile edit screen
 */
function kts_avatar_user_profile( $user ) { ?>

	<h3><?php _e( 'Avatar', 'upload-user-avatars' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="upload-user-avatar"><?php _e( 'Upload Avatar', 'upload-user-avatars' ); ?></label></th>
			<td style="width: 50px;" valign="top">
				<?php echo get_avatar( $user->ID ); ?>
			</td>
			<td> <?php

			$caps = get_option( 'upload_user_avatars_caps' );
			$avatar = get_user_meta( $user->ID, 'upload_user_avatar', true );

			if ( empty( $caps ) || current_user_can( 'upload_files' ) ) {

				# Nonce security
				wp_nonce_field( 'upload_user_avatar_nonce', '_upload_user_avatar_nonce', false );
					
				# File upload input
				echo '<input type="file" name="upload_user_avatar" id="upload-local-avatar" /><br />';

				if ( empty( $avatar ) ) {
					echo '<p class="description">' . __( 'You currently have no photo or avatar. Use the button above to upload one, and then click Update Profile.', 'upload-user-avatars' ) . '</p>';
				}
				else {
					echo '<p><input type="checkbox" name="upload_user_avatar_erase" value="1" /> ' . __( 'Delete current image', 'upload-user-avatars' ) . '</p>';
					echo '<p class="description">' . __( 'Update your photo or avatar, or check the box above to delete your current one, and then click Update Profile.', 'upload-user-avatars' ) . '</p>';
				}

			}
			else {
				if ( empty( $avatar ) ) {
					echo '<p class="description">' . __( 'No avatar has been uploaded.', 'upload-user-avatars' ) . '</p>';
				}
				else {
					echo '<p class="description">' . __( 'You do not have the appropriate media management permissions to change your avatar here.</p><p class="description">To change your avatar, contact the site administrator.', 'upload-user-avatars' ) . '</p>';
				}	
			}
			?>
			</td>
		</tr>
	</table>
	<script>
		var form = document.getElementById('your-profile');
		form.encoding = 'multipart/form-data';
		form.setAttribute('enctype', 'multipart/form-data');
	</script> <?php
}
add_action( 'show_user_profile', 'kts_avatar_user_profile' );
add_action( 'edit_user_profile', 'kts_avatar_user_profile' );

/**
 * Update the user's avatar setting
 */
function kts_avatar_user_profile_update( $user_id ) {
	# Check for nonce
	if ( ! isset( $_POST['_upload_user_avatar_nonce'] ) || ! wp_verify_nonce( $_POST['_upload_user_avatar_nonce'], 'upload_user_avatar_nonce' ) ) {
		return;
	}

	if ( ! empty( $_FILES['upload_user_avatar']['name'] ) ) {
		# Allowed file extensions/types
		$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
		);

		# Front end support - shortcode
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		# Enable change of folder to which avatars are uploaded
		add_filter( 'upload_dir', 'kts_avatar_change_upload_dir' );

		# Delete all sizes of previous avatar
		kts_avatar_delete( $user_id );

		# Need to be more secure because low privilege users can upload
		if ( strstr( $_FILES['upload_user_avatar']['name'], '.php' ) ) {
			wp_die( 'For security reasons, files with the ".php" extension cannot be uploaded.' );
		}

		$avatar = wp_handle_upload( $_FILES['upload_user_avatar'], array(
			'mimes' => $mimes,
			'test_form' => false,
			'unique_filename_callback' => function( $dir, $name, $ext ) use( $user_id ) { // pass $user_id variable to anonymous function
				$user = get_user_by( 'id', $user_id );
				$name = $base_name = sanitize_file_name( $user->display_name . '_avatar' );
				$number = 1;

				while ( file_exists( $dir . '/' . $name . $ext ) ) {
					$name = $base_name . '_' . $number;
					$number++;
				}

				return $name . $ext;
			}
		) );

		# Handle failures
		if ( empty( $avatar['file'] ) && $avatar['error'] ) {
			if ( is_admin() ) {
				add_action( 'user_profile_update_errors', function( $errors ) use ( $avatar ) { // pass $avatar variable
					return $errors->add( 'avatar_error', '<strong>' . __( 'An error occurred while attempting to upload the file.', 'upload-user-avatars' ) . '</strong> ' . $avatar['error'] );
				} );
				return;
			}
			else {
				echo '<p class="red">' . __( 'An error occurred while attempting to upload the file.', 'upload-user-avatars' ) . ' ' . $avatar['error'] . __( ' An upload must be a .jpg, .jpeg, .gif, or .png file.', 'upload-user-avatars' ) . '</p>';
			}
		}

		# Save user information (overwriting previous)
		if ( isset( $avatar['url'] ) ) {
			update_user_meta( $user_id, 'upload_user_avatar', array( 'full' => $avatar['url'] ) );
		}

	}
	else if ( ! empty( $_POST['upload_user_avatar_erase'] ) ) {
		# Delete the current avatar
		kts_avatar_delete( $user_id );
	}
}
add_action( 'personal_options_update', 'kts_avatar_user_profile_update' );
add_action( 'edit_user_profile_update', 'kts_avatar_user_profile_update' );

/**
 * Enable avatar management on the frontend via this shortocde
 */
function kts_avatar_shortcode() {
	if ( ! is_user_logged_in() ) {
		return; // do nothing if not logged in
	}

	$user_id = get_current_user_id();
	$avatar = get_user_meta( $user_id, 'upload_user_avatar', true );

	if ( isset( $_POST['manage_avatar_submit'] ) ) {
		kts_avatar_user_profile_update( $user_id );
	}

	ob_start(); ?>

	<form id="upload-user-avatar-form" action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data"> <?php

		echo get_avatar( $user_id );

		$caps = get_option( 'upload_user_avatars_caps' );
		if ( empty( $caps ) || current_user_can( 'upload_files' ) ) {

			# Nonce security
			wp_nonce_field( 'upload_user_avatar_nonce', '_upload_user_avatar_nonce', false );
				
			# File upload input
			echo '<p><input type="file" name="upload_user_avatar" id="upload-local-avatar" /></p>';

			if ( empty( $avatar ) ) {
				echo '<p class="description">' . __( 'You currently have no photo or avatar. Use the button above to upload one, and then click Update.', 'upload-user-avatars' ) . '</p>';
			}
			else {
				echo '<p><input type="checkbox" name="upload_user_avatar_erase" value="1" /> ' . __( 'Delete current image', 'upload-user-avatars' ) . '</p>';
				echo '<p class="description">' . __( 'Update your photo or avatar, or check the box above to delete your current one, and then click Update.', 'upload-user-avatars' ) . '</p>';
			}
		}
		else {
			if ( empty( $avatar ) ) {
				echo '<p class="description">' . __( 'No avatar has been uploaded.', 'upload-user-avatars' ) . '</p>';
			}
			else {
				echo '<p class="description">' . __( 'You do not have the appropriate media management permissions to change your avatar here.</p><p class="description">To change your avatar, contact the site administrator.', 'upload-user-avatars' ) . '</p>';
			}	
		} ?>

		<input type="submit" name="manage_avatar_submit" value="<?php _e( 'Update', 'upload-user-avatars' ); ?>" />
	</form> <?php

	return ob_get_clean();
}
add_shortcode( 'upload-user-avatars', 'kts_avatar_shortcode' );

/**
 * Remove the custom get_avatar hook for the default avatar list output on 
 * the Discussion Settings page
 */
function kts_avatar_defaults( $avatar_defaults ) {
	remove_action( 'get_avatar', 'kts_get_avatar' );
	return $avatar_defaults;
}
add_action( 'avatar_defaults', 'kts_avatar_defaults' );

/**
 * Delete a user's avatar
 */
function kts_avatar_delete( $user_id ) {
	$old_avatars = get_user_meta( $user_id, 'upload_user_avatar', true );
	$upload_path = wp_upload_dir();

	if ( is_array( $old_avatars ) && ! empty( $old_avatars['full'] ) ) {

		# Get path to avatar uploaded via this plugin
		$old_avatar_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $old_avatars['full'] );

		# Remove file extension from path to avatar files
		$ext = pathinfo( $old_avatar_path, PATHINFO_EXTENSION );
		$avatar_sizes = str_replace( '.' . $ext, '', $old_avatar_path );

		# Get directory where avatars stored
		$user = get_user_by( 'id', $user_id );
		$name = sanitize_file_name( $user->display_name . '_avatar' );
		$avatar_directory = str_replace( $name . '.' . $ext, '', $old_avatar_path );

		# Delete every size of avatar for this user
		foreach( glob( $avatar_directory . '*.*' ) as $file ) {
			$pos = strpos( $file, $avatar_sizes );
			if ( $pos !== false ) {
				wp_delete_file( $file );
			}
		}
	}
	delete_user_meta( $user_id, 'upload_user_avatar' );
}
