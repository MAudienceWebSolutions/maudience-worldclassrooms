<?php
// No dirrect access
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * 
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'Query_Tour_Signups' ) ) :
	class Query_Tour_Signups {

		public $args          = array();
		public $request       = '';
		public $wheres        = '';

		public $prep          = array();
		public $results       = array();
		public $max_num_pages = 1;
		public $total_rows    = 0;

		/**
		 * Construct
		 * @version 1.0
		 */
		function __construct( $args = array() ) {

			$this->args = wp_parse_args( $args, array(
				'signup_id'   => NULL,
				'user_id'     => NULL,
				'tour_id'     => NULL,
				'status'      => NULL,
				'plan'        => NULL,
				'payment'     => NULL,
				'payment_id'  => NULL,
				'scholarship' => NULL,
				'time'        => NULL,
				'orderby'     => 'time',
				'order'       => 'DESC',
				'number'      => 10,
				'offset'      => '',
				'paged'       => NULL
			) );

			global $wpdb;

			$table = $wpdb->prefix . 'tour_signups';
			$select = $where = $sortby = $limits = '';
			$prep = $wheres = array();

			if ( $this->args['signup_id'] !== NULL ) {
				$wheres[] = 'id = %d';
				$prep[]   = absint( $this->args['signup_id'] );
			}

			if ( $this->args['user_id'] !== NULL ) {
				$wheres[] = 'user_id = %d';
				$prep[]   = absint( $this->args['user_id'] );
			}

			if ( $this->args['tour_id'] !== NULL ) {
				$wheres[] = 'tour_id = %d';
				$prep[]   = absint( $this->args['tour_id'] );
			}

			if ( $this->args['status'] !== NULL ) {
				$wheres[] = 'status = %s';
				$prep[]   = sanitize_key( $this->args['status'] );
			}

			if ( $this->args['plan'] !== NULL ) {
				$wheres[] = 'plan = %s';
				$prep[]   = sanitize_key( $this->args['plan'] );
			}

			if ( $this->args['payment'] !== NULL ) {
				$wheres[] = 'payment = %f';
				$prep[]   = number_format( $this->args['payment'], 2, '.', '' );
			}

			if ( $this->args['payment_id'] !== NULL ) {
				$wheres[] = 'payment_id = %s';
				$prep[]   = sanitize_text_field( $this->args['payment_id'] );
			}

			if ( $this->args['scholarship'] !== NULL ) {
				$wheres[] = 'scholarship = %s';
				$prep[]   = sanitize_text_field( $this->args['scholarship'] );
			}

			if ( $this->args['orderby'] !== NULL && in_array( $this->args['orderby'], array( 'time', 'paid', 'id' ) ) )
				$sortby = "ORDER BY " . $this->args['orderby'] . " " . $this->args['order'];

			$number = $this->args['number'];
			if ( $number < -1 )
				$number = abs( $number );

			elseif ( $number == 0 || $number == -1 )
				$number = NULL;

			// Limits
			if ( $number !== NULL ) {

				$page = 1;
				if ( $this->args['paged'] !== NULL ) {
					$page = absint( $this->args['paged'] );
					if ( ! $page )
						$page = 1;
				}

				if ( $this->args['offset'] == '' ) {
					$pgstrt = ($page - 1) * $number . ', ';
				}

				else {
					$offset = absint( $this->args['offset'] );
					$pgstrt = $offset . ', ';
				}

				$limits = 'LIMIT ' . $pgstrt . $number;
			}
			else {
				$limits = '';
			}

			// Prep return
			$select = '*';

			$found_rows = '';
			if ( $limits != '' )
				$found_rows = 'SQL_CALC_FOUND_ROWS';

			if ( empty( $wheres ) ) {
				$wheres[] = 'status != %s';
				$prep[]   = 'temp';
			}

			$this->wheres = $where = 'WHERE ' . implode( ' AND ', $wheres );

			// Run
			$this->request = $wpdb->prepare( "SELECT {$found_rows} * FROM {$table} {$where} {$sortby} {$limits};", $prep );
			$this->prep    = $prep;
			$this->results = $wpdb->get_results( $this->request );

			if ( $limits != '' )
				$this->num_rows = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
			else
				$this->num_rows = count( $this->results );

			if ( $limits != '' )
				$this->max_num_pages = ceil( $this->num_rows / $number );

			$this->total_rows = $wpdb->get_var( "SELECT COUNT( * ) FROM {$table}" );

			$this->status_types = array(
				''        => 'Unknown',
				'new'     => 'New',
				'paid'    => 'Paid in Full',
				'pending' => 'Pending Payment'
			);

		}

		/**
		 * Has Entries
		 * @version 1.0
		 */
		public function have_entries() {

			if ( ! empty( $this->results ) ) return true;
			return false;

		}

		public function show_status( $status = '' ) {

			if ( array_key_exists( $status, $this->status_types ) )
				return $this->status_types[ $status ];

			return '-';

		}

		protected function get_status_counts() {

			global $wpdb;

			$table = $wpdb->prefix . 'tour_signups';
			return $wpdb->get_results( "
				SELECT COUNT(*) AS total, status 
				FROM {$table} 
				WHERE status IN ('new','paid','pending') 
				GROUP BY status DESC;" );

		}

		/**
		 * Construct
		 * @version 1.0
		 */
		public function status_filter() {

			if ( ! $this->have_entries() ) return;

			$base = add_query_arg( array( 'post_type' => 'tour', 'page' => $_GET['page'] ), admin_url( 'edit.php' ) );

			$statuses = $this->get_status_counts();

?>
<ul class="subsubsub">
	<li><a href="<?php echo esc_url( $base ); ?>"<?php if ( ! isset( $_GET['status'] ) || $_GET['status'] == '-1' ) echo ' class="current"'; ?>>All <span class="count">(<?php echo number_format_i18n( $this->total_rows ); ?>)</span></a> | </li>
<?php

			if ( ! empty( $statuses ) ) {
				$count = 0;
				$total = count( $statuses );
				foreach ( $statuses as $status ) {
					$count ++;

?>
	<li><a href="<?php echo esc_url( add_query_arg( 'status', $status->status, $base ) ); ?>"<?php if ( isset( $_GET['status'] ) && $_GET['status'] == $status->status ) echo ' class="current"'; ?>><?php echo $this->show_status( $status->status ); ?> <span class="count">(<?php echo number_format_i18n( $status->total ); ?>)</span></a><?php if ( $count < $total ) echo ' | '; ?></li>
<?php

				}
			}

?>
</ul>
<?php

		}

		/**
		 * Get Page Number
		 * @version 1.0
		 */
		public function get_pagenum() {

			global $paged;

			if ( $paged > 0 )
				$pagenum = absint( $paged );

			elseif ( isset( $_REQUEST['paged'] ) )
				$pagenum = absint( $_REQUEST['paged'] );

			else return 1;

			return max( 1, $pagenum );

		}

		/**
		 * Pagination
		 * @version 1.0
		 */
		public function pagination( $location = 'top', $id = '' ) {

			$output      = '';
			$total_pages = $this->max_num_pages;
			$total_items = $this->num_rows;
			$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

			$current = $this->get_pagenum();

			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

			$page_links = array();

			$total_pages_before = '<span class="paging-input">';
			$total_pages_after  = '</span>';

			$disable_first = $disable_last = $disable_prev = $disable_next = false;

 			if ( $current == 1 ) {
				$disable_first = true;
				$disable_prev = true;
 			}
			if ( $current == 2 ) {
				$disable_first = true;
			}
 			if ( $current == $total_pages ) {
				$disable_last = true;
				$disable_next = true;
 			}
			if ( $current == $total_pages - 1 ) {
				$disable_last = true;
			}

			if ( $disable_first ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( remove_query_arg( 'paged', $current_url ) ),
					__( 'First page' ),
					'&laquo;'
				);
			}

			if ( $disable_prev ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
					__( 'Previous page' ),
					'&lsaquo;'
				);
			}

			if ( 'bottom' == $location ) {
				$html_current_page  = $current;
				$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input">';
			} else {
				$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' />",
					'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
					$current,
					strlen( $total_pages )
				);
			}
			$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
			$page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

			if ( $disable_next ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
					__( 'Next page' ),
					'&rsaquo;'
				);
			}

			if ( $disable_last ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
					__( 'Last page' ),
					'&raquo;'
				);
			}

			$pagination_links_class = 'pagination-links';
			if ( ! empty( $infinite_scroll ) ) {
				$pagination_links_class = ' hide-if-js';
			}
			$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

			if ( $total_pages ) {
				$page_class = $total_pages < 2 ? ' one-page' : '';
			} else {
				$page_class = ' no-pages';
			}

			echo '<div class="tablenav-pages' . $page_class . '">' . $output . '</div>';

		}

		/**
		 * Row Actions
		 * @version 1.0
		 */
		public function row_actions( $entry ) {

			$actions = array();
			$base    = add_query_arg( array( 'post_type' => 'tour', 'page' => 'mywclass-signups' ), admin_url( 'edit.php' ) );

			if ( $entry->status != 'new' )
				$actions['edit'] = '<a href="' . esc_url( add_query_arg( array( 'action' => 'edit', 'signup_id' => $entry->id ), $base ) ) . '">Edit Signup</a>';
			else
				$actions['edit'] = 'Can not edit';

			if ( $entry->status != 'new' )
				$actions['resend'] = '<a href="' . esc_url( add_query_arg( array( 'action' => 'resend', 'signup_id' => $entry->id ), $base ) ) . '">Resend Email</a>';
			$actions['delete'] = '<a href="' . esc_url( add_query_arg( array( 'action' => 'delete', 'signup_id' => $entry->id ), $base ) ) . '">Delete</a>';

			$output = '';
			$counter = 0;
			$count = count( $actions );
			foreach ( $actions as $id => $link ) {

				$end = ' | ';
				if ( $counter+1 == $count )
					$end = '';

				$output .= '<span class="' . $id . '">' . $link . $end . '</span>';
				$counter ++;

			}

			return '<div class="row-actions">' . $output . '</div>';

		}

	}
endif;

?>