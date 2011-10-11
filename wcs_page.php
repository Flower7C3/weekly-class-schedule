<div id="wcs-container">
<?php
global $wpdb;
global $classes_obj;
global $instructors_obj;
global $schedule_obj;
$table_name = $schedule_obj->table_name;
$week_days_array = array ( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'	);
$enable_24h = get_option( 'enable_24h' );
$enable_classrooms = get_option( 'enable_classrooms' );
$schedule_tables_array = array( '' );

if ( $enable_classrooms == "on" ) {
	if ( empty( $atts ) ) {
		$classroom_message = "<h2>Classroom attribute is not defined</h2>";
		$classroom_message .= "<p>If you are using this plugin in 'Classroom' mode, your shortcode needs to have the classroom attribute ";
		$classroom_message .= "and look something like that: <pre>[wcs \"Classroom A\"]</pre></p>";
		$classroom_message .= "<p>Check documentation for more information.</p>";
		echo $classroom_message;
	} else {
		foreach ( $atts as $classroom ) {
			$schedule_tables_array[] = $classroom;
		}
		array_shift( $schedule_tables_array );
	}
}
// Print Schedule in table format ?>
<?php
	$sql = "SELECT * FROM " . $table_name;
	$results = $wpdb->get_results( $sql );

	foreach ( $schedule_tables_array as $key => $schedule ) :
		echo "<br/><h2>" . ucwords( $schedule ) . "</h2>";
		if ( $enable_classrooms == "on" ) {
			$sql = "SELECT start_hour FROM " . $table_name . " WHERE classroom = '" . $schedule . "' ORDER BY start_hour ASC";
			$start_hours_array = array_unique( $wpdb->get_col( $sql ) );
		} else {
			$sql = "SELECT start_hour FROM " . $table_name . " ORDER BY start_hour ASC";
			$start_hours_array = array_unique( $wpdb->get_col( $sql ) );
		}
?>
<table class="wcs-schedule-table">
	<tr>
		<th>&nbsp;</th>
		<?php
			foreach ( $week_days_array as $value ) {
				echo "<th class='weekday-label weekday-column'>" . substr( $value, 0, 3 ) . "</th>";
			}
		?>
	</tr>
	<?php
		foreach ( $start_hours_array as $start_hour ) {
			if ( $enable_24h == "on" ) {
				echo "<tr><td class='hour-label'>" . clean_time_format( $start_hour ) . "</td>";
			} else {
				echo "<tr><td class='hour-label'>" . convert_to_am_pm( $start_hour ) . "</td>";
			}
			
			foreach ( $week_days_array as $weekday ) {
				echo "<td class='" . strtolower( $weekday ) . "-column weekday-column'>";
				foreach ( $results as $entry) {
					if ( $enable_classrooms == "on" ) {
						$verify = ( $entry->classroom == $schedule ? true : false );
					} else {
						$verify = true;
					}
					if ( $entry->start_hour == $start_hour && $entry->week_day == $weekday && ( $verify ) ) {
						$sql = "SELECT item_description FROM " . $classes_obj->table_name . " WHERE id = '" . $entry->class_id . "'";
						$class_desc = esc_html( stripslashes( $wpdb->get_var( $sql ) ) );
						$sql = "SELECT item_description FROM " . $instructors_obj->table_name . " WHERE id = '" . $entry->instructor_id . "'";
						$inst_desc = esc_html( stripslashes( $wpdb->get_var( $sql ) ) );
						
						$class = esc_html( stripslashes( $entry->class ) );
						$inst = esc_html( stripslashes ( $entry->instructor ) );
						
						if ( $enable_24h == "on" ) {
							$class_start = clean_time_format( $entry->start_hour );
							$class_end = clean_time_format( $entry->end_hour );
						} else {
							$class_start = convert_to_am_pm( $entry->start_hour );
							$class_end = convert_to_am_pm( $entry->end_hour );
						}
						
						$notes = ( strlen( $entry->notes ) > 14 ) ? substr( $entry->notes, 0 , 12 ) . "..." : $entry->notes;
						
						$output = "<!--[if IE 7]><div class='ie-container'><![endif]-->";
						$output .= "<div class='active-box-container'><div class='class-box'>" . $class . "</a></div>";
						$output .= "<div class='class-info'><a class='qtip-target' title='" . $class_desc . "'>" . $class . "</a>";
						$output .= " with ";
						$output .= "<a class='qtip-target' title='" . $inst_desc . "'>" . $inst . "</a><br/>";
						$output .= $class_start . " to " . $class_end;
						$output .= "<br/><div class='notes-container'>" . $entry->notes . "</div>"; 
						$output .= "</div></div>";
						$output .= "<!--[if IE 7]></div><![endif]-->";
						echo $output;
					} 
				}
				echo "</td>";
			}
			
			echo "</tr>";
		}
	?>
</table>
<?php endforeach; ?>
</div>




