<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Application\Entity\Builder\TaskBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Exception;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response; //У phpStorm траблы с тайпхинтингом, последние две зависимости можно убрать

use Application\Entity\User;
use Application\Entity\Token;
use Application\Entity\Task;
use Application\Entity\Permission;

class IndexController extends AbstractActionController
{

    /** @var Token token */
    private $token;

    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Очищает базу, добавляет в неё одного юзера, два токена для него и три таска
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function seedAction()
    {
        if ($user = $this->entityManager->getRepository(User::class)->findOneByLogin("A"))
        {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }


        $user = new User();
        $user->setLogin("A");
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $tokenRead = new Token();
        $tokenRead->setToken("8040c6830d2e14af191feef7eaf");
        $tokenRead->setUser($user);
        $this->entityManager->persist($tokenRead);
        $this->entityManager->flush();

        $permissionRead = new Permission(Permission::PERMISSION_READ);
        $permissionRead->setToken($tokenRead);
        $this->entityManager->persist($permissionRead);
        $this->entityManager->flush();

        $tokenWrite = new Token();
        $tokenWrite->setToken("akpsdkaosdpoasdkopsadk");
        $tokenWrite->setUser($user);
        $this->entityManager->persist($tokenWrite);
        $this->entityManager->flush();

        $permissionWrite = new Permission(Permission::PERMISSION_WRITE);
        $permissionWrite->setToken($tokenWrite);
        $this->entityManager->persist($permissionWrite);
        $this->entityManager->flush();

        $task1 = (new TaskBuilder())
            ->withTitle('Отчет для Джона.')
            ->withDescription('Не забыть, что нужно написать отчет в отдел Джона. Он ждет его к 16:00 22.05.2017')
            ->withCreatedAt('2017-03-05 12:00:00')
            ->forUser($user)
            ->build();
        $this->entityManager->persist($task1);

        $task2 = (new TaskBuilder())
            ->withTitle('Встреча с заказчиком.')
            ->withDescription('На завтра назначена встреча с заказчиком. Необходимо подготовить презентацию по нашим продуктам.')
            ->withCreatedAt('2017-03-05 12:05:00')
            ->forUser($user)
            ->build();
        $this->entityManager->persist($task2);

        $task3 = (new TaskBuilder())
            ->withTitle('Подготовить презентацию к встрече.')
            ->withDescription('Нужно не меньше 10 слайдов. Подробности см: http://localhost/api/tasks ')
            ->withCreatedAt('2017-03-05 12:11:46')
            ->forUser($user)
            ->build();
        $this->entityManager->persist($task3);

        $this->entityManager->flush();

    }

    /**
     * Обработчик GET /api/tasks
     * @return JsonModel
     */
    public function getAction() : JsonModel
    {
        if ($this->token->hasPermission(Permission::PERMISSION_READ))
        {
            if ($id = (string) $this->params()->fromRoute('id'))
            {
                if ($task = $this->token->getUser()->getTaskWithId($id))
                {
                    return $this->getSuccessResponse($task->jsonSerialize());
                }
            } else
            {
                $data = $this->token->getUser()->getTasks();
                return $this->getSuccessResponse($data);
            }
        }
        return $this->getUnauthorizedResponse();

    }

    /**
     * Обработчик POST /api/tasks
     * @return JsonModel
     */
    public function postAction() : JsonModel
    {
        if ($this->token->hasPermission(Permission::PERMISSION_WRITE))
        {
            /** @var Request $request */
            $request = $this->getRequest();

            $title = $request->getPost('title');
            $description = $request->getPost('description');
            $createdAt = $request->getPost('createdAt');

            $task = (new TaskBuilder())
                ->withTitle($title)
                ->withDescription($description)
                ->withCreatedAt($createdAt)
                ->forUser($this->token->getUser())
                ->build();

            try
            {
                $this->entityManager->persist($task);
                $this->entityManager->flush();
            } catch (ORMException $e)
            {
                return $this->getServerErrorResponse($e->getMessage());
            }

            $data = ['message' => 'create_status'];
            return $this->getSuccessResponse($data, 201);
        }
        return $this->getUnauthorizedResponse();
    }

    /**
     * Обработчик PUT /api/tasks
     * @return JsonModel
     */
    public function putAction() : JsonModel
    {
        if ($this->token->hasPermission(Permission::PERMISSION_WRITE))
        {
            $id = (string) $this->params()->fromRoute('id');
            if (!empty($id))
            {
                if ($task = $this->token->getUser()->getTaskWithId($id))
                {
                    /** @var Request $request */
                    $request = $this->getRequest();

                    $title = $request->getPost('title');
                    $description = $request->getPost('description');
                    $createdAt = $request->getPost('createdAt');

                    $task = (new TaskBuilder($task))
                        ->withTitle($title)
                        ->withDescription($description)
                        ->withCreatedAt($createdAt)
                        ->build();

                    try
                    {
                        $this->entityManager->persist($task);
                        $this->entityManager->flush();
                    } catch (ORMException $e)
                    {
                        return $this->getServerErrorResponse($e->getMessage());
                    }

                    $data = ['message' => 'create_status'];
                    return $this->getSuccessResponse($data);
                }
            }
        }
        return $this->getUnauthorizedResponse();
    }

    public function getUnauthorizedResponse(): JsonModel
    {
        $this->getResponse()->setStatusCode(401);
        $data = [
            "error" => "unauthorized",
            "error_description" => "A human-readable error message",
        ];
        return new JsonModel($data);
    }

    public function getSuccessResponse($data, $status = 200) : JsonModel
    {
        $this->getResponse()->setStatusCode($status);
        return new JsonModel($data);
    }

    public function getServerErrorResponse($data, $status = 500) : JsonModel
    {
        $this->getResponse()->setStatusCode($status);
        $data = [
            "error" => "internal",
            "error_description" => $data,
        ];
        return new JsonModel($data);
    }

    /**
     * Оверрайд для проверки токена
     * @param MvcEvent $e
     * @return JsonModel
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch)
        {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
        }

        $action = $routeMatch->getParam('action', 'not-found');
        $method = static::getMethodFromAction($action);

        if (!method_exists($this, $method))
        {
            $method = 'notFoundAction';
        }

        //Проверка токена на существование
        $headers = $this->getRequest()->getHeaders();
        if ($headers->has('Authorization'))
        {
            $this->token = $this->entityManager->getRepository(Token::class)->findOneByToken($headers->get('Authorization')->getFieldValue());
            if ($this->token)
            {
                $actionResponse = $this->$method();
            } else
            {
                $actionResponse = $this->getUnauthorizedResponse();
            }
        } else
        {
            $actionResponse = $this->getUnauthorizedResponse();
        }

        $e->setResult($actionResponse);

        return $actionResponse;
    }

}
