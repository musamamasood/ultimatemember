<?php

// Get default and real arguments
$def_args = array();
foreach ( UM()->config()->core_directory_meta['members'] as $k => $v ) {
	$key = str_replace( '_um_', '', $k );
	$def_args[$key] = $v;
}
extract( array_merge( $def_args, $args ), EXTR_SKIP );


// View
$args['view_type'] = 'grid';

if ( empty( $args['view_types'] ) ) {
	$args['view_types'] = array(
		'grid',
		'list'
	);
	$single_view = true;
	$args['view_type'] = 'grid';
} elseif ( is_array( $args['view_types'] ) ) {
	if ( count( $args['view_types'] ) == 1 ) {
		$single_view = true;
		$args['view_type'] = $args['view_types'][0];
	}
	else {
		$single_view = false;
		$args['default_view'] = !empty( $args['default_view'] ) ? $args['default_view'] : $args['view_types'][0];
		$args['view_type'] = !empty( $_GET['view_type'] ) ? $_GET['view_type'] : $args['default_view'];
	}
}

$view_type_info = array(
	'list' => array(
		'title' => __( 'Change to List', 'ultimate-member' ),
		'icon'	=> 'um-faicon-list'
	),
	'grid' => array(
		'title' => __( 'Change to Grid', 'ultimate-member' ),
		'icon'	=> 'um-faicon-th'
	)
);

$delete_default = array_diff( array_keys( $view_type_info ), array_keys( array_flip( $args['view_types'] ) ) );

foreach ( $delete_default as $key => $value ) {
	unset( $view_type_info[ $value ] );
}

$view_type_info = apply_filters( 'um_add_view_types_info', $view_type_info, $args['view_types'] );

if( ! array_key_exists( $args['view_type'], $view_type_info ) && ! empty( $view_type_info ) ) {
	$args['view_type'] = 'grid';
}


// Search
$sorting_options = empty( $args['sorting_fields'] ) ? array() : $args['sorting_fields'];
if ( $sorting_options ) {
	$all_sorting_options = UM()->members()->get_sorting_fields();
	$sorting_options = array_intersect_key( $all_sorting_options, array_flip( $sorting_options ) );
}

$priority_user_role = UM()->roles()->get_priority_user_role( um_user( 'ID' ) );

$show_search = empty( $args['roles_can_search'] ) || in_array( $priority_user_role, $args['roles_can_search'] );

$show_filters = empty( $args['roles_can_filter'] ) || in_array( $priority_user_role, $args['roles_can_filter'] );

$search_filters = array();
if ( isset( $args['search_fields'] ) ) {
	$search_filters = apply_filters( 'um_frontend_member_search_filters', array_unique( array_filter( $args['search_fields'] ) ) );
}


// Classes
$classes = '';
if ( $search && $show_search ) {
	$classes .= ' um-member-with-search';	
}
if ( $filters && $show_filters ) {
	$classes .= ' um-member-with-filters';
}
if ( ! $single_view ) {
	$classes .= ' um-member-with-view';
}
if ( ! empty( $sorting_options ) ) {
	$classes .= ' um-member-with-sorting';
}


// Extentions scripts
if ( UM()->options()->get( 'followers_show_stats' ) || UM()->options()->get( 'followers_show_button' ) ) {
	wp_enqueue_script( 'um_followers' );
}
if ( !empty( $args['friends_show_stats'] ) || !empty( $args['friends_show_button'] ) ) {
	wp_enqueue_script( 'um_friends' );
}
if ( !empty( $args['show_pm_button'] ) ) {
	wp_enqueue_script( 'um-messaging' );
}


// Templates		
include UM()->templates()->get_template( 'members-grid' );
include UM()->templates()->get_template( 'members-list' );
include UM()->templates()->get_template( 'members-pagination' );
?>

<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo esc_attr( $form_id ); ?> um-visible"
     data-unique_id="um-<?php echo esc_attr( $form_id ) ?>"
     data-view_type="<?php echo $args['view_type'] ?>"
     data-only_search="<?php echo (int)( $search && $show_search && $must_search ) ?>">

	<div class="um-form">
		<div class="um-member-directory-header <?php echo esc_attr( $classes ) ?>">
			<?php if ( $search && $show_search ) { ?>
				<div class="um-member-directory-search-line">
					<input type="text" class="um-search-line" placeholder="<?php esc_attr_e( 'Search', 'ultimate-member' ) ?>"  value="" />
					<div class="uimob340-show uimob500-show">
						<a href="javascript:void(0);" class="um-button um-do-search um-tip-n" original-title="<?php esc_attr_e( 'Search', 'ultimate-member' ); ?>">
							<i class="um-faicon-search"></i>
						</a>
					</div>
					<div class="uimob340-hide uimob500-hide">
						<a href="javascript:void(0);" class="um-button um-do-search"><?php _e( 'Search', 'ultimate-member' ); ?></a>
					</div>
				</div>
			<?php } ?>
				
			<?php if ( ! empty( $sorting_options ) ) { ?>
				<div class="um-member-directory-sorting">
					<select class="um-s3 um-member-directory-sorting-options" id="um-member-directory-sorting-select-<?php echo esc_attr( $form_id ) ?>" data-placeholder="<?php esc_attr_e( 'Sort By', 'ultimate-member' ); ?>">
						<option value=""></option>
						<?php foreach ( $sorting_options as $value => $title ) { ?>
							<option value="<?php echo $value ?>"><?php echo $title ?></option>
						<?php } ?>
					</select>
				</div>
			<?php } ?>

			<div class="um-member-directory-actions">
				<?php if ( $filters && $show_filters ) { ?>
					<div class="um-member-directory-filters">
						<a href="javascript:void(0);" class="um-member-directory-filters-a um-tip-n" original-title="<?php esc_attr_e( 'Filters', 'ultimate-member' ); ?>">
							<i class="um-faicon-sliders"></i>
						</a>
					</div>
				<?php } ?>

				<?php if ( ! $single_view ) { ?>
					<div class="um-member-directory-view-type">
						<?php foreach ( $view_type_info as $key => $type ) { ?>
							<a href="javascript:void(0)"
								class="um-member-directory-view-type-a um-tip-n"
								data-type="<?php echo $key; ?>"
								original-title="<?php echo $type['title']; ?>"
								default-title="<?php echo $type['title']; ?>"
								next-item="" ><i class="<?php echo $type['icon']; ?>"></i></a>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="um-clear"></div>

		<?php 
		if ( $filters && $show_filters ) {		

			if ( !empty( $args['filters'] ) && is_array( $search_filters ) ) { ?>
				<script type="text/template" id="tmpl-um-members-filtered-line">
					<# if ( data.filters.length > 0 ) { #>
						<# _.each( data.filters, function( filter, key, list ) { #>
							<div class="um-members-filter-tag">
								<strong>{{{filter.label}}}</strong>: {{{filter.value_label}}}
								<div class="um-members-filter-remove" data-name="{{{filter.name}}}" data-value="{{{filter.value}}}" data-range="{{{filter.range}}}">&times;</div></div>
						<# }); #>
					<# } #>
				</script>

				<div class="um-search um-search-<?php echo count( $search_filters ) ?>">
					<?php
					foreach ( $search_filters as $i =>$filter ) :
						$filter_content = UM()->members()->show_filter( $filter );
						if ( empty( $filter_content ) ) {
							continue;
						} 
						?>

						<div class="um-search-filter"> <?php echo $filter_content; ?> </div>

						<?php
					endforeach;
					?>
						
					<div class="um-clear"></div>
				</div>

				<div class="um-filtered-line">
					<div class="um-clear-filters"><a href="javascript:void(0);" class="um-clear-filters-a"><?php esc_attr_e( 'Clear All Filters', 'ultimate-member' ); ?></a></div>
				</div>
			<?php 
			}
		}
		do_action( 'um_members_directory_head', $args );
		?>

		<div class="um-members-wrapper">
			<?php do_action( 'um_member_directory_map', $args ); ?>
			<div class="um-members-overlay"><div class="um-ajax-loading"></div></div>
		</div>
		<div class="um-clear"></div>

		<div class="um-members-pagination-box"></div>		

		<?php
		/**
		* UM hook
		*
		* @type action
		* @title um_members_directory_footer
		* @description Member directory display footer
		* @input_vars
		* [{"var":"$args","type":"array","desc":"Member directory shortcode arguments"}]
		* @change_log
		* ["Since: 2.0"]
		* @usage add_action( 'um_members_directory_footer', 'function_name', 10, 1 );
		* @example
		* <?php
		 * add_action( 'um_members_directory_footer', 'my_members_directory_footer', 10, 1 );
		 * function my_members_directory_footer( $args ) {
		 *     // your code here
		 * }
		 * ?>
		*/
		do_action( 'um_members_directory_footer', $args ); ?>

		<div class="um-clear"></div>
	</div>
</div>
