<?php
class Library
{
    public static function getUrlServerName()
    {
        $s = (empty($_SERVER["HTTPS"]))? null : (($_SERVER["HTTPS"] == "on")? "s" : null);
        $p = strtolower($_SERVER["SERVER_PROTOCOL"]);

        $protocol = substr($p, 0, strpos($p, "/")) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80")? null : ":" . $_SERVER["SERVER_PORT"];

        return $protocol . "://" . $_SERVER["SERVER_NAME"] . $port;
    }

    public static function getUrl()
    {
        return self::getUrlServerName() . $_SERVER["REQUEST_URI"];
    }

    public static function getDateIniEnd($dateFilter, $dateFrom, $dateTo)
    {
        $dateIni = null;
        $dateEnd = null;

        $dayInSeconds = (24 / 1) * (60 / 1) * (60 / 1);
        $firstDayWeek = strtotime("today") - ((intval(date("N")) - 1) * $dayInSeconds);

        switch ($dateFilter) {
            case "TODAY":
                $todayIni = date("Y-m-d H:i:s", strtotime("today"));
                $todayEnd = date("Y-m-d H:i:s", strtotime("today 23:59:59"));
                $dateIni = $todayIni;
                $dateEnd = $todayEnd;
                break;
            case "YESTERDAY":
                $yesterdayIni = date("Y-m-d H:i:s", strtotime("yesterday"));
                $yesterdayEnd = date("Y-m-d H:i:s", strtotime("yesterday 23:59:59"));
                $dateIni = $yesterdayIni;
                $dateEnd = $yesterdayEnd;
                break;
            case "THIS_WEEK":
                $thisWeekIni = date("Y-m-d H:i:s", $firstDayWeek);
                $thisWeekEnd = date("Y-m-d 23:59:59", $firstDayWeek + (6 * $dayInSeconds));
                $dateIni = $thisWeekIni;
                $dateEnd = $thisWeekEnd;
                break;
            case "PREVIOUS_WEEK":
                $previousWeekIni = date("Y-m-d 00:00:00", $firstDayWeek - (6 * $dayInSeconds));
                $previousWeekEnd = date("Y-m-d 23:59:59", $firstDayWeek - 1);
                $dateIni = $previousWeekIni;
                $dateEnd = $previousWeekEnd;
                break;
            case "THIS_MONTH":
                $thisMonthIni = date("Y-m-01 00:00:00");
                $thisMonthEnd = date("Y-m-t 23:59:59");
                $dateIni = $thisMonthIni;
                $dateEnd = $thisMonthEnd;
                break;
            case "PREVIOUS_MONTH":
                $previousMonthIni = date("Y-m-01 00:00:00", strtotime("previous month"));
                $previousMonthEnd = date("Y-m-t 23:59:59", strtotime("previous month"));
                $dateIni = $previousMonthIni;
                $dateEnd = $previousMonthEnd;
                break;
            case "THIS_YEAR":
                $thisYearIni = date("Y-m-d H:i:s", strtotime("jan " . intval(date("Y"))));
                $thisYearEnd = date("Y-m-d H:i:s", strtotime("Dec 31 " . intval(date("Y")) . " 23:59:59"));
                $dateIni = $thisYearIni;
                $dateEnd = $thisYearEnd;
                break;
            case "PREVIOUS_YEAR":
                $previousYearIni = date("Y-m-d H:i:s", strtotime("jan " . (intval(date("Y")) - 1)));
                $previousYearEnd = date("Y-m-d H:i:s", strtotime("Dec 31 " . (intval(date("Y")) - 1) . " 23:59:59"));
                $dateIni = $previousYearIni;
                $dateEnd = $previousYearEnd;
                break;
            case "CUSTOM":
                $dateIni = $dateFrom . " 00:00:00";
                $dateEnd = $dateTo . " 23:59:59";
                break;
        }

        return array($dateIni, $dateEnd);
    }
}

