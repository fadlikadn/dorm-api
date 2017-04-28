<?php
// src/AppBundle/Entity/DormRating.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_dorm_rating")
 */
class DormRating
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ratings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
	
	/**
     * @ORM\ManyToOne(targetEntity="Dormitory", inversedBy="ratings")
     * @ORM\JoinColumn(name="dorm_id", referencedColumnName="id")
     */
    private $dormitory;
	
	/**
     * @ORM\Column(type="integer", length=2)
     */
    private $rate;
	
	public function getId() 
	{
		return $this->id;
		
	}
	
	public function getRate() 
	{
		return $this->rate;
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
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
	
	public function setRate($rate) 
	{
		$this->rate = $rate;
	}
	
	public function setUser($user) 
	{
		$this->user = $user;
	}
	
	public function setDormitory($dormitory) 
	{
		$this->dormitory = $dormitory;
	}
}
