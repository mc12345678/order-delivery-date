<?php
 /* 
 *
 * Altered for ORDER DELIVERY DATE contribution
 * Zen Cart Version: 1.3.8a
 * Modification Date: 2008-05-15
 * Author of this modification: MrMeech
 * Previous authors to this contribution are: Peter Martin (pe7er), James Betesh
 * This contribution is licensed under the GNU Public License V2.0
 * http://www.zen-cart.com/license/2_0.txt
 *
 */ 
?>
<script type="text/javascript">
window.addEvent('domready', function() { myCal = new Calendar({ date: 'Y-m-d' }, { blocked: ['24-25,31 12 *', '0 * * 0'], direction: 3, draggable: false }); });
</script>
<?php /*

Use the following properties to customize your website by adding them to the above Javascript DOM Event.

BLOCKED - An array of blocked (disabled) dates in the following format: 'day month year'. The syntax is similar to cron: the values are separated by spaces and may contain * (asterisk) - (dash) and , (comma) delimiters. For example blocked: ['1 1 2007'] would disable January 1, 2007; blocked: ['* 1 2007'] would disable all days (wildcard) in January, 2007; blocked: ['1-10 1 2007'] would disable January 1 through 10, 2007; while blocked: ['1,10 1 2007'] would disable January 1 and 10, 2007. In combination: blocked: ['1-10,20,22,24 1-3 *'] would disable 1 through 10, plus the 22nd and 24th of January through March for every (wildcard) year. There is an optional additional value which is day of the week (0 - 6 with 0 being Sunday). For example blocked: ['0 * 2007 0,6'] would disable all weekends (saturdays and sundays) in 2007. To block different days on different months, use this syntax: blocked: ['19 11 *', '20-25,31 12 *'] which will disable November 19th each year, and the 20-25th and 31st of December each year.
 
CLASSES - An array of 12 CSS classes that are applied to the calendar construct: calendar, prev, next, month, year, today, invalid, valid, inactive, active, hover, hilite. It is not necessary (as of RC3) to pass all 11 classes in the array: if you only wanted to change the 1st class you would set classes: ['alternate']; if you only wanted to change the 1st and 4th classes you would need to set classes: ['alternate', '', '', 'mes']. The first class, by default 'calendar', is applied to the div that contains the calendar element, the calendar button and the input or select it is appended to. The 'hilite' class (as of RC4) is applied to all days between 2 or more selected dates in multi-calendar functionality - see the demo for an example.

DAYS - An array of the 7 names of the days of the week, starting with sunday.

DIRECTION - A positive or negative integer that determines the calendar's direction: n (a positive number) the calendar is future-only beginning at n days after today; -n (a negative number) the calendar is past-only ending at n days before today; 0 (zero) the calendar has no future or past restrictions (default). Note if you would like the calendar to be directional starting from today–as opposed to (1) tomorrow or (-1) yesterday–use a positive or negative fraction, such as direction: .5 (future-only, starting today).

DRAGGABLE - A boolean value, true or false, to indicate if the calendar element will be draggable–useful in case the calendar obstructs (or is obstructed by) some other element on the page. Note, requires Mootools to be compiled with the Drag component.

MONTHS - An array of the 12 names of the month, starting with january.

NAVIGATION - An integer–0, 1 or 2–indicating which navigation method the class will use: traditional navigation (1) where left and right arrows allow the user to navigate by month (default); improved navigation (2) where the user can navigate by month or year; and static navigation (0) which is actually no navigation at all.

OFFSET - An integer that indicates the first day of the week, with 0 being Sunday (default).

onHideStart, onHideComplete, onShowStart, onShowComplete - Event handlers that can be used to trigger your own scripts.

PAD - An integer, the minimum number of days between calendars with multi-calendar functionality. For example: 1 (default) would enforce multiple calendars to space at least 1 day between picked dates; 7 would enforce a space of at least 1 week; while 0 (no padding) would allow multiple calendars to occupy the same date.

TWEAK - An object with { x: 0, y: 0} values that will "tweak" the calendar's placement on the page by x number of pixels horizontally and y number of pixels vertically.

*/ ?>