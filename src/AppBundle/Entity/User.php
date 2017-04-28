<?php
// src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_user")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $username;
	
	/**
     * @ORM\Column(type="string", length=100)
     */
    private $password;
	
	/**
     * @ORM\Column(type="string", length=200)
     */
    private $email;
	
	/**
     * @ORM\Column(type="string", length=100)
     */
    private $role;
	
	/**
     * @ORM\ManyToOne(targetEntity="UserType", inversedBy="users")
     * @ORM\JoinColumn(name="user_type_id", referencedColumnName="id")
     */
    private $user_type;

    /**
     * @ORM\Column(type="date")
     */
    private $birthdate;
	
	/**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text")
     */
    private $description;
	
	/**
     * @ORM\Column(type="integer", length=2)
     */
    private $status;
	
	/**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $photo;
	
	/**
	 * @Assert\Image(maxSize="6000000")
	 */
	private $file;
	
	/**
     * @ORM\OneToMany(targetEntity="Dormitory", mappedBy="user")
     */
	private $dormitorys;
	
	/**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="user")
     */
	private $bookings;
	
	/**
     * @ORM\OneToMany(targetEntity="DormRating", mappedBy="user")
     */
	private $ratings;
	
	/**
     * @ORM\OneToMany(targetEntity="BookingSchedule", mappedBy="user")
     */
	private $booking_schedules;
	
	public function __construct()
	{
		$this->dormitorys = new ArrayCollection();
		$this->bookings = new ArrayCollection();
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
	
	public function getEmail() 
	{
		return $this->email;
		
	}
	
	public function getPhone() 
	{
		return $this->phone;
		
	}
	
	public function getStatus() 
	{
		return $this->status;
	}
	
	public function getUsername() 
	{
		return $this->username;
		
	}
	
	public function getPassword() 
	{
		return base64_decode($this->password);
	}
	
	public function getRoles() 
	{
		return explode(",",$this->role);
		
	}
	
	public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
	
	public function eraseCredentials()
    {
    }
	
	public function getBirthdate() 
	{
		return $this->birthdate->format('Y-m-d');
	}
	
	
	public function getDescription() 
	{
		return $this->description;
		
	}
	
	/**
     * Get userType
     *
     * @return \AppBundle\Entity\UserType
     */
    public function getUserType()
    {
        return $this->user_type;
    }
	
	/**
     * Get dormitorys
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDormitorys()
    {
        return $this->dormitorys;
    }
	
	public function setName($name) 
	{
		$this->name = $name;
	}
	
	public function setUsername($username) 
	{
		$this->username = $username;
	}
	
	public function setPassword($password) 
	{
		$this->password = base64_encode($password);
	}
	
	public function setEmail($email) 
	{
		$this->email = $email;
	}
	
	public function setPhone($phone) 
	{
		$this->phone = $phone;
	}
	
	public function setStatus($status) 
	{
		$this->status = $status;
	}
	
	public function setRole($role) 
	{
		$this->role = $role;
	}
	
	public function setUserType($user_type) 
	{
		$this->user_type = $user_type;
	}
	
	public function setBirthdate($birthdate) 
	{
		$this->birthdate = new \DateTime($birthdate);
	}
	
	public function setDescription($description) 
	{
		$this->description = $description;
	}
	
	/**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    

    /**
     * Add dormitory
     *
     * @param \AppBundle\Entity\Dormitory $dormitory
     *
     * @return User
     */
    public function addDormitory(\AppBundle\Entity\Dormitory $dormitory)
    {
        $this->dormitorys[] = $dormitory;

        return $this;
    }

    /**
     * Remove dormitory
     *
     * @param \AppBundle\Entity\Dormitory $dormitory
     */
    public function removeDormitory(\AppBundle\Entity\Dormitory $dormitory)
    {
        $this->dormitorys->removeElement($dormitory);
    }

    

    /**
     * Add booking
     *
     * @param \AppBundle\Entity\Booking $booking
     *
     * @return User
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
     * Add rating
     *
     * @param \AppBundle\Entity\DormRating $rating
     *
     * @return User
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
	 * Virtual getter that returns logo web path
	 * @return string 
	 */
	public function getAvatar() {
		return $this->getWebPath();
	}

	/**
	 * CHANGE UPLOAD DIR FOR YOUR NEEDS
	 * 
	 * @return string 
	 */
	private function getUploadDir() {
		return 'uploads/pics';
	}

	public function getFile() {
		return $this->file;
	}

	public function setFile($file) {
		$this->file = $file;
	}

	public function getPhoto() {
		return $this->photo;
	}

	public function setPhoto($photo) {
		$this->photo = $photo;
	}

	public function getAbsolutePath() {
		return null === $this->photo ? null : $this->getUploadRootDir() . '/' . $this->photo;
	}

	public function getWebPath() {
		return null === $this->photo ? null : $this->getUploadDir() . '/' . $this->photo;
	}

	private function getUploadRootDir() {
		// the absolute directory path where uploaded documents should be saved
		return __DIR__ . '/../../../web/' . $this->getUploadDir();
	}

	public function upload() {
		// the file property can be empty if the field is not required
		if (null === $this->file) {
			return;
		}
		if ($this->getPhoto()) {
			unlink($this->getUploadRootDir()."/".$this->getPhoto());
		}
		
		$Name = "user-profile-".$this->getId();

		$this->photo = $Name . '.' . $this->file->guessExtension();

		$this->file->move($this->getUploadRootDir(), $this->getPhoto());

		unset($this->file);
	}
	
	public function deletePhoto() {
		unlink($this->getUploadRootDir()."/".$this->getPhoto());
	}
	
	public function getPhotoBase64() {
		if ($this->getPhoto()) {
			$path = $this->getUploadRootDir()."/".$this->getPhoto();
		} else {
			$path = $this->getUploadRootDir()."/default-user.png";
		}
		
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}

    /**
     * Add bookingSchedule
     *
     * @param \AppBundle\Entity\BookingSchedule $bookingSchedule
     *
     * @return User
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
