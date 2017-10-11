<?php

namespace Ioni\PayzenBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SubscriptionInfos.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class SubscriptionInfos
{
    const FREQ_DAY = 'day';
    const FREQ_WEEK = 'week';
    const FREQ_MONTH = 'month';
    const FREQ_YEAR = 'year';
    const FREQS = [self::FREQ_DAY, self::FREQ_WEEK, self::FREQ_MONTH, self::FREQ_YEAR];

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     */
    protected $amount;

    /**
     * @var \DateTimeInterface
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual("today")
     */
    protected $beginDate;

    /**
     * @var \DateTimeInterface
     *
     * @Assert\GreaterThanOrEqual("today")
     */
    protected $endDate;

    /**
     * @var string
     *
     * @see self::FREQS
     *
     * @Assert\NotBlank()
     */
    protected $frequency;

    /**
     * Amount of frequency.
     * Example : count=5, frequency=month => for 5 months.
     *
     * @var int 0 for infinity
     */
    protected $count;

    /**
     * @var int
     *
     * @Assert\Range(min="1", max="31")
     */
    protected $monthDay;

    /**
     * @var int
     *
     * @Assert\GreaterThanOrEqual(value="1")
     */
    protected $interval;

    public function __construct()
    {
        $this->interval = 1;
        $this->count = 0;
        $this->beginDate = new \DateTime();
    }

    /**
     * @return null|int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param null|int $amount
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getBeginDate(): \DateTimeInterface
    {
        return $this->beginDate;
    }

    /**
     * @param \DateTimeInterface $beginDate
     */
    public function setBeginDate(\DateTimeInterface $beginDate)
    {
        $this->beginDate = clone $beginDate;
    }

    /**
     * @return null|\DateTimeInterface
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param null|\DateTimeInterface $endDate
     */
    public function setEndDate(\DateTimeInterface $endDate = null)
    {
        $this->endDate = ($endDate !== null ? clone $endDate : null);
    }

    /**
     * @return null|string
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param null|string $frequency
     */
    public function setFrequency(string $frequency = null)
    {
        $this->frequency = $frequency;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count)
    {
        $this->count = $count;
        if ($this->count < 0) {
            $this->count = 0;
        }
    }

    /**
     * @return null|int
     */
    public function getMonthDay()
    {
        return $this->monthDay;
    }

    /**
     * @param null|int $monthDay
     */
    public function setMonthDay(int $monthDay = null)
    {
        $this->monthDay = $monthDay;
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     */
    public function setInterval(int $interval)
    {
        $this->interval = $interval;
    }
}
