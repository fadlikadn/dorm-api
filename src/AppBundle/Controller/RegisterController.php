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

class RegisterController extends FOSRestController
{
	
    public function submitAction(Request $request)
    {
		
		if ($request->request->get('name') !== null) {
			$em = $this->getDoctrine()->getManager();
			$type_id = (is_numeric($request->request->get('type'))) ? $request->request->get('type') : 0;
			$type = $em->getRepository('AppBundle:UserType')->find($type_id);
			if ($type) {
				$user = new User();
				$user->setName($request->request->get('name'));
				$user->setUsername($request->request->get('username'));
				$user->setPassword($request->request->get('password'));
				
				$user->setBirthdate($request->request->get('birthdate'));
				$user->setUserType($type);
				$user->setEmail($request->request->get('email'));
				$user->setPhone($request->request->get('phone'));
				if ($type->getName() == 'User') {
					$user->setStatus(1);
					$user->setRole("ROLE_USER");
					$response = array('status' => 'OK', 'reason' => 'You have been registered successfully');
				} else {
					$user->setStatus(0);
					$user->setRole("ROLE_DP");
					$response = array('status' => 'OK', 'reason' => 'Waiting for administrator approval');
				}
				
				$user->setDescription("");
				
				$em->persist($user);
				
				$em->flush();

				$response['id'] = $user->getId();
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'User Type not Found');
			}
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Cannot Get Data');
		}
		
        return $this->sendApiresponse($response);
    }
	
	public function getTypeAction()
    {
		$em = $this->getDoctrine()->getManager();
        $datas = $em->getRepository('AppBundle:UserType')->findAll();
		
		if ($datas) {
            foreach ($datas as $data) {
				if ($data->getId() !== 1) {
					$response['data'][] = array(
						'id' => $data->getId(),
						'name' => $data->getName(),
					);
				}
            }
            $response['status'] = 'OK';
        } else {
            $response['status'] = 'ERROR';
			$response['reason'] = 'Cannot Find Data';
        }
		
		return $this->sendApiresponse($response);
        //return $this->handleView($view);
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
