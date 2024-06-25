<?php

namespace App\Controller;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class SecurityController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/security", name="security")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SecurityController.php',
        ]);
    }

    /**
     * @Route("/api/login", name="app_login", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        if ($this->getUser()) {
            return $this->json(
                array(
                    'message' => 'Utilisateur deja connecté'
                ),
                Response::HTTP_OK
            );
        }

        $params = json_decode($request->getContent(), true);
        $db = $this->getDoctrine()->getManager();
        $repository = $db->getRepository(User::class);
        /** @var User $user */
        $user = $repository->findOneBy(array('email' => $params['email']));

        if ($user && $this->passwordEncoder->isPasswordValid($user, $params['password'])) {
            $token = bin2hex(random_bytes(30));
            $user->setApiToken($token);
            $db->persist($user);
            $db->flush();

            return $this->json(
                array('token' => $token),
                Response::HTTP_ACCEPTED
            );
        } else {
            return $this->json(
                array('error' => 'Invalids credentials'),
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
    }

    /**
     * @Route("/api/logout", name="app_logout")
     * @throws Exception
     */
    public function logout()
    {
        throw new \Exception('Will be intercepted before getting here');
    }
}
