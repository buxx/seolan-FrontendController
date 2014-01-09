<?php

class SecurityController
{
  public static function getRoadGroupCheck($roads, $function, $group = Null)
  {
    if (($road = self::findRoadWithRoadFunction($roads, $function)))
    {
      if ($group)
        return $this->roadHaveGroup($road, $group);
      return $road['groups'];
    }
    
    return False;
  }
  
  protected static function findRoadWithRoadFunction($roads, $function)
  {
    foreach ($roads as $road)
    {
      if ($road['function'] == $function)
      {
        return $road;
      }
    }
    
    return False;
  }
  
  protected static function roadHaveGroup($road, $group)
  {
    return in_array($group, $road['groups']);
  }
  
}
