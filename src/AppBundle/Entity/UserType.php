<?php
// src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_user_type")
 */
class UserType
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
     * @ORM\OneToMany(targetEntity="User", mappedBy="user_type")
     */
	private $users;
	
	public function __construct()
	{
		$this->users = new ArrayCollection();
	}
	
	public function getId() 
	{
		return $this->id;
		
	}
	
	public function getName() 
	{
		return $this->name;
		
	}
	
	public function setName($name) 
	{
		$this->name = $name;
	}
	

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserType
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
