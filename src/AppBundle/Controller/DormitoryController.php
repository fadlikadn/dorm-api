<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Dormitory;
use AppBundle\Entity\DormPhoto;
use AppBundle\Entity\DormRating;
use AppBundle\Entity\BookingSchedule;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;

class DormitoryController extends FOSRestController
{
	
    public function showmapAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();

		$q  = $qb->select(array('p'))
			->from('AppBundle:Dormitory', 'p')
			->where('p.latitude > :sw_latitude')
			->andWhere('p.longitude > :sw_longitude')
			->andWhere('p.latitude < :ne_latitude')
			->andWhere('p.longitude < :ne_longitude')
			->setParameter('sw_latitude', $request->request->get('sw_latitude'))
			->setParameter('sw_longitude', $request->request->get('sw_longitude'))
			->setParameter('ne_latitude', $request->request->get('ne_latitude'))
			->setParameter('ne_longitude', $request->request->get('ne_longitude'))
			->getQuery();

        $datas = $q->getResult();
		$response['status'] = 'OK';
		if ($datas) {
            foreach ($datas as $data) {
                $response['data'][] = array(
                    'id' => $data->getId(),
                    'name' => $data->getName(),
					'longitude' => $data->getLongitude(),
                    'latitude' => $data->getLatitude(),
					'address' => $data->getAddress(),
					'price' => $data->getPrice(),
					'numberOfRoom' => $data->getNumberOfRoom(),
					'description' => $data->getDescription(),
					'dormProvider' => $data->getUser()->getName(),
					'telp' => $data->getUser()->getPhone(),
					'status' => $data->getStatus(),
                );
            }

            
        } else {
			$response['data'] = array();
        }
		
		return $this->sendApiresponse($response);
    }
	
	public function showAction()
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if (in_array("ROLE_ADMIN", $token_role)) {
			$dorms = $em->getRepository('AppBundle:Dormitory')->findAll();
			$data = array();
			if ($dorms) {
				foreach ($dorms as $dorm) {
					array_push($data, array(
						'id' => $dorm->getId(),
						'name' => $dorm->getName(),
						'longitude' => $dorm->getLongitude(),
						'latitude' => $dorm->getLatitude(),
						'address' => $dorm->getAddress(),
						'price' => $dorm->getPrice(),
						'description' => $dorm->getDescription(),
						'dormProvider' => $dorm->getUser()->getName(),
						'numberOfRoom' => $dorm->getNumberOfRoom(),
						'telp' => $dorm->getUser()->getPhone(),
						'status' => $dorm->getStatus(),
					));
				}

				$response = array('status' => 'OK', 'data' => $data);
			} else {
				$response = array('status' => 'OK', 'data' => array());
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed');
		}
		
		
		return $this->sendApiresponse($response);
    }
	
	public function showDetailAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$dorm = $em->getRepository('AppBundle:Dormitory')->find($id);
		if ($dorm) {
			$data = array(
				'id' => $dorm->getId(),
				'name' => $dorm->getName(),
				'longitude' => $dorm->getLongitude(),
				'latitude' => $dorm->getLatitude(),
				'address' => $dorm->getAddress(),
				'price' => $dorm->getPrice(),
				'numberOfRoom' => $dorm->getNumberOfRoom(),
				'description' => $dorm->getDescription(),
				'dormProvider' => $dorm->getUser()->getName(),
				'usernameDP' => $dorm->getUser()->getUsername(),
				'telp' => $dorm->getUser()->getPhone(),
				'status' => $dorm->getStatus(),
				'myrating' => 0,
				'photos' => array()
			);
			
			$user = $em->getRepository('AppBundle:User')->find($token_id);
			if ($user) {
				$myrating = $em->getRepository('AppBundle:DormRating')->findOneBy(array('dormitory' => $dorm, 'user' => $user));
				if ($myrating) {
					$data['myrating'] = $myrating->getRate();
				}
			}
			
			$photos = $em->getRepository('AppBundle:DormPhoto')->findBy(array('dormitory' => $dorm));
			if ($photos) {
				foreach ($photos as $photo) {
					array_push($data['photos'], array(
						'url' => $photo->getWebPath(),
						'status' => $photo->getStatus(),
						'caption' => $photo->getCaption(),
						'id' => $photo->getId(),
						'dp_username' => $dorm->getUser()->getUsername()
					));
				}
			}

			$response = array('status' => 'OK', 'data' => $data);
		} else {
			$response = array('status' => 'OK', 'data' => array());
		}
		
		
		return $this->sendApiresponse($response);
    }
	
	public function showdpAction()
    {
		$em = $this->getDoctrine()->getManager();
		
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if (in_array("ROLE_DP", $token_role)) {
			$user = $em->getRepository('AppBundle:User')->find($token_id);
			if ($user) {
				
				$dorms = $em->getRepository('AppBundle:Dormitory')->findBy(array('user' => $user));
				$data = array();
				if ($dorms) {
					foreach ($dorms as $dorm) {
						array_push($data, array(
							'id' => $dorm->getId(),
							'name' => $dorm->getName(),
							'longitude' => $dorm->getLongitude(),
							'latitude' => $dorm->getLatitude(),
							'address' => $dorm->getAddress(),
							'price' => $dorm->getPrice(),
							'numberOfRoom' => $dorm->getNumberOfRoom(),
							'description' => $dorm->getDescription(),
							'dormProvider' => $dorm->getUser()->getName(),
							'telp' => $dorm->getUser()->getPhone(),
							'status' => $dorm->getStatus(),
						));
					}

					$response = array('status' => 'OK', 'data' => $data);
				} else {
					$response = array('status' => 'OK', 'data' => array());
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
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
		if ($request->request->get('id') !== null) {
			$id = $request->request->get('id');
			if ((in_array("ROLE_ADMIN", $token_role))) {
				$dorm = $em->getRepository('AppBundle:Dormitory')->find($id);
				if ($dorm) {
					$dorm->setName($request->request->get('name'));
					$dorm->setLongitude($request->request->get('longitude'));
					$dorm->setLatitude($request->request->get('latitude'));
					$dorm->setAddress($request->request->get('address'));
					$dorm->setNumberOfRoom($request->request->get('number_of_room'));
					$dorm->setPrice($request->request->get('price'));
					$dorm->setDescription($request->request->get('description'));
					$dorm->setStatus($request->request->get('status'));
					
					$em->persist($dorm);
					
					$em->flush();

					$response = array('status' => 'OK', 'id' => $dorm->getId());
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to edit this dorm');
			}
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function editsavedpAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ($request->request->get('id') !== null) {
			$id = $request->request->get('id');
			if ((in_array("ROLE_DP", $token_role))) {
				$dorm = $em->getRepository('AppBundle:Dormitory')->find($id);
				if ($dorm) {
					if ($dorm->getUser()->getId() == $token_id) {
						$dorm->setName($request->request->get('name'));
						$dorm->setLongitude($request->request->get('longitude'));
						$dorm->setLatitude($request->request->get('latitude'));
						$dorm->setAddress($request->request->get('address'));
						$dorm->setNumberOfRoom($request->request->get('number_of_room'));
						$dorm->setPrice($request->request->get('price'));
						$dorm->setDescription($request->request->get('description'));
						$dorm->setStatus($request->request->get('status'));
						
						$em->persist($dorm);
						
						$em->flush();

						$response = array('status' => 'OK', 'id' => $dorm->getId());
					} else {
						$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to edit this dorm');
					}
					
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to edit this dorm');
			}
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function deleteAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ((in_array("ROLE_ADMIN", $token_role))) {
			$dorm = $em->getRepository('AppBundle:Dormitory')->find($id);
			if ($dorm) {
				$em->remove($dorm);
				$em->flush();

				$response = array('status' => 'OK');
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to delete this dorm');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function deletedpAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		
		if ((in_array("ROLE_DP", $token_role))) {
			$dorm = $em->getRepository('AppBundle:Dormitory')->find($id);
			if ($dorm) {
				if ($dorm->getUser()->getId() == $token_id) {
					$em->remove($dorm);
					$em->flush();

					$response = array('status' => 'OK');
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to delete this dorm');
				}
				
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to delete this dorm');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function bookingScheduleAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if ($request->request->get('id_dorm') !== null) {
			$id_dorm = $request->request->get('id_dorm');
			if ((in_array("ROLE_USER", $token_role))) {
				$dorm = $em->getRepository('AppBundle:Dormitory')->find($id_dorm);
				if ($dorm) {
					$qb = $em->createQueryBuilder();
					$q  = $qb->select('p.room_number')
						->from('AppBundle:BookingSchedule', 'p')
						->where('p.reservation_date >= :checkin_date')
						->andWhere('p.reservation_date < :checkout_date')
						->andWhere('p.dormitory = :dorm')
						->andWhere('p.status = :status')
						->setParameter('checkin_date', new \DateTime($request->request->get('checkin_date')))
						->setParameter('checkout_date', new \DateTime($request->request->get('checkout_date')))
						->setParameter('dorm', $dorm)
						->setParameter('status', 1)
						->distinct()
						->getQuery();

					$schedules = $q->getResult();
					$data = array();
					if ($schedules) {
						foreach ($schedules as $schedule) {
							array_push($data,$schedule['room_number']);
						}
					}

					$response = array('status' => 'OK', 'data' => $data);
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Id dorm Not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to edit this dorm');
			}
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function addsavedpAction(Request $request)
    {
		
		if ($request->request->get('name') !== null) {
			$em = $this->getDoctrine()->getManager();
			$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
			if ((in_array("ROLE_DP", $token_role))) {
				$user = $em->getRepository('AppBundle:User')->find($token_id);
				if ($user) {
					$dorm = new Dormitory();
					$dorm->setName($request->request->get('name'));
					$dorm->setLongitude($request->request->get('longitude'));
					$dorm->setLatitude($request->request->get('latitude'));
					$dorm->setAddress($request->request->get('address'));
					$dorm->setNumberOfRoom($request->request->get('number_of_room'));
					$dorm->setPrice($request->request->get('price'));
					$dorm->setDescription($request->request->get('description'));
					$dorm->setStatus($request->request->get('status'));
					$dorm->setUser($user);
					
					$em->persist($dorm);
					
					$em->flush();
					
					$data = array(
						'id' => $dorm->getId(),
						'telp' => $user->getPhone(),
						'dormProvider' => $user->getName(),
					);

					$response = array('status' => 'OK', 'data' => $data);
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
				}
				
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to add dormitory');
			}
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Cannot Get Data');
		}
		
        return $this->sendApiresponse($response);
    }
	
	public function uploadPhotoAction (Request $request)
	{
		if ($request->request->get('id_dorm') !== null) {
			$em = $this->getDoctrine()->getManager();
			$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
			
			$dorm = $em->getRepository('AppBundle:Dormitory')->find($request->request->get('id_dorm'));
			if ($dorm) {
				if (($dorm->getUser()->getId() == $token_id) or (in_array("ROLE_ADMIN", $token_role))) {
					$photo = new DormPhoto();
					$photo->setCaption($request->request->get('caption'));
					$photo->setDormitory($dorm);
					$photo->setStatus(1);
					$photo->setFile($this->getRequest()->files->get('file'));
					$photo->upload();
					
					$em->persist($photo);
					$em->flush();
					
					$data = array(
						'url' => $photo->getWebPath(),
						'caption' => $photo->getCaption(),
						'status' => $photo->getStatus(),
						'id' => $photo->getId(),
						'id_dorm' => $photo->getDormitory()->getId(),
						'dp_username' => $photo->getDormitory()->getUser()->getUsername(),
					);

					$response = array('status' => 'OK', 'data' => $data);
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Not Allowed');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Dorm Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Cannot Get Data');
		}
		
        return $this->sendApiresponse($response);
		
	}
	
	public function deletePhotoAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$photo = $em->getRepository('AppBundle:DormPhoto')->find($id);
		if ($photo) {
			if (($photo->getDormitory()->getUser()->getId() == $token_id) or (in_array("ROLE_ADMIN", $token_role))) {
				$photo->deletePhoto();
				$em->remove($photo);
				$em->flush();

				$response = array('status' => 'OK');
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to delete this photo');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Photo Not Found');
		}
		
        
		return $this->sendApiresponse($response);
    }
	
	public function countRatingAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$dorm = $em->getRepository('AppBundle:Dormitory')->find($id);
		if ($dorm) {
			$ratings = $em->getRepository('AppBundle:DormRating')->findBy(array('dormitory' => $dorm));
			$response = array('status' => 'OK', 'data' => 0);
			if ($ratings) {
				$count = 0;
				foreach ($ratings as $rating) {
					$count += $rating->getRate();
				}
				$response['data'] = $count/count($ratings);
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Dorm Not Found');
		}
		
		
		return $this->sendApiresponse($response);
    }
	
	public function ratingAction(Request $request)
    {
		if ($request->request->get('id_dorm') !== null) {
			$em = $this->getDoctrine()->getManager();
			$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
			if ((in_array("ROLE_USER", $token_role))) {
				$user = $em->getRepository('AppBundle:User')->find($token_id);
				if ($user) {
					$dorm = $em->getRepository('AppBundle:Dormitory')->find($request->request->get('id_dorm'));
					if ($dorm) {
						$check_rate = $em->getRepository('AppBundle:DormRating')->findOneBy(array('dormitory' => $dorm, 'user' => $user));
						if ($check_rate) {
							$check_rate->setRate($request->request->get('rate'));
							
							$em->persist($check_rate);
							$em->flush();
							
							$data = array(
								'id' => $check_rate->getId(),
							);

							$response = array('status' => 'OK', 'data' => $data);
						} else {
							$rating = new DormRating();
							$rating->setRate($request->request->get('rate'));
							$rating->setUser($user);
							$rating->setDormitory($dorm);
							
							$em->persist($rating);
							$em->flush();
							
							$data = array(
								'id' => $rating->getId(),
							);

							$response = array('status' => 'OK', 'data' => $data);
						}
					} else {
						$response = array('status' => 'ERROR', 'reason' => 'Dorm Not Found');
					}
					
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
				}
				
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to rate dormitory');
			}
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Cannot Get Data');
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
