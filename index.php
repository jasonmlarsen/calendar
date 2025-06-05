<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Calendar</title>
<meta property="og:title" content="Calendar">
<meta property="og:url" content="https://jasonmlarsen.com/calendar">
<meta property="og:description" content="A simple printable calendar with the full year on a single page">
<style>
@import url('https://fonts.bunny.net/css?family=inter:300|oswald:300,400');
@media print {
  @page {
    margin: 0;
    padding: 1em;
  }
  #info {
    display: none;
  }
  td {
    font-size: 8px !important;
  }
  .weekend {
    background: #d8d8d8 !important;
  }
}
html {
  font-family: 'Oswald';
}
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
table {
  width: 100%;
  height: calc(100% - 2.5em);
  border-collapse: separate;
  border-spacing: .5em 0;
}
td, th {
  font-weight: normal;
  text-transform: uppercase;
  border-bottom: 1px solid #888;
  padding: .3vmin .3vmin;
  font-size: .9vmin;
  font-weight: 300;
  color: #000;
}
th {
  font-size: 1.1vmin;
  padding: 0;
}
td:empty {
  border: 0;
}
.date {
  display: inline-block;
  width: 1.1em;
}
.day {
  display: inline-block;
  text-align: center;
  width: 1em;
  color: #888;
}
.weekend {
  background: #eee;
  font-weight: 400;
}
p {
  margin: 0 0 .5em 0;
  text-align: center;
}
* {
  color-adjust: exact;
  -webkit-print-color-adjust: exact;
}
#info {
  font-family: 'Inter', sans-serif;
  position: absolute;
  top: 0;
  left: 0;
  margin: 5em 2em;
  width: calc(100% - 6em);
  background: #333;
  color: #eee;
  padding: 1em 1em .5em 1em;
  font-size: 2vmax;
  border-radius: .2em;
}
#info p {
  text-align: left;
  margin: 0 0 1em 0;
  line-height: 135%;
}
#info a {
  color: inherit;
}
</style>
</head>
<body>
<div id="info">
<p>👋 <strong>Hello!</strong> If you print this page, you’ll get a nifty calendar that displays all of the year’s dates on a single page. It will automatically fit on a single sheet of paper of any size. For best results, adjust your print settings to landscape orientation and disable the header and footer.</p>
<p>Take in the year all at once. Fold it up and carry it with you. Jot down your notes on it. Plan things out and observe the passage of time. Above all else, be kind to others.</p>
<p>Looking for <?php echo date("Y", strtotime("now + 1 year")); ?>? <a href="?year=<?php echo date("Y", strtotime("now + 1 year")); ?>">Here you go!</a></p>
<p style="font-size: 80%; color: #999;">Made by <a href="https://neatnik.net/">Neatnik</a> · Small changes made by <a href="https://jasonmlarsen.com/">Jason</a></p>
</div>
<?php
date_default_timezone_set('UTC');

// Parse startmonth parameter (e.g., 'jan' for January)
$month_names = array('jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6, 
                     'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12);
$start_month = 1; // Default to January
if (isset($_REQUEST['startmonth']) && array_key_exists(strtolower($_REQUEST['startmonth']), $month_names)) {
  $start_month = $month_names[strtolower($_REQUEST['startmonth'])];
}

// Set the base year from ?year parameter or current year
$base_year = isset($_REQUEST['year']) ? (int)$_REQUEST['year'] : date('Y', time());

// Calculate the year range for the header
$end_month = $start_month == 1 ? 12 : $start_month - 1;
$end_year = $start_month == 1 ? $base_year : $base_year + 1;
$year_display = ($base_year == $end_year) ? $base_year : "$base_year-$end_year";

// Initialize arrays and variables
$dates = array();
$month = $start_month;
$month_index = 1; // Tracks the 12 months to display
$day = 1;

// Display the year header
echo '<p>' . $year_display . '</p>';
echo '<table>';
echo '<thead>';
echo '<tr>';

// Add the month headings for 12 consecutive months
for ($i = 0; $i < 12; $i++) {
  $current_month = ($start_month + $i - 1) % 12 + 1;
  $current_year = $base_year + floor(($start_month + $i - 1) / 12);
  $month_date = DateTime::createFromFormat('!Y-m-d', "$current_year-$current_month-01");
  echo '<th>' . $month_date->format('M') . '</th>';
}
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Prepare a list of the first weekdays for each of the 12 months
$first_weekdays = array();
$month_flags = array();

for ($i = 1; $i <= 12; $i++) {
  $current_month = ($start_month + $i - 1) % 12 + 1;
  $current_year = $base_year + floor(($start_month + $i - 1) / 12);
  $first_weekdays[$i] = date('N', strtotime("$current_year-$current_month-01"));
  $month_flags[$i] = false; // Flag to track first days
}

// Generate the date matrix for 12 months
while ($month_index <= 12) {
  $day = 1;
  $current_month = ($start_month + $month_index - 1) % 12 + 1;
  $current_year = $base_year + floor(($start_month + $month_index - 1) / 12);
  for ($x = 1; $x <= 42; $x++) {
    if (!$month_flags[$month_index]) {
      if ($first_weekdays[$month_index] == $x) {
        $dates[$month_index][$x] = $day;
        $day++;
        $month_flags[$month_index] = true;
      } else {
        $dates[$month_index][$x] = 0;
      }
    } else {
      if ($day > cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year)) {
        $dates[$month_index][$x] = 0;
      } else {
        $dates[$month_index][$x] = $day;
      }
      $day++;
    }
  }
  $month_index++;
}

// Set weekend days
if (isset($_REQUEST['sofshavua'])) {
  $weekend_day_1 = 5;
  $weekend_day_2 = 6;
} else {
  $weekend_day_1 = 6;
  $weekend_day_2 = 7;
}

// Generate the table
$month_index = 1;
$day = 1;

if (isset($_REQUEST['layout']) && $_REQUEST['layout'] == 'aligned-weekdays') {
  // Aligned-weekdays layout
  while ($day <= 42) {
    echo '<tr>';
    while ($month_index <= 12) {
      $current_month = ($start_month + $month_index - 1) % 12 + 1;
      $current_year = $base_year + floor(($start_month + $month_index - 1) / 12);
      if ($dates[$month_index][$day] == 0) {
        echo '<td></td>';
      } else {
        $date = $current_year . '-' . str_pad($current_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($dates[$month_index][$day], 2, '0', STR_PAD_LEFT);
        if (date('N', strtotime($date)) == $weekend_day_1 || date('N', strtotime($date)) == $weekend_day_2) {
          echo '<td class="weekend">';
        } else {
          echo '<td>';
        }
        echo $dates[$month_index][$day];
        echo '</td>';
      }
      $month_index++;
    }
    echo '</tr>';
    $month_index = 1;
    $day++;
  }
} else {
  // Default layout
  while ($day <= 31) {
    echo '<tr>';
    while ($month_index <= 12) {
      $current_month = ($start_month + $month_index - 1) % 12 + 1;
      $current_year = $base_year + floor(($start_month + $month_index - 1) / 12);
      if ($day > cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year)) {
        echo '<td></td>';
        $month_index++;
        continue;
      }
      if (DateTime::createFromFormat('!Y-m-d', "$current_year-$current_month-$day")->format('N') == $weekend_day_1 || DateTime::createFromFormat('!Y-m-d', "$current_year-$current_month-$day")->format('N') == $weekend_day_2) {
        echo '<td class="weekend">';
      } else {
        echo '<td>';
      }
      echo '<span class="date">' . $day . '</span> <span class="day">' . substr(DateTime::createFromFormat('!Y-m-d', "$current_year-$current_month-$day")->format('D'), 0, 1) . '</span>';
      echo '</td>';
      $month_index++;
    }
    echo '</tr>';
    $month_index = 1;
    $day++;
  }
}
?>
</tbody>
</table>
</body>
</html>
