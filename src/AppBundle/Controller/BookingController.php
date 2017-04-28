<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Dormitory;
use AppBundle\Entity\Booking;
use AppBundle\Entity\BookingSchedule;
use AppBundle\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;

class BookingController extends FOSRestController
{
	
	public function submitAction(Request $request)
    {
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if (in_array("ROLE_USER", $token_role)) {
			$em = $this->getDoctrine()->getManager();
			$dorm = $em->getRepository('AppBundle:Dormitory')->find($request->request->get('id_dorm'));
			if ($request->request->get('room_number') !== "") {
				if ($dorm) {
					if ($dorm->getStatus() == 1) {
						$qb = $em->createQueryBuilder();
						$q  = $qb->select('p.room_number')
							->from('AppBundle:BookingSchedule', 'p')
							->where('p.reservation_date > :checkin_date')
							->andWhere('p.reservation_date < :checkout_date')
							->andWhere('p.dormitory = :dorm')
							->andWhere('p.room_number IN (:room_number)')
							->andWhere('p.status = :status')
							->setParameter('checkin_date', new \DateTime($request->request->get('checkin_date')))
							->setParameter('checkout_date', new \DateTime($request->request->get('checkout_date')))
							->setParameter('dorm', $dorm)
							->setParameter('room_number', explode(',',$request->request->get('room_number')))
							->setParameter('status', 1)
							->distinct()
							->getQuery();

						$schedules = $q->getResult();
						
						if (!$schedules) {
							$user = $em->getRepository('AppBundle:User')->find($token_id);
							if ($user) {
								$booking = new Booking();
								$booking->setCheckinDate($request->request->get('checkin_date'));
								$booking->setCheckoutDate($request->request->get('checkout_date'));
								$booking->setDescription($request->request->get('description'));
								$booking->setBookingDate();
								$booking->setStatus(1);
								$booking->setUser($user);
								$booking->setDormitory($dorm);
								$booking->setRoomNumber($request->request->get('room_number'));
								
								$em->persist($booking);
								
								$em->flush();
								
								$body_message = "
									Hi ".$user->getName().",<br><br>
									
									You just book a room using Fiding Dorm Application. Details :<br>
									1. Dorm Name  : ".$dorm->getName()."<br>
									2. Booking No : ".$booking->getId()."<br>
									3. Check In   : ".$booking->getCheckinDate()."<br>
									4. Check Out  : ".$booking->getCheckoutDate()."<br>
									5. Book Date  : ".$booking->getBookingDate()."<br>
									6. Desc.      : ".$booking->getDescription()."<br>
									7. Room       : ".$booking->getRoomNumber()."<br>
									8. Status     : Waiting for Approval<br><br>
									
									Regards,<br>
									Admin DormApp
								";
								
								$this->sendEmail($booking->getUser()->getEmail(),'Booking Notification',$body_message);
								
								$body_message = "
									Hi ".$booking->getDormitory()->getUser()->getName().",<br><br>
									
									Someone just book a room for your dormitory using Fiding Dorm Application. Details :<br>
									1. Dorm Name  : ".$booking->getDormitory()->getName()."<br>
									2. Cust Name  : ".$booking->getUser()->getName()."<br>
									3. Booking No : ".$booking->getId()."<br>
									4. Check In   : ".$booking->getCheckinDate()."<br>
									5. Check Out  : ".$booking->getCheckoutDate()."<br>
									6. Book Date  : ".$booking->getBookingDate()."<br>
									7. Deadline   : ".$booking->getDeadlineDate()."<br>
									8. Desc.      : ".$booking->getDescription()."<br>
									9. Room       : ".$booking->getRoomNumber()."<br>
									10. Status    : Waiting for Approval<br><br>
									
									Regards,<br>
									Admin DormApp
								";
								
								$this->sendEmail($booking->getDormitory()->getUser()->getEmail(),'Booking Notification',$body_message);
								
								$response = array('status' => 'OK', 'data' => $booking->getId());
							} else {
								$response = array('status' => 'ERROR', 'reason' => 'User not Found');
							}
						} else {
							$response = array('status' => 'ERROR', 'reason' => 'Someone has Already Book for this period');
						}
						
					} else {
						$response = array('status' => 'ERROR', 'reason' => 'Dorm not Available');
					}
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Dorm not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'You did not choose any room');
			}
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed');
		}
		
		
        return $this->sendApiresponse($response);
    }
	
	public function mybookingAction()
    {
		$em = $this->getDoctrine()->getManager();
		
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if (in_array("ROLE_USER", $token_role)) {
			$user = $em->getRepository('AppBundle:User')->find($token_id);
			if ($user) {
				
				$bookings = $em->getRepository('AppBundle:Booking')->findBy(array('user' => $user));
				$data = array();
				if ($bookings) {
					foreach ($bookings as $booking) {
						array_push($data, array(
							'id' => $booking->getId(),
							'checkout_date' => $booking->getCheckoutDate(),
							'checkin_date' => $booking->getCheckinDate(),
							'description' => $booking->getDescription(),
							'status' => $booking->getStatus(),
							'room_number' => $booking->getRoomNumber(),
							'dorm_name' => $booking->getDormitory()->getName(),
							'dorm_id' => $booking->getDormitory()->getId(),
							'dp_name' => $booking->getDormitory()->getUser()->getName(),
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
	
	public function dpbookingAction()
    {
		$em = $this->getDoctrine()->getManager();
		
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if (in_array("ROLE_DP", $token_role)) {
			$dp = $em->getRepository('AppBundle:User')->find($token_id);
			if ($dp) {
				$dorms = $em->getRepository('AppBundle:Dormitory')->findBy(array('user' => $dp));
				$data = array();
				if ($dorms) {
					foreach ($dorms as $dorm) {
						$bookings = $em->getRepository('AppBundle:Booking')->findBy(array('dormitory' => $dorm));
						if ($bookings) {
							foreach ($bookings as $booking) {
								array_push($data, array(
									'id' => $booking->getId(),
									'checkout_date' => $booking->getCheckoutDate(),
									'checkin_date' => $booking->getCheckinDate(),
									'description' => $booking->getDescription(),
									'status' => $booking->getStatus(),
									'room_number' => $booking->getRoomNumber(),
									'dorm_name' => $booking->getDormitory()->getName(),
									'dorm_id' => $booking->getDormitory()->getId(),
									'user_name' => $booking->getUser()->getName(),
								));
							}
						}
					}
				}
				$response = array('status' => 'OK', 'data' => $data);
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed');
		}
		
		
		return $this->sendApiresponse($response);
    }
	
	public function adminbookingAction()
    {
		$em = $this->getDoctrine()->getManager();
		
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if (in_array("ROLE_ADMIN", $token_role)) {
			$bookings = $em->getRepository('AppBundle:Booking')->findAll();
			$data = array();
			if ($bookings) {
				foreach ($bookings as $booking) {
					array_push($data, array(
						'id' => $booking->getId(),
						'checkout_date' => $booking->getCheckoutDate(),
						'checkin_date' => $booking->getCheckinDate(),
						'description' => $booking->getDescription(),
						'status' => $booking->getStatus(),
						'room_number' => $booking->getRoomNumber(),
						'dorm_name' => $booking->getDormitory()->getName(),
						'dorm_id' => $booking->getDormitory()->getId(),
						'user_name' => $booking->getUser()->getName(),
					));
				}
			}
			$response = array('status' => 'OK', 'data' => $data);
			
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed');
		}
		
		
		return $this->sendApiresponse($response);
    }
	
	public function deleteAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ((in_array("ROLE_USER", $token_role))) {
			$user = $em->getRepository('AppBundle:User')->find($token_id);
			if ($user) {
				$booking = $em->getRepository('AppBundle:Booking')->find($id);
				if ($booking) {
					if ($booking->getUser()->getId() == $user->getId()) {
						$usr_name = $booking->getUser()->getName();
						$usr_mail = $booking->getUser()->getEmail();
						$dp_name = $booking->getDormitory()->getUser()->getName();
						$dp_mail = $booking->getDormitory()->getUser()->getEmail();
						$dorm_name = $booking->getDormitory()->getName();
						$book_id = $booking->getId();
						$checkin = $booking->getCheckinDate();
						$book_date = $booking->getBookingDate();
						$room_number = $booking->getRoomNumber();
						$checkout = $booking->getCheckoutDate();
						$desc = $booking->getDescription();
						
						$em->remove($booking);
						$em->flush();
						
						$body_message = "
							Hi ".$usr_name.",<br><br>
							
							Here is update for your booking. Details :<br>
							1. Dorm Name  : ".$dorm_name."<br>
							2. Booking No : ".$book_id."<br>
							3. Check In   : ".$checkin."<br>
							4. Check Out  : ".$checkout."<br>
							5. Room Number: ".$room_number."<br>
							6. Book Date  : ".$book_date."<br>
							7. Desc.      : ".$desc."<br>
							8. Status     : Cancelled by User<br><br>
							
							Regards,<br>
							Admin DormApp
						";
						
						$this->sendEmail($usr_mail,'Booking Notification',$body_message);
						
						$body_message = "
							Hi ".$dp_name.",<br><br>
							
							Someone just cancel their booking for your dormitory using Fiding Dorm Application. Details :<br>
							1. Dorm Name  : ".$dorm_name."<br>
							2. Cust Name  : ".$usr_name."<br>
							3. Booking No : ".$book_id."<br>
							4. Check In   : ".$checkin."<br>
							5. Check Out  : ".$checkout."<br>
							6. Room Number: ".$room_number."<br>
							7. Book Date  : ".$book_date."<br>
							8. Desc.      : ".$desc."<br>
							9. Status     : Cancelled by User<br><br>
							
							Regards,<br>
							Admin DormApp
						";
						
						$this->sendEmail($dp_mail,'Booking Notification',$body_message);
						
						$response = array('status' => 'OK');
					} else {
						$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to delete this booking');
					}
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to delete this booking');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function dprejectbookingAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ((in_array("ROLE_DP", $token_role))) {
			$user = $em->getRepository('AppBundle:User')->find($token_id);
			if ($user) {
				$booking = $em->getRepository('AppBundle:Booking')->find($id);
				if ($booking) {
					if ($booking->getDormitory()->getUser()->getId() == $user->getId()) {
						$booking->setStatus(2);
					
						$em->persist($booking);
						$em->flush();
						
						$body_message = "
							Hi ".$booking->getUser()->getName().",<br><br>
							
							Here is update for your booking. Details :<br>
							1. Dorm Name  : ".$booking->getDormitory()->getName()."<br>
							2. Booking No : ".$booking->getId()."<br>
							3. Check In   : ".$booking->getCheckinDate()."<br>
							4. Check Out  : ".$booking->getCheckoutDate()."<br>
							5. Book Date  : ".$booking->getBookingDate()."<br>
							6. Desc.      : ".$booking->getDescription()."<br>
							7. Status     : Rejected by Dorm Provider<br><br>
							
							Regards,<br>
							Admin DormApp
						";
						
						$this->sendEmail($booking->getUser()->getEmail(),'Booking Notification',$body_message);
						
						$body_message = "
							Hi ".$booking->getDormitory()->getUser()->getName().",<br><br>
							
							You just reject a booking for your dormitory using Fiding Dorm Application. Details :<br>
							1. Dorm Name  : ".$booking->getDormitory()->getName()."<br>
							2. Cust Name  : ".$booking->getUser()->getName()."<br>
							3. Booking No : ".$booking->getId()."<br>
							4. Check In   : ".$booking->getCheckinDate()."<br>
							5. Check Out  : ".$booking->getCheckoutDate()."<br>
							6. Book Date  : ".$booking->getBookingDate()."<br>
							7. Desc.      : ".$booking->getDescription()."<br>
							8. Status     : Rejected by Dorm Provider<br><br>
							
							Regards,<br>
							Admin DormApp
						";
						
						$this->sendEmail($booking->getDormitory()->getUser()->getEmail(),'Booking Notification',$body_message);
						
						$response = array('status' => 'OK');
						
					} else {
						$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to reject this booking');
					}
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to reject this booking');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function adminrejectbookingAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		if ((in_array("ROLE_ADMIN", $token_role))) {
			$booking = $em->getRepository('AppBundle:Booking')->find($id);
			if ($booking) {
				$booking->setStatus(2);
				
				$em->persist($booking);
				$em->flush();
				
				$body_message = "
					Hi ".$booking->getUser()->getName().",<br><br>
					
					Here is update for your booking. Details :<br>
					1. Dorm Name  : ".$booking->getDormitory()->getName()."<br>
					2. Booking No : ".$booking->getId()."<br>
					3. Check In   : ".$booking->getCheckinDate()."<br>
					4. Check Out  : ".$booking->getCheckoutDate()."<br>
					5. Book Date  : ".$booking->getBookingDate()."<br>
					6. Desc.      : ".$booking->getDescription()."<br>
					7. Status     : Rejected by Administrator<br><br>
					
					Regards,<br>
					Admin DormApp
				";
				
				$this->sendEmail($booking->getUser()->getEmail(),'Booking Notification',$body_message);
				
				$body_message = "
					Hi ".$booking->getDormitory()->getUser()->getName().",<br><br>
					
					Administrator just reject a booking for your dormitory using Fiding Dorm Application. Details :<br>
					1. Dorm Name  : ".$booking->getDormitory()->getName()."<br>
					2. Cust Name  : ".$booking->getUser()->getName()."<br>
					3. Booking No : ".$booking->getId()."<br>
					4. Check In   : ".$booking->getCheckinDate()."<br>
					5. Check Out  : ".$booking->getCheckoutDate()."<br>
					6. Book Date  : ".$booking->getBookingDate()."<br>
					7. Desc.      : ".$booking->getDescription()."<br>
					8. Status     : Rejected by Administrator<br><br>
					
					Regards,<br>
					Admin DormApp
				";
				
				$this->sendEmail($booking->getDormitory()->getUser()->getEmail(),'Booking Notification',$body_message);

				$response = array('status' => 'OK');
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to reject this booking');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function dpapprovebookingAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$token_role = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
		$token_id = $this->get('security.token_storage')->getToken()->getUser()->getId();
		if ((in_array("ROLE_DP", $token_role))) {
			$user = $em->getRepository('AppBundle:User')->find($token_id);
			if ($user) {
				$booking = $em->getRepository('AppBundle:Booking')->find($id);
				if ($booking) {
					if ($booking->getDormitory()->getUser()->getId() == $user->getId()) {
						$qb = $em->createQueryBuilder();
						$q  = $qb->select('count(p.id) as cnt')
							->from('AppBundle:BookingSchedule', 'p')
							->where('p.reservation_date >= :checkin_date')
							->andWhere('p.reservation_date < :checkout_date')
							->andWhere('p.dormitory = :dorm')
							->andWhere('p.room_number IN (:room_number)')
							->andWhere('p.status = :status')
							->setParameter('checkin_date', $booking->getCheckinDate())
							->setParameter('checkout_date', $booking->getCheckoutDate())
							->setParameter('dorm', $booking->getDormitory())
							->setParameter('room_number', explode(',',$booking->getRoomNumber()))
							->setParameter('status', 1)
							->getQuery();
							
						$schedules = $q->getResult();
						
						if ($schedules[0]['cnt'] == 0) {
							$begin = new \DateTime($booking->getCheckinDate());
							$end = new \DateTime($booking->getCheckoutDate());

							$interval = \DateInterval::createFromDateString('1 day');
							$period = new \DatePeriod($begin, $interval, $end);
							$rooms = explode(',',$booking->getRoomNumber());
							foreach ( $rooms as $room ) {
								foreach ( $period as $dt ) {
									$schedule = new BookingSchedule();
									$schedule->setReservationDate($dt->format( "Y-m-d"));
									$schedule->setRoomNumber($room);
									$schedule->setStatus(1);
									$schedule->setUser($booking->getUser());
									$schedule->setDormitory($booking->getDormitory());
									$schedule->setBooking($booking);
									$em->persist($schedule);
									$em->flush();
								}
							}
							
							
							$booking->setStatus(3);
						
							$em->persist($booking);
							$em->flush();
							
							$body_message = "
								Hi ".$booking->getUser()->getName().",<br><br>
								
								Here is update for your booking. Details :<br>
								1. Dorm Name  : ".$booking->getDormitory()->getName()."<br>
								2. Booking No : ".$booking->getId()."<br>
								3. Check In   : ".$booking->getCheckinDate()."<br>
								4. Check Out  : ".$booking->getCheckoutDate()."<br>
								5. Book Date  : ".$booking->getBookingDate()."<br>
								6. Desc.      : ".$booking->getDescription()."<br>
								7. Room       : ".$booking->getRoomNumber()."<br>
								8. Status     : Approved by Dorm Provider<br><br>
								
								Regards,<br>
								Admin DormApp
							";
							
							$this->sendEmail($booking->getUser()->getEmail(),'Booking Notification',$body_message);
							
							$body_message = "
								Hi ".$booking->getDormitory()->getUser()->getName().",<br><br>
								
								You just approve a booking for your dormitory using Fiding Dorm Application. Details :<br>
								1. Dorm Name  : ".$booking->getDormitory()->getName()."<br>
								2. Cust Name  : ".$booking->getUser()->getName()."<br>
								3. Booking No : ".$booking->getId()."<br>
								4. Check In   : ".$booking->getCheckinDate()."<br>
								5. Check Out  : ".$booking->getCheckoutDate()."<br>
								6. Book Date  : ".$booking->getBookingDate()."<br>
								7. Desc.      : ".$booking->getDescription()."<br>
								8. Room       : ".$booking->getRoomNumber()."<br>
								9. Status     : Approved by Dorm Provider<br><br>
								
								Regards,<br>
								Admin DormApp
							";
							
							$this->sendEmail($booking->getDormitory()->getUser()->getEmail(),'Booking Notification',$body_message);

							$response = array('status' => 'OK');
						} else {
							$response = array('status' => 'ERROR', 'reason' => 'Someone has already book for this period');
						}
						
					} else {
						$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to reject this booking');
					}
				} else {
					$response = array('status' => 'ERROR', 'reason' => 'Id Not Found');
				}
			} else {
				$response = array('status' => 'ERROR', 'reason' => 'User Not Found');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed to reject this booking');
		}
        
		return $this->sendApiresponse($response);
    }
	
	public function sendNotificationAction()
    {
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();
		
		$current_date = new \DateTime("NOW", new \DateTimeZone('Asia/Jakarta'));
		//$deadline_date = $current_date->format('Y-m-d');
		$deadline_date = $current_date->modify('-3 day')->format('Y-m-d');
		$deadline_datetime = new \DateTime($deadline_date.' 23:59:59');
		
		if ($_SERVER['REMOTE_ADDR'] == "172.17.0.1") {
			$q  = $qb->select(array('p'))
				->from('AppBundle:Booking', 'p')
				->where('p.booking_date < :deadline_date')
				->andWhere('p.status = :book_status')
				->setParameter('deadline_date', $deadline_datetime)
				->setParameter('book_status', 1)
				->getQuery();

			$bookings = $q->getResult();
			$count_data = count($bookings);
			if ($bookings) {
				foreach ($bookings as $booking) {
					$usr_name = $booking->getUser()->getName();
					$usr_mail = $booking->getUser()->getEmail();
					$dp_name = $booking->getDormitory()->getUser()->getName();
					$dp_mail = $booking->getDormitory()->getUser()->getEmail();
					$dorm_name = $booking->getDormitory()->getName();
					$book_id = $booking->getId();
					$checkin = $booking->getCheckinDate();
					$book_date = $booking->getBookingDate();
					$checkout = $booking->getCheckoutDate();
					$desc = $booking->getDescription();
					
					$booking->setStatus(2);
					
					$em->persist($booking);
					$em->flush();
					
					$body_message = "
						Hi ".$usr_name.",<br><br>
						
						Here is update for your booking. Details :<br>
						1. Dorm Name  : ".$dorm_name."<br>
						2. Booking No : ".$book_id."<br>
						3. Check In   : ".$checkin."<br>
						4. Check Out  : ".$checkout."<br>
						5. Book Date  : ".$book_date."<br>
						6. Desc.      : ".$desc."<br>
						7. Status     : Auto cancel because no response from dorm provider after 3 days<br><br>
						
						Regards,<br>
						Admin DormApp
					";
					
					$this->sendEmail($usr_mail,'Booking Notification',$body_message);
					
					$body_message = "
						Hi ".$dp_name.",<br><br>
						
						Admin just cancel a booking for your dormitory because no response from you. Details :<br>
						1. Dorm Name  : ".$dorm_name."<br>
						2. Cust Name  : ".$usr_name."<br>
						3. Booking No : ".$book_id."<br>
						4. Check In   : ".$checkin."<br>
						5. Check Out  : ".$checkout."<br>
						6. Book Date  : ".$book_date."<br>
						7. Desc.      : ".$desc."<br>
						8. Status     : Auto cancel because no response from dorm provider after 3 days<br><br>
						
						Regards,<br>
						Admin DormApp
					";
					
					$this->sendEmail($dp_mail,'Booking Notification',$body_message);
				}
				$response = array('status' => 'OK', 'reason' => 'Rejected '.$count_data.' booking');
			} else {
				$response = array('status' => 'OK', 'reason' => 'Rejected 0 booking');
			}
		} else {
			$response = array('status' => 'ERROR', 'reason' => 'Not Allowed');
		}
		
		return $this->sendApiresponse($response);
    }
	
	private function sendEmail($to,$subject,$email) {
		$email = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom('dorm.applications@gmail.com')
			->setTo($to)
			->setBody($email, 'text/html')
		;
		$this->get('mailer')->send($email);
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
