<?php
	/**
	 *	Archive calendar
	 **/

	// Getting month names (we make the list once to avoid doing to much date conversion)
	$monthName = array();
	for($i=1;$i!=13;$i++) {
		$index = sprintf('%02d', $i);
		$monthName[$index] = ucfirst(mb_substr(CaracoleI18n::strftime("%B", strtotime('2000-'.$index.'-01 00:00:00')), 0, 3));
	}
	// Getting first and last years
	$item = array_shift($itemList);
	$startYear = strftime("%Y", strtotime($item['Post']['publish_start']));
	$lastItem = end($itemList);
	$endYear = strftime("%Y", strtotime($lastItem['Post']['publish_start']));

	// Starting table
	echo $this->Html->tag('table', null, array('class' => 'calendarYear', 'cellspacing' => 0));
	echo $this->Html->tag('tbody', null);
	// Year line
	for($year=$startYear;$year>=$endYear;$year--) {
		$content = $year;
		// Adding a link if there is at least one post in this year
		if (substr($item['Post']['publish_start'], 0, 4)==$year) {
			$content = $this->Fastcode->link($content, array(
					'plugin' => 'caracole_blog',
					'controller' => 'posts',
					'action' => 'archive',
					'year' => $year
			));
		}
		echo $this->Html->tag('td', $content, array('class' => 'year'));

		// Displaying months
		for($month=1;$month<=12;$month++) {
			$monthIndex = sprintf("%02d", $month);
			$content = $monthName[$monthIndex];

			// Adding a link if there is a post in this month
			if (substr($item['Post']['publish_start'], 0, 7)=="$year-$monthIndex") {
				$content = $this->Fastcode->link($content, array(
					'plugin' => 'caracole_blog',
					'controller' => 'posts',
					'action' => 'archive',
					'year' => $year,
					'month' => $monthIndex
				));
				$item = array_shift($itemList);
			}

			echo $this->Html->tag('td', $content);
		}

		// Ending the year
		echo '</tr>';
	}
	echo '</tbody></table>';


?>
