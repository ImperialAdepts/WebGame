<?php

namespace AppBundle\PlanetConnection;

use AppBundle\Entity\SolarSystem\Planet;
use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DynamicPlanetConnector implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = NULL)
    {
        $this->container = $container;
    }

    /**
     * @param $dbName
     * @param $dbUser
     * @param $dbPassword
     * @param $dbHost
     * @param bool $reset
     *
     * @throws \Exception
     */
    public function resetConnection($dbName, $dbUser, $dbPassword, $dbHost, $reset = false)
    {

        try {

            //establish the connection
            $connection = $this->container->get('doctrine.dbal.dynamic_planet_connection');

            if ($connection->isConnected()) {
                $this->container->get('doctrine')->getManager('planet')->flush();
            }

            if ($reset && $connection->isConnected()) {
                $connection->close();
            }

            $refConn = new \ReflectionObject($connection);
            $refParams = $refConn->getProperty('params');
            $refParams->setAccessible('public'); //we have to change it for a moment

            $params = $refParams->getValue($connection);
            $params['dbname'] = $dbName;
            $params['user'] = $dbUser;
            $params['password'] = $dbPassword;
            $params['host'] = $dbHost;

            $refParams->setAccessible('private');
            $refParams->setValue($connection, $params);

            if ($reset) {
                $this->container->get('doctrine')->resetManager('planet');
            }

        }
        catch (\Exception $e) {
            throw $e;
        }

    }

    /**
     * @param Planet $planet
     * @throws Exception
     */
    public function setPlanet(Planet $planet, $reset = false)
    {
        $credentials = $planet->getDatabaseCredentials();
        $this->resetConnection(
            $credentials['database_name'],
            $credentials['database_user'],
            $credentials['database_password'],
            $credentials['database_host'],
            $reset);
    }

}