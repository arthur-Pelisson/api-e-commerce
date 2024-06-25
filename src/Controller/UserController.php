<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class UserController
 * @package App\Controller
 * @Route("api")
 */
class UserController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * @Route("/register", name="app_register", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();

        $user
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setLogin($data['login'])
            ->setPassword($passwordEncoder->encodePassword($user,$data['password']))
            ->setEmail($data['email']);
        ;

        try {
            $db = $this->getDoctrine()->getManager();
            $db->persist($user);
            $db->flush();

            return $this->json(
                array('message' => 'Votre compte a bien été créée, vous pouvez maintenant vous connecter.'),
                Response::HTTP_CREATED
            );

        } catch(Exception $exception) {
            return new JsonResponse(array('message' => 'Une erreur est survenue :' .$exception), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/user", name="user", methods={"GET", "HEAD"})
     * @param Request $request
     * @return Response
     * @throws ExceptionInterface
     */
    public function profile(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);
        $id = $this->getUser()->getId();
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);

        $user = $this->repository->find($id);

        if ($user) {
            return $this->json(
                $serializer->normalize($user),
                Response::HTTP_ACCEPTED
            );
//            return $this->json(
//                array(
//                    $this->getUser()->getId(),
//                ),
//                Response::HTTP_ACCEPTED
//            );
        } else {
            return $this->json([
                'error' => 'Une erreur est survenue lors de la requête, vérifiez que vous avez rentré les bon paramètres',
            ], Response::HTTP_NO_CONTENT);
        }
    }

    /**
     * @Route("/user", name="user_update", methods={"PUT", "HEAD"})
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $this->getUser()->getId();
        $user = $this->repository->find($id);
        $user
            ->setFirstname(isset($data['firstname']) ? $data['firstname'] : $user->getFirstname())
            ->setLastname(isset($data['lastname']) ? $data['lastname'] : $user->getLastname())
        ;

        try {
            $db = $this->getDoctrine()->getManager();
            $db->persist($user);
            $db->flush();

            return $this->json(
                array('message' => 'Votre compte a bien été mise à jour.'),
                Response::HTTP_ACCEPTED
            );

        } catch(Exception $exception) {
            return new JsonResponse(array('message' => $exception), Response::HTTP_BAD_REQUEST);
        }
    }
}
