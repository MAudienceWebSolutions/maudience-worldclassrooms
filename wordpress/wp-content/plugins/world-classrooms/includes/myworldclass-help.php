<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Help: Signup List
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'myworldclass_help_signup_list' ) ) :
	function myworldclass_help_signup_list() {

		$screen = get_current_screen();

		$screen->add_help_tab( array(
			'id'      => 'overview',
			'title'   => 'Tour Signups',
			'content' => '
			<h3>Introduction</h3>
			<p>Each time someone begins to signup for a tour, their entry is saved into our database. These entries are accessible on this page where you can edit existing signups or new ones.</p>
			<p>To prevent accumulation of large number of incomplete signups, the website automatically delete incomplete signups once an hour.</p>
			<p><strong>Note</strong><br />Depending on traffic, a large number of new signups can accumulate in the database before they are automatically deleted. For your convenience, when selecting to view "All" signups, these New signups are filtered out, leaving only Pending or Paid in Full signups visible. To view these new signups, click on filter by "New" below.</p>
			<p>&nbsp;</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'signup-statuses',
			'title'   => 'Signup Statuses',
			'content' => '
			<h3>Statuses</h3>
			<p>There are three possible statuses a signup can receive:</p>
			<h4>New</h4>
			<p>Until a signup has been completed with a successful payment, the signup is considered to be "New". Signups with this status are automatically deleted by the website after 1 hour of inactivity.</p>
			<h4>Pending</h4>
			<p>Once a signup has been completed but not paid in full, the signup is considered "Pending Payment". This is the status the signup will have until payment has been made in full.</p>
			<h4>Paid in Full</h4>
			<p>This is the final status a signup receives. This status can only be reached by paying the outstanding amount for the tour or by an administrator changing it.</p>
			<p>&nbsp;</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'signup-editing',
			'title'   => 'Editing',
			'content' => '
			<h3>Editing Signups</h3>
			<p>To edit a particular signup, simply hover your mouse over the name of the person and click on "Edit Signup".</p>
			<p><strong>Note</strong><br />New signups that are incomplete can not be edited. Furthermore, until the first step of the signup is completed and we have a name, the name for the signup will come up as "Unknown".</p>
			<p>&nbsp;</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'signup-deleting',
			'title'   => 'Deleting',
			'content' => '
			<h3>Deleting Signups</h3>
			<p>To delete a particular signup, simply hover your mouse over the signup in the list and click "Delete".</p>
			<p>You can also bulk delete signups by ticking the checkbox for each one you want to delete and select "Delete" in the "Bulk Action" dropdown menu.</p>
			<p><strong style="color:red;">WARNING!</strong><br />Deleting a signup <strong>can not be undone</strong>! If the signup is Pending and has a payment history, those entries will be also deleted from the website. It will however not delete payment records in your Authorize.net account.</p>
			<p>&nbsp;</p>',
		) );

	}
endif;

/**
 * Help: Signup Editor
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'myworldclass_help_signup_editor' ) ) :
	function myworldclass_help_signup_editor() {

		$screen = get_current_screen();

		$screen->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' => '
			<h3>Introduction</h3>
			<p>Here you can edit a particular signup. This editor is intended for administrators or staff that needs to check or edit details provided for this signup.</p>
			<p>&nbsp;</p>',
		) );

	}
endif;

/**
 * Help: 
 * @since 1.0
 * @version 1.0
 */


?>