<?php
    /*
    Plugin Name: User Role Dropdown
    Plugin URI: https://www.designsbytouch.co.uk
    Description: Add dropdown for user role selection
    Author: Daniel Hand / Studio Touch
    Version: 1.0

    */


function user_role_dropdown() {
  $wp_roles = new WP_Roles();
	$wp_roles->use_db = true;

    echo '<select id ="role" name="role">';
    foreach ( $wp_roles->roles as $key=>$value ) {
        if (($value['name'] !== 'Administrator') and ($value['name'] !== 'Editor') and ($value['name'] !== 'Author') and ($value['name'] !== 'Contributor') and ($value['name'] !== 'Subscriber') and ($value['name'] !== 'UK') and ($value['name'] !== 'USA') and ($value['name'] !== 'Customer') and ($value['name'] !== 'Shop manager')) {
            echo '<option value="' . $key . '">' . $value['name'] . '</option>';
        }
    }
    echo '</select>';

}

add_action( 'woocommerce_register_form', 'user_role_dropdown' );



add_action( 'personal_options_update', 'save_role_selection_field' );
add_action( 'edit_user_profile_update', 'save_role_selection_field' );
function save_role_selection_field( $user_id ) {
	//if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

	update_user_meta( $user_id, 'role', $_POST['role'] );

	$user = new WP_User( $user_id );

	// Remove role
	$current_user_role = get_current_user_role();

	$user->remove_role( $current_user_role );

	// Add role
	$user->add_role( $_POST['role'] );
}

function get_current_user_role () {
    global $current_user;
    get_currentuserinfo();
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    return $user_role;
};


add_action('user_register', 'register_role');
function register_role($user_id, $password="", $meta=array()) {

   $userdata = array();
   $userdata['ID'] = $user_id;
   $userdata['role'] = $_POST['role'];

   // allow if a role is selected
   if ( $userdata['role'] ){
      wp_update_user($userdata);
   }
}


//2. Add validation.
add_filter( 'registration_errors', 'new_usersroles_registration_errors', 10, 3 );
function new_usersroles_registration_errors( $errors, $sanitized_user_login, $user_email ) {

    if ( empty( $_POST['role'] ) || ! empty( $_POST['role'] ) && trim( $_POST['role'] ) == '' ) {
         $errors->add( 'role_error', __( '<strong>ERROR</strong>: You must include a role.', 'nicholaswells' ) );
    }

    return $errors;
}

//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'new_usersroles_user_register' );
function new_usersroles_user_register( $user_id ) {

   $user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $_POST['role'] ) );
}



	?>
