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
<p>👋 <strong>Hello!</strong> If you print this page, you'll get a nifty calendar that displays all of the year's dates on a single page. It will automatically fit on a single sheet of paper of any size. For best results, adjust your print settings to landscape orientation and disable the header and footer.</p>
<p>Take in the year all at once. Fold it up and carry it with you. Jot down your notes on it. Plan things out and observe the passage of time. Above all else, be kind to others.</p>
<p>Looking for <?php echo date("Y", strtotime("now + 1 year")); ?>? <a href="?year=<?php echo date("Y", strtotime("now + 1 year")); ?>">Here you go!</a></p>
<p style="font-size: 80%; color: #999;">Made by <a href="https://neatnik.net/">Neatnik</a> · Small changes made by <a href="https://jasonmlarsen.com/">Jason</a></p>
</div>
<?php
date_default_timezone_set('UTC');

// Parse startmonth parameter
$month_names = array('jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6, 
                     'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12);
$start_month = 1; // Default to January
if (isset($_REQUEST['startmonth']) && array_key_exists(strtolower($_REQUEST['startmonth']), $month_names)) {
  $start_month = $month_names[strtolower($_REQUEST['startmonth'])];
}

// Set the base year
$base_year = isset($_REQUEST['year']) ? (int)$_REQUEST['year'] : date('Y', time());
$now = strtotime($base_year.'-01-01');

// Calculate year display for header
$end_month = $start_month == 1 ? 12 : $start_month - 1;
$end_year = $start_month == 1 ? $base_year : $base_year + 1;
$year_display = ($base_year == $end_year) ? $base_year : "$base_year-$end_year";

$dates = array();
$month = 1;
$day = 1;

echo '<p>'.$year_display.'</p>';
echo '<table>';
echo '<thead>';
echo '<tr>';

// Add the month headings for 12 consecutive months starting from start_month
for($i = 0; $i < 12; $i++) {
  $current_month = ($start_month + $i - 1) % 12 + 1;
  echo '<th>'.DateTime::createFromFormat('!m', $current_month)->format('M').'</th>';
}
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Prepare a list of the first weekdays for each of the 12 months to display
$first_weekdays = array();

for($x = 1; $x <= 12; $x++) {
  // Calculate the actual month and year for this position
  $actual_month = ($start_month + $x - 2) % 12 + 1;
  $actual_year = $base_year + floor(($start_month + $x - 2) / 12);
  
  $first_weekdays[$x] = date('N', strtotime("$actual_year-$actual_month-01"));
  $$x = false; // Set a flag for each month position
}

// Start the loop around 12 month positions
while($month <= 12) {
  $day = 1;
  
  // Calculate actual month and year for this position
  $actual_month = ($start_month + $month - 2) % 12 + 1;
  $actual_year = $base_year + floor(($start_month + $month - 2) / 12);
  
  for($x = 1; $x <= 42; $x++) {
    if(!$$month) {
      if($first_weekdays[$month] == $x) {
        $dates[$month][$x] = $day;
        $day++;
        $$month = true;
      }
      else {
        $dates[$month][$x] = 0;
      }
    }
    else {
      // Ensure that we have a valid date
      if($day > cal_days_in_month(CAL_GREGORIAN, $actual_month, $actual_year)) {
        $dates[$month][$x] = 0;
      }
      else {
        $dates[$month][$x] = $day;
      }
      $day++;
    }
  }
  $month++;
}

// Now produce the table
$month = 1;
$day = 1;

if(isset($_REQUEST['sofshavua'])) {
  $weekend_day_1 = 5;
  $weekend_day_2 = 6;
}
else {
  $weekend_day_1 = 6;
  $weekend_day_2 = 7;
}

if(isset($_REQUEST['layout']) && $_REQUEST['layout'] == 'aligned-weekdays') {
  // Start the outer loop around 42 days (6 weeks at 7 days each)
  while($day <= 42) {
    echo '<tr>';
    // Start the inner loop around 12 months
    while($month <= 12) {
      if($dates[$month][$day] == 0) {
        echo '<td></td>';
      }
      else {
        // Calculate actual month and year for this position
        $actual_month = ($start_month + $month - 2) % 12 + 1;
        $actual_year = $base_year + floor(($start_month + $month - 2) / 12);
        
        $date = $actual_year.'-'.str_pad($actual_month, 2, '0', STR_PAD_LEFT).'-'.str_pad($dates[$month][$day], 2, '0', STR_PAD_LEFT);
        if(date('N', strtotime($date)) == $weekend_day_1 || date('N', strtotime($date)) == $weekend_day_2) {
          echo '<td class="weekend">';
        }
        else {
          echo '<td>';
        }
        echo $dates[$month][$day];
        echo '</td>';
      }
      $month++;
    }
    echo '</tr>';
    $month = 1;
    $day++;
  }
}
else {
  // Start the outer loop around 31 days
  while($day <= 31) {
    echo '<tr>';
    // Start the inner loop around 12 months
    while($month <= 12) {
      // Calculate actual month and year for this position
      $actual_month = ($start_month + $month - 2) % 12 + 1;
      $actual_year = $base_year + floor(($start_month + $month - 2) / 12);
      
      // If we've reached a point in the date matrix where the resulting date would be invalid
      if($day > cal_days_in_month(CAL_GREGORIAN, $actual_month, $actual_year)) {
        echo '<td></td>';
        $month++;
        continue;
      }
      // If the day falls on a weekend, apply a specific class for styles
      if(DateTime::createFromFormat('!Y-m-d', "$actual_year-$actual_month-$day")->format('N') == $weekend_day_1 || DateTime::createFromFormat('!Y-m-d', "$actual_year-$actual_month-$day")->format('N') == $weekend_day_2) {
        echo '<td class="weekend">';
      }
      else {
        echo '<td>';
      }
      // Display the day number and day of the week
      echo '<span class="date">'.$day.'</span> <span class="day">'.substr(DateTime::createFromFormat('!Y-m-d', "$actual_year-$actual_month-$day")->format('D'), 0, 1).'</span>';
      echo '</td>';
      $month++;
    }
    echo '</tr>';
    $month = 1;
    $day++;
  }
}

?>
</tbody>
</table>
</body>
</html>
