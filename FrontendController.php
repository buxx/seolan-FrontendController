<?php

// TODO: Commenter la classe et les méthodes
class FrontendController
{
  
  protected $roads;
  
  public function __construct($roads)
  {
    $this->roads = $roads;
  }
  
  public function getRoads()
  {
    return $this->roads;
  }
  
  public function decode($url)
  {
    if (($road = $this->findRoadWithUrl($url)))
    {
      $this->configure($road, $url);
      return True;
    }
    
    return False;
  }
  
  protected function findRoadWithUrl($url)
  {
    if (($road = $this->findRoadWithRoadFunction($url)))
      return $road;
    
    if (($road = $this->findRoadWithRoadUrl($url)))
      return $road;
    
    return False;
  }
  
  protected function findRoadWithRoadFunction($url)
  {
    if (($query_function = $this->getUrlFunctionParameter($url)))
    {
      foreach ($this->roads as $road)
      {
        if ($road['function'] == $query_function)
        {
          return $road;
        }
      }
    }
    
    return False;
  }
  
  protected function getUrlFunctionParameter($url)
  {
    if (array_key_exists('query', $parsed_url = parse_url($url)))
    {
      $parsed_query = array();
      parse_str($parsed_url['query'], $parsed_query);
      
      if (array_key_exists('_function', $parsed_query))
      {
        return $parsed_query['_function'];
      }
    }
  }
  
  protected function findRoadWithRoadUrl($url)
  {
    foreach ($this->roads as $road)
    {
      if ($this->urlMatchWithRoad($road, $url))
        return $road;
    }
    
    return False;
  }
  
  protected function urlMatchWithRoad($road, $url)
  {
    if (!array_key_exists('url', $road))
      return False;
    
    preg_match($road['url'], $url, $preg_result);
    if (count($preg_result))
      return True;
    
    return False;
  }
  
  public function haveRoad($road_name)
  {
    if (isset($this->roads[$road_name]))
      return True;
    return False;
  }
  
  protected function getRoad($function_name)
  {
    if ($this->haveRoad($function_name))
      return $this->roads[$function_name];
    return False;
  }
  
  // TODO: Plus élégant (double foreache, etc)
  protected function configure($road, $url)
  {
    $_REQUEST['function'] = $road['function'];
    $url_parameters = $this->getMatchedUrlParameters($road, $url);
    
    foreach ($road['parameters'] as $road_parameter_id => $road_parameter_value)
    {
      if (!is_integer($road_parameter_id))
      {
        $_REQUEST[$road_parameter_id] = $road_parameter_value;
      }
    }
    
    foreach ($url_parameters as $road_parameter_id => $road_parameter_value)
    {
      $_REQUEST[$road_parameter_id] = $road_parameter_value;
    }
  }
  
  // TODO: Plus élégant
  protected function getMatchedUrlParameters($road, $url)
  {
    $matched_parameters = array();
    preg_match($road['url'], $url, $preg_result);
    
    // Le premier enregistrement contient la chaine complète
    unset($preg_result[0]);
    $preg_result = array_values($preg_result);
    
    if (array_key_exists('parameters', $road))
    {
      foreach ($road['parameters'] as $road_parameter_key => $road_parameter_name)
      {
        if (array_key_exists($road_parameter_key, $preg_result))
        {
          $matched_parameters[$road_parameter_name] = $preg_result[$road_parameter_key];
        }
      }
    }
    
    return $matched_parameters;
  }
}