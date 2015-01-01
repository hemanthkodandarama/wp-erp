<?php

/**
 * Create a new department
 *
 * @param  array   arguments
 *
 * @return int|false
 */
function erp_hr_create_department( $args = array() ) {
    global $wpdb;

    $defaults = array(
        'id'          => 0,
        'company_id'  => 0,
        'title'       => '',
        'description' => '',
        'lead'        => 0,
        'parent'      => 0,
        'status'      => 1
    );

    $fields = wp_parse_args( $args, $defaults );

    // validation
    if ( ! $fields['company_id'] ) {
        return new WP_Error( 'no-company-id', __( 'No company provided.', 'wp-erp' ) );
    }

    if ( empty( $fields['title'] ) ) {
        return new WP_Error( 'no-name', __( 'No department name provided.', 'wp-erp' ) );
    }

    // unset the department id
    $dept_id = $fields['id'];
    unset( $fields['id'] );

    if ( ! $dept_id ) {

        if ( $wpdb->insert( $wpdb->prefix . 'erp_hr_depts', $fields ) ) {

            do_action( 'erp_hr_dept_new', $wpdb->insert_id, $fields );

            return $wpdb->insert_id;
        }

    } else {

        if ( $wpdb->update( $wpdb->prefix . 'erp_hr_depts', $fields, array( 'id' => $dept_id ) ) ) {

            do_action( 'erp_hr_dept_updated', $dept_id, $fields );

            return true;
        }

    }

    return false;
}

/**
 * Get all the departments of a company
 *
 * @param  int  the company id
 *
 * @return array  list of departments
 */
function erp_hr_get_departments( $company_id ) {
    global $wpdb;

    return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}erp_hr_depts WHERE company_id = %d", $company_id ) );
}

/**
 * Delete a department
 *
 * @param  int  department id
 *
 * @return bool
 */
function erp_hr_delete_department( $department_id ) {
    global $wpdb;

    return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}erp_hr_depts WHERE id = %d", $department_id ) );
}

/**
 * Get company departments dropdown
 *
 * @param  int  company id
 * @param  string  selected department
 *
 * @return string  the dropdown
 */
function erp_hr_get_departments_dropdown( $company_id, $selected = '' ) {
    $departments = erp_hr_get_departments( $company_id );
    $dropdown = '<option value="0">' . __( '- Select Department -' ) . '</option>';

    if ( $departments ) {
        foreach ($departments as $key => $department) {
            $dropdown .= sprintf( "<option value='%s'>%s</option>\n", $department->id, stripslashes( $department->title ) );
        }
    }

    return $dropdown;
}