<?php
 
namespace App\Controller;
 
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
 
/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends FOSRestController
{	
	
    public function getLoginCheckAction() {}

    /**
     * Create User.
     * @Rest\Post("/usuario")
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="body",
     *     type="string",
     *     description="password",
     *	   schema={}
     *  
     * )
     *
     * @SWG\Parameter(
     *     name="_email",
     *     in="body",
     *     type="string",
     *     description="email",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_roles",
     *     in="roles",
     *     type="string",
     *     description="roles",
     *	   schema={}
     * )
     *
     * @SWG\Tag(name="Usuario")
     */
    public function postUserAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new user();

        $serializer = $this->get('jms_serializer');

        $entityManager = $this->getDoctrine()->getManager();

        $message = '';

        try{
        	$code = 200;
            $error = false;
	        $user->setUsername($request->get('_username'));
	        $user->setPassword(
	                $passwordEncoder->encodePassword(
	                    $user,
	                    $request->get('_password')
	                )
	            );

	        $roles = array();
	        $roles[] = $request->get('_roles');
	        $user->setEmail($request->get('_email'));
	        $user->setRoles($roles);

	        
	        $entityManager->persist($user);
	        $entityManager->flush();

	    }catch(Exception $ex){
	    	$code = 500;
            $error = true;
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
	    }
	    $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
        
    }
    /**
     * Lists all Users.
     * @Rest\Get("/usuario.{_format}", name="users_list_all", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all users in database."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all users."
     * )
     * @SWG\Tag(name="Usuario")
     */
    public function getAllUsersAction(Request $request)
    {	
    	$serializer = $this->get('jms_serializer');

    	$users = [];

        $entityManager = $this->getDoctrine()->getManager();

        $message = '';

        try{
        	$code = 200;
            $error = false;

            $users = $entityManager->getRepository("App:User")->findAll();

            if (is_null($users)) {
                $users = [];
            }

        }catch (Exception $ex){
        	$code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Users from the database - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $users : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
    /**
     * @Rest\Get("/usuario/{id}.{_format}", name="user_id", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets an user by id."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The user with the passed Id was not found."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The user ID"
     * )
     *
     *
     * @SWG\Tag(name="Usuario")
     */
    public function getUserAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');

    	$user = new User();

        $entityManager = $this->getDoctrine()->getManager();

        $message = '';
 
        try {
            $code = 200;
            $error = false;
 
            $user_id = $id;
            $user = $entityManager->getRepository("App:User")->find($user_id);
 
            if (is_null($user)) {
                $code = 500;
                $error = true;
                $message = "The user does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current User - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
    
 
}