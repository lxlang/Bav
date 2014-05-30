<?php

namespace malkusch\bav;

/**
 * Abstract update plan.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @license GPL
 */
abstract class UpdatePlan
{

    /**
     * Bundesbank releases updates in those months
     */
    private static $updateMonths = array(
        3, // March
        6, // June
        9, // September
        12, // December
    );

    /**
     * Number of months before an update month when Bundesbank is supposed
     * to deliver the updated file.
     *
     * @var int
     */
    private static $relaseThreshold = 1;

    /**
     * Perform the update process.
     */
    abstract public function perform(\BAV_DataBackend $backend);

    /**
     * Returns true if the data is to old and needs an update
     *
     * @see BAV_DataBackend::getLastUpdate()
     * @return bool
     */
    public function isOutdated(\BAV_DataBackend $backend)
    {
        /*
         * The following code creates a list with the release months (update month - $relaseThreshold)
         * and the current month. Then sort that list and take the month before the current month as
         * the current threshold.
         * 
         * The current month gets an increment of 0.5 for the case that the current month is a 
         * release month (e.g. the list will look (2, 3.5, 5, 8, 11)).
         */
        $currentMonth = date("n", time()) + 0.5;

        $monthList = array($currentMonth);
        foreach (self::$updateMonths as $month) {
            $releaseMonth = $month - self::$relaseThreshold;
            $monthList[] = $releaseMonth;

        }
        sort($monthList); // You have now something like (2, 3.5, 5, 8, 11).

        // Now reflect the cycle between the last and the first month(11, 2, 3.5, 5, 8, 11, 2).
        $monthList[] = self::$updateMonths[0] - self::$relaseThreshold;
        array_unshift($monthList, self::$updateMonths[count(self::$updateMonths) - 1] - self::$relaseThreshold);
        
        $index = array_search($currentMonth, $monthList);
        assert($index !== false);
        $previousIndex = $index - 1;

        $thresholdMonth = $monthList[$previousIndex];
        $year = $thresholdMonth > $currentMonth ? date("Y", time()) - 1 : date("Y", time());

        $threshold = mktime(0, 0, 0, $thresholdMonth, 1, $year);
        
        return $backend->getLastUpdate() < $threshold;
    }
}
