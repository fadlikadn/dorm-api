<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;
use AppBundle\Entity\UserType;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;

class ProfileController extends FOSRestController
{
	
    public function showAction()
    {
		$em = $this->getDoctrine()->getManager();
		$id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$user = $em->getRepository('AppBundle:User')->find($id);

		if ($user) {
			$data = array(
				'id' => $user->getId(),
				'name' => $user->getName(),
				'username' => $user->getUsername(),
				'email' => $user->getEmail(),
				'phone' => $user->getPhone(),
				'birthdate' => $user->getBirthdate(),
				'user_type' => $user->getUserType()->getName(),
			);
			$response = array('status' => 'OK', 'data' => $data);
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'ID not found');
		}
			
		return $this->sendApiresponse($response);
    }
	
	public function editsaveAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		
		if ($request->request->get('id') !== null) {
			$id = $request->request->get('id');
			if ($this->get('security.token_storage')->getToken()->getUser()->getId() == $id) {
				$user = $em->getRepository('AppBundle:User')->find($id);
				if ($user) {
					$user->setName($request->request->get('name'));
					$user->setEmail($request->request->get('email'));
					$user->setPhone($request->request->get('phone'));
					$user->setBirthdate($request->request->get('birthdate'));
					
					$em->persist($user);
					
					$em->flush();

					$response = array('status' => 'OK', 'id' => $user->getId());
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to edit this user');
			}
			
		}
        
		
		return $this->sendApiresponse($response);
    }
	
	public function changepasswordAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		
		$id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$user = $em->getRepository('AppBundle:User')->findOneBy(
			array('id' => $id, 'password' => base64_encode($request->request->get('oldpassword')))
		);
		if ($user) {
			$user->setPassword($request->request->get('newpassword'));
			
			$em->persist($user);
			
			$em->flush();

			$response = array('status' => 'OK', 'id' => $user->getId());
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Your current password might be wrong');
		}
        
		
		return $this->sendApiresponse($response);
    }
	
	public function uploadPhotoAction (Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		
		$user = $em->getRepository('AppBundle:User')->find($token_id);
		if ($user) {
			$user->setFile($this->getRequest()->files->get('file'));
			$user->upload();
			
			$em->persist($user);
			$em->flush();
			
			$data = array(
				'profpict' => $user->getPhotoBase64(),
			);

			$response = array('status' => 'OK', 'data' => $data);
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
		}
		
        return $this->sendApiresponse($response);
	}
	
	private function sendApiresponse($data) {
		$view = View::create()
			 ->setStatusCode(200)
			 ->setData($data)
			 ->setFormat('json')
		;
		return $this->get('fos_rest.view_handler')->handle($view);
	}
	
}
