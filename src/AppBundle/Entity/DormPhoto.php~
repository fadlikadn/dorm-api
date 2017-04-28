<?php
// src/AppBundle/Entity/DormPhoto.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_dorm_photo")
 */
class DormPhoto
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $photo;
	
	/**
	 * @Assert\Image(maxSize="6000000")
	 */
	private $file;
	
	/**
     * @ORM\Column(type="string", length=200)
     */
    private $caption;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="Dormitory", inversedBy="photos")
     * @ORM\JoinColumn(name="dorm_id", referencedColumnName="id")
     */
    private $dormitory;
	
	/**
     * @ORM\Column(type="integer", length=2)
     */
    private $status;
	
	public function getId() 
	{
		return $this->id;
		
	}
	
	public function getStatus() 
	{
		return $this->status;
	}
	
	public function getCaption() 
	{
		return $this->caption;
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
	
	public function setStatus($status) 
	{
		$this->status = $status;
	}
	
	public function setCaption($caption) 
	{
		$this->caption = $caption;
	}
	
	public function setDormitory($dormitory) 
	{
		$this->dormitory = $dormitory;
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

		$hashName = sha1($this->file->getClientOriginalName() . $this->getId() . mt_rand(0, 99999));

		$this->photo = $hashName . '.' . $this->file->guessExtension();

		$this->file->move($this->getUploadRootDir(), $this->getPhoto());

		unset($this->file);
	}
	
	public function deletePhoto() {
		unlink($this->getUploadRootDir()."/".$this->getPhoto());
	}
	
}
