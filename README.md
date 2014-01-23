seolan-FrontendController
=================

## Introduction

Sur-couche a placer dans les classes de vues afin de centraliser la configuration
des vues.

## Evolution

Des appels comme avec "secGroups()" sont fait en statiques. Le FontendController
pourrais être encore mieux intégré dans le cas ou ces méthodes seraient utilisé
orienté  objet.

Fournir un générateur d'url PHP et Smarty pour lequel on fournis le nom de la route,
les paramètres et nous retourne le string de l'url.

TODO: Bien vérifier que si aucune vue n'est trouvé: appliquer (si définie comme tel)
la vue par défaut (travailler avec le paramètre function que seolan met par defaut). 

## Exemple

Dans un fichier de vue, class.myproject.inc
``` php
<?php

require_once 'lib/FrontendController/FrontendController.php';
require_once 'lib/FrontendController/SecurityController.php';

class MyProject extends XShell
{
  // On définie les paramètre de chaques routes ici
  static protected $roads = array(
    // Cette route se nomme "foo"
    'foo' => array(
      // Elle correspond a la méthode "foo" de la classe MyProject
      'function' => 'foo',
      // On définie les réègle de sécurité
      'groups' => array('none'),
      // Le format de l'url si on doit reconnaitre la vue avec la forme de l'url
      'url' => "#\/foo_([a-zA-Z0-9]+).html#",
      // Les paramètres à appliquer si c'est cette action que l'on execute
      'parameters' => array(
        // Le premier paramètre fournis dans l'url se nommme "bar"
        0 => 'bar',
        // Nous imposeront le paramètre "template"
        'template' => 'foobarbaz.html'
      )
    ),
    // Notre page d'accueil.
    'index' => array(
      // Elle correspond a la méthode "index" de la classe MyProject
      // Ne possèdant pas de "format d'url", cette action sera trouvé si l'url
      // précise la fonction demandé (index.php?_function=index)
      'function' => 'index',
      // On définie les réègle de sécurité
      'groups' => array('none')
    ),
  );
  
  protected $controller;
  
  function __construct($parameters = Null)
  {
    // On construit notre controlleur avec nos routes
    $this->controller = new FrontendController(self::$roads);
  }

  // Si l'url rewriting est désactivé on utilise la méthode run()
  public function run($parameters)
  {
    // On insère notre controlleur dans le point d'entrée 
    $this->controller->decode(filter_input(INPUT_SERVER, 'REQUEST_URI'));
    return parent::run($parameters);
  }

  // Sinon, si l'url rewritiung est activé on utilise la méthode decodeRewriting()
  public function decodeRewriting($url)
  {
    if (False === $this->controller->decode($url))
      return parent::decodeRewriting($url);
  }

  function secGroups($function, $group = Null)
  {
    // On utilise notre configuration des routes secGroups
    return SecurityController::getRoadGroupCheck(self::$roads, $function, $group);
  }

  public function foo($ar)
  {
    $parameters_manager = new XParam($ar);
    // Pour une URL "http://myproject.com/foo_baz.html"
    echo $parameters_manager->get('bar'); // Affiche "baz"

    // La configuration de cette route a aussi imposé comme template: foobarbaz.html
    echo $parameters_manager->get('template'); // Affiche "foobarbaz.html"
  }
  
  function index($ar)
  {
    ...
  }
}
```
