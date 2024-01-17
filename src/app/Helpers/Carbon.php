<?php


namespace App\Helpers;

use Carbon\Carbon as BaseCarbon;

class Carbon extends BaseCarbon
{
    const DEFAULT_DATE_TIME_FORMAT = "m/d/Y H:i:s";
    const DEFAULT_DATE_TIME_12_FORMAT = "m/d/Y h:i:s a";
    const FILE_TIME_FORMAT = "H-i-s";
    const DEFAULT_DAY_MONTH_FORMAT = "jS F";
    const DEFAULT_DAY_MONTH_YEAR_FORMAT = "jS F Y";
    const DB_MONTH_DAY_FORMAT = "m-d";
    const DEFAULT_DB_DATE_FORMAT = "Y-m-d";
    const DEFAULT_DB_DATE_TIME_FORMAT = "Y-m-d H:i:s";
    const DEFAULT_OSS_DATE_FORMAT = "Ymd";

    public function toDefaultDateTimeFormat()
    {
        return $this->format(self::DEFAULT_DATE_TIME_FORMAT);
    }

    public function toDefaultDateTime12Format()
    {
        return $this->format(self::DEFAULT_DATE_TIME_12_FORMAT);
    }

    public function toFileTimeFormat()
    {
        return $this->format(self::FILE_TIME_FORMAT);
    }

    public function toDefaultMonthDayFormat()
    {
        return $this->format(self::DEFAULT_DAY_MONTH_FORMAT);
    }

    public function toDBMonthDayFormat()
    {
        return $this->format(self::DB_MONTH_DAY_FORMAT);
    }

    public function toDefaultDBDateFormat()
    {
        return $this->format(self::DEFAULT_DB_DATE_FORMAT);
    }

    public function toDefaultDBDateTimeFormat()
    {
        return $this->format(self::DEFAULT_DB_DATE_TIME_FORMAT);
    }

    public function toDefaultOssDateFormat()
    {
        return $this->format(self::DEFAULT_OSS_DATE_FORMAT);
    }

    public function toDefaultDayMonthYearFormat()
    {
        return $this->format(self::DEFAULT_DAY_MONTH_YEAR_FORMAT);
    }

}
