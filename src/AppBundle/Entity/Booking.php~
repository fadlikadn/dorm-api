<?php
// src/AppBundle/Entity/Booking.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_booking")
 */
class Booking
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    
	/**
     * @ORM\Column(type="date")
     */
    private $checkin_date;
	
	/**
     * @ORM\Column(type="date")
     */
    private $checkout_date;
	
	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
	
	/**
     * @ORM\ManyToOne(targetEntity="Dormitory", inversedBy="bookings")
     * @ORM\JoinColumn(name="dorm_id", referencedColumnName="id")
     */
    private $dormitory;
	
	/**
     * @ORM\Column(type="string", length=100)
     */
    private $room_number;
	
    /**
     * @ORM\Column(type="text")
     */
    private $description;
	
	/**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $booking_date;
	
	/**
     * @ORM\Column(type="integer", length=2)
     */
    private $status;
	
	/**
     * @ORM\OneToMany(targetEntity="BookingSchedule", mappedBy="booking")
     */
	private $booking_schedules;
	
	public function __construct()
	{
		$this->booking_schedules = new ArrayCollection();
	}
	
	public function getId() 
	{
		return $this->id;
		
	}
	
	public function getCheckoutDate() 
	{
		return $this->checkout_date->format('Y-m-d');
		
	}
	
	public function getCheckinDate() 
	{
		return $this->checkin_date->format('Y-m-d');
		
	}
	
	public function getBookingDate() 
	{
		return $this->booking_date->format('Y-m-d H:i:s');
		
	}
	
	public function getDeadlineDate()
	{
		return $this->booking_date->modify('+2 day')->format('Y-m-d');
	}
	
	public function getDescription() 
	{
		return $this->description;
		
	}
	
	public function getRoomNumber() 
	{
		return $this->room_number;
		
	}
	
	public function getStatus() 
	{
		return $this->status;
	}
	
	/**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
	
	/**
     * Get dorm
     *
     * @return \AppBundle\Entity\Dormitory
     */
    public function getDormitory()
    {
        return $this->dormitory;
    }
	
	public function setCheckoutDate($checkout_date) 
	{
		$this->checkout_date = new \DateTime($checkout_date);
	}
	
	public function setCheckinDate($checkin_date) 
	{
		$this->checkin_date = new \DateTime($checkin_date);
	}
	
	public function setBookingDate() 
	{
		$this->booking_date = new \DateTime("NOW", new \DateTimeZone('Asia/Jakarta'));
	}
	
	public function setDescription($description) 
	{
		$this->description = $description;
	}
	
	public function setRoomNumber($room_number) 
	{
		$this->room_number = $room_number;
	}
	
	public function setStatus($status) 
	{
		$this->status = $status;
	}
	
	public function setUser($user) 
	{
		$this->user = $user;
	}
	
	public function setDormitory($dormitory) 
	{
		$this->dormitory = $dormitory;
	}
	
    

    /**
     * Add bookingSchedule
     *
     * @param \AppBundle\Entity\BookingSchedule $bookingSchedule
     *
     * @return Booking
     */
    public function addBookingSchedule(\AppBundle\Entity\BookingSchedule $bookingSchedule)
    {
        $this->booking_schedules[] = $bookingSchedule;

        return $this;
    }

    /**
     * Remove bookingSchedule
     *
     * @param \AppBundle\Entity\BookingSchedule $bookingSchedule
     */
    public function removeBookingSchedule(\AppBundle\Entity\BookingSchedule $bookingSchedule)
    {
        $this->booking_schedules->removeElement($bookingSchedule);
    }

    /**
     * Get bookingSchedules
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookingSchedules()
    {
        return $this->booking_schedules;
    }
}
