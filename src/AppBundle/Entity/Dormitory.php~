<?php
// src/AppBundle/Entity/Dormitory.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_dormitory")
 */
class Dormitory
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;
	
	/**
     * @ORM\Column(type="string", length=500)
     */
    private $address;
	
	/**
     * @ORM\Column(type="decimal", precision=14, scale=11)
     */
    private $longitude;
	
	/**
     * @ORM\Column(type="decimal", precision=14, scale=11)
     */
    private $latitude;
	
	/**
     * @ORM\Column(type="integer", length=5)
     */
    private $number_of_room;
	
	/**
     * @ORM\Column(type="string", length=100)
     */
    private $price;
	
	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="dormitorys")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
	
    /**
     * @ORM\Column(type="text")
     */
    private $description;
	
	/**
     * @ORM\Column(type="integer", length=2)
     */
    private $status;
	
	/**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="dormitory")
     */
	private $bookings;
	
	/**
     * @ORM\OneToMany(targetEntity="DormPhoto", mappedBy="dormitory")
     */
	private $photos;
	
	/**
     * @ORM\OneToMany(targetEntity="DormRating", mappedBy="dormitory")
     */
	private $ratings;
	
	/**
     * @ORM\OneToMany(targetEntity="BookingSchedule", mappedBy="dormitory")
     */
	private $booking_schedules;
	
	public function __construct()
	{
		$this->bookings = new ArrayCollection();
		$this->photos = new ArrayCollection();
		$this->ratings = new ArrayCollection();
		$this->booking_schedules = new ArrayCollection();
	}
	
	public function getId() 
	{
		return $this->id;
		
	}
	
	public function getName() 
	{
		return $this->name;
		
	}
	
	public function getLongitude() 
	{
		return $this->longitude;
		
	}
	
	public function getLatitude() 
	{
		return $this->latitude;
		
	}
	
	public function getNumberOfRoom() 
	{
		return $this->number_of_room;
		
	}
	
	public function getPrice() 
	{
		return $this->price;
		
	}
	
	public function getDescription() 
	{
		return $this->description;
		
	}
	
	public function getStatus() 
	{
		return $this->status;
	}
	
	public function getAddress() 
	{
		return $this->address;
		
	}
	
	/**
     * Get userType
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
	
	public function setUser($user) 
	{
		$this->user = $user;
	}
	
	public function setName($name) 
	{
		$this->name = $name;
	}
	
	public function setLongitude($longitude) 
	{
		$this->longitude = $longitude;
	}
	
	public function setLatitude($latitude) 
	{
		$this->latitude = $latitude;
	}
	
	public function setNumberOfRoom($number_of_room) 
	{
		$this->number_of_room = $number_of_room;
	}
	
	public function setPrice($price) 
	{
		$this->price = $price;
	}
	
	public function setDescription($description) 
	{
		$this->description = $description;
	}
	
	public function setStatus($status) 
	{
		$this->status = $status;
	}
	
	public function setAddress($address) 
	{
		$this->address = $address;
	}
    

    /**
     * Add booking
     *
     * @param \AppBundle\Entity\Booking $booking
     *
     * @return Dormitory
     */
    public function addBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->bookings[] = $booking;

        return $this;
    }

    /**
     * Remove booking
     *
     * @param \AppBundle\Entity\Booking $booking
     */
    public function removeBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->bookings->removeElement($booking);
    }

    /**
     * Get bookings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookings()
    {
        return $this->bookings;
    }
	

    /**
     * Add photo
     *
     * @param \AppBundle\Entity\Booking $photo
     *
     * @return Dormitory
     */
    public function addPhoto(\AppBundle\Entity\Booking $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \AppBundle\Entity\Booking $photo
     */
    public function removePhoto(\AppBundle\Entity\Booking $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Add rating
     *
     * @param \AppBundle\Entity\DormRating $rating
     *
     * @return Dormitory
     */
    public function addRating(\AppBundle\Entity\DormRating $rating)
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove rating
     *
     * @param \AppBundle\Entity\DormRating $rating
     */
    public function removeRating(\AppBundle\Entity\DormRating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Add bookingSchedule
     *
     * @param \AppBundle\Entity\BookingSchedule $bookingSchedule
     *
     * @return Dormitory
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
