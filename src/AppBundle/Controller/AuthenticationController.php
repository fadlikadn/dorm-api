<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;

class AuthenticationController extends FOSRestController
{
	
	public function loginAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:User');
		
		$user = $repository->findOneBy(
			array(
				'username' => $request->request->get('username'), 
				'password' => base64_encode($request->request->get('password')), 
				'status' => 1
			)
		);
		$response = array();
		if ($user) {
			$token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);
			$response['status'] = 'OK';
			$response['token'] = $token;
			$response['name'] = $user->getName();
			$response['role'] = implode(',',$user->getRoles());
			$response['profpict'] = $user->getPhotoBase64();
		} else {
			$response['status'] = 'ERROR';
			$response['reason'] = 'Username and Password not match or your account is inactive';
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
