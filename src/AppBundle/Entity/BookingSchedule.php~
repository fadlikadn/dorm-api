<?php
// src/AppBundle/Entity/Booking.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_booking_schedule")
 */
class BookingSchedule
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
    private $reservation_date;
	
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
     * @ORM\ManyToOne(targetEntity="Booking", inversedBy="booking_schedules")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
     */
    private $booking;
	
	/**
     * @ORM\Column(type="integer", length=2)
     */
    private $room_number;
	
	/**
     * @ORM\Column(type="integer", length=2)
     */
    private $status;
	
	public function getId() 
	{
		return $this->id;
		
	}
	
	public function getReservationDate() 
	{
		return $this->reservation_date->format('Y-m-d');
		
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
	
	/**
     * Get dorm
     *
     * @return \AppBundle\Entity\Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }
	
	public function setReservationDate($reservation_date) 
	{
		$this->reservation_date = new \DateTime($reservation_date);
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
	
	public function setBooking($booking) 
	{
		$this->booking = $booking;
	}
	
    
}
