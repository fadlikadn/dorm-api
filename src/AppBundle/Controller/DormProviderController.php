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

class DormProviderController extends FOSRestController
{
	
    public function showAction()
    {
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$em = $this->getDoctrine()->getManager();
		
		if (in_array("ROLE_ADMIN", $token_role)) {
			$user_type = $em->getRepository('AppBundle:UserType')->findBy(array('name' => 'Dorm Provider'));
			$user = $em->getRepository('AppBundle:User')->findBy(array('user_type' => $user_type));
			//$user = $em->getRepository('AppBundle:User')->findAll();
			if ($user) {
				$data = array();
				for ($i=0;$i<count($user);$i++) {
					array_push($data,array(
						'id' => $user[$i]->getId(),
						'name' => $user[$i]->getName(),
						'username' => $user[$i]->getUsername(),
						'email' => $user[$i]->getEmail(),
						'phone' => $user[$i]->getPhone(),
						'birthdate' => $user[$i]->getBirthdate(),
						'description' => $user[$i]->getDescription(),
						'status' => $user[$i]->getStatus(),
					));
				}
				
				$response = array('status' => 'OK', 'data' => $data);
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Data not found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed');
		}
		
		return $this->sendApiresponse($response);
    }
	
	public function editsaveAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ($request->request->get('id') !== null) {
			$id = $request->request->get('id');
			if ((in_array("ROLE_ADMIN", $token_role)) and ($token_id !== $id)) {
				$user = $em->getRepository('AppBundle:User')->find($id);
				if ($user) {
					$user->setName($request->request->get('name'));
					$user->setEmail($request->request->get('email'));
					$user->setPhone($request->request->get('phone'));
					$user->setBirthdate($request->request->get('birthdate'));
					$user->setStatus($request->request->get('status'));
					$user->setDescription($request->request->get('description'));
					if ($request->request->get('password') !== "") {
						$user->setPassword($request->request->get('password'));
					}
					
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
	
	public function deleteAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		
		if ((in_array("ROLE_ADMIN", $token_role)) and ($token_id !== $id)) {
			$user = $em->getRepository('AppBundle:User')->find($id);
			if ($user) {
				$em->remove($user);
				$em->flush();

				$response = array('status' => 'OK');
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to edit this user');
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
