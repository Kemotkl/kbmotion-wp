<?php
defined( 'ABSPATH' ) || exit;

function kbm_register_cpts() {
	register_post_type( 'kbm_trainer', array(
		'labels'       => array( 'name' => 'Trenerzy', 'singular_name' => 'Trener', 'add_new_item' => 'Dodaj trenera', 'edit_item' => 'Edytuj trenera' ),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'trener' ),
		'menu_icon'    => 'dashicons-groups',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'show_in_rest' => true,
		'show_in_menu' => 'kbm',
	) );
	register_post_type( 'kbm_review', array(
		'labels'       => array( 'name' => 'Opinie', 'singular_name' => 'Opinia', 'add_new_item' => 'Dodaj opinię' ),
		'public'       => false,
		'show_ui'      => true,
		'supports'     => array( 'title', 'editor', 'page-attributes' ),
		'show_in_rest' => true,
		'show_in_menu' => 'kbm',
	) );
	register_post_type( 'kbm_class', array(
		'labels'       => array( 'name' => 'Grafik zajęć', 'singular_name' => 'Zajęcia', 'add_new_item' => 'Dodaj zajęcia do grafiku' ),
		'public'       => false,
		'show_ui'      => true,
		'supports'     => array( 'title' ),
		'show_in_menu' => 'kbm',
	) );
	register_post_type( 'kbm_lead', array(
		'labels'          => array( 'name' => 'Zgłoszenia', 'singular_name' => 'Zgłoszenie' ),
		'public'          => false,
		'show_ui'         => true,
		'supports'        => array( 'title', 'editor' ),
		'show_in_menu'    => 'kbm',
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
}
add_action( 'init', 'kbm_register_cpts' );

/* ---------- Meta fields ---------- */
function kbm_meta_fields() {
	return array(
		'kbm_trainer' => array(
			'role'      => array( 'Specjalizacja (np. Treningi personalne)', 'text' ),
			'tags'      => array( 'Obszary (po przecinku: redukcja, siła, mobilność)', 'text' ),
			'instagram' => array( 'Instagram URL', 'url' ),
			'is_owner'  => array( 'Właściciel studia (1 = tak)', 'text' ),
		),
		'kbm_review'  => array(
			'stars'  => array( 'Ocena 1–5', 'text' ),
			'source' => array( 'Źródło (np. Google)', 'text' ),
		),
		'kbm_class'   => array(
			'day'     => array( 'Dzień (1=pon … 7=nd)', 'text' ),
			'start'   => array( 'Początek (HH:MM)', 'text' ),
			'end'     => array( 'Koniec (HH:MM)', 'text' ),
			'trainer' => array( 'Prowadzący', 'text' ),
			'level'   => array( 'Poziom (np. wszystkie poziomy)', 'text' ),
		),
	);
}

add_action( 'add_meta_boxes', function () {
	foreach ( kbm_meta_fields() as $type => $fields ) {
		add_meta_box( 'kbm_meta', 'Szczegóły', function ( $post ) use ( $fields ) {
			wp_nonce_field( 'kbm_meta', 'kbm_meta_nonce' );
			echo '<table class="form-table">';
			foreach ( $fields as $key => $def ) {
				printf(
					'<tr><th><label for="kbm_%1$s">%2$s</label></th><td><input class="regular-text" type="text" id="kbm_%1$s" name="kbm_%1$s" value="%3$s"></td></tr>',
					esc_attr( $key ),
					esc_html( $def[0] ),
					esc_attr( get_post_meta( $post->ID, '_kbm_' . $key, true ) )
				);
			}
			echo '</table>';
		}, $type, 'normal', 'high' );
	}
} );

add_action( 'save_post', function ( $post_id ) {
	if ( ! isset( $_POST['kbm_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kbm_meta_nonce'] ) ), 'kbm_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$type   = get_post_type( $post_id );
	$fields = kbm_meta_fields()[ $type ] ?? array();
	foreach ( $fields as $key => $def ) {
		if ( isset( $_POST[ 'kbm_' . $key ] ) ) {
			$raw = wp_unslash( $_POST[ 'kbm_' . $key ] );
			$val = 'url' === $def[1] ? esc_url_raw( $raw ) : sanitize_text_field( $raw );
			update_post_meta( $post_id, '_kbm_' . $key, $val );
		}
	}
} );

/* Admin columns for the schedule. */
add_filter( 'manage_kbm_class_posts_columns', function ( $c ) {
	return array( 'cb' => $c['cb'], 'title' => 'Zajęcia', 'day' => 'Dzień', 'time' => 'Godzina', 'trainer' => 'Prowadzący' );
} );
add_action( 'manage_kbm_class_posts_custom_column', function ( $col, $id ) {
	$days = kbm_days();
	if ( 'day' === $col ) {
		echo esc_html( $days[ (int) get_post_meta( $id, '_kbm_day', true ) ] ?? '–' );
	} elseif ( 'time' === $col ) {
		echo esc_html( get_post_meta( $id, '_kbm_start', true ) . '–' . get_post_meta( $id, '_kbm_end', true ) );
	} elseif ( 'trainer' === $col ) {
		echo esc_html( get_post_meta( $id, '_kbm_trainer', true ) );
	}
}, 10, 2 );

function kbm_days() {
	return array( 1 => 'Poniedziałek', 2 => 'Wtorek', 3 => 'Środa', 4 => 'Czwartek', 5 => 'Piątek', 6 => 'Sobota', 7 => 'Niedziela' );
}

/* Lead list columns. */
add_filter( 'manage_kbm_lead_posts_columns', function ( $c ) {
	return array( 'cb' => $c['cb'], 'title' => 'Zgłoszenie', 'kind' => 'Typ', 'email' => 'E-mail', 'date' => 'Data' );
} );
add_action( 'manage_kbm_lead_posts_custom_column', function ( $col, $id ) {
	if ( 'kind' === $col ) {
		echo esc_html( get_post_meta( $id, '_kbm_kind', true ) );
	} elseif ( 'email' === $col ) {
		echo esc_html( get_post_meta( $id, '_kbm_email', true ) );
	}
}, 10, 2 );
