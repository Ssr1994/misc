<?php
  function processOp(&$ops, &$nums) {
    $op = array_pop($ops);
    $num_r = array_pop($nums);
    $num_l = array_pop($nums);
    if (!$op or !$num_r or !$num_l)
      return false;

    switch ($op) {
      case '+':
        $nums[] = $num_l + $num_r;
        break;
      case '-':
        $nums[] = $num_l - $num_r;
        break;
      case '*':
        $nums[] = $num_l * $num_r;
        break;
      case '/':
        $nums[] = $num_l / $num_r;
        break;
      default:
        return false;
    }
    return true;
  }

  function computeExpr($expr) {
    $ops = array();
    $nums = array();
    $num = "";
    $isMulOrDiv = false;
    
    $len = strlen($expr);
    for ($i = 0; $i < $len; $i++) {
      switch ($ch = substr($expr, $i, 1)) {
        case '+':
        case '-':
          if ($num == "") // negative or positive sign
            $num .= $ch;
          else {
            $nums[] = $num;
            $num = "";
            if ($isMulOrDiv) {
              if (!processOp($ops, $nums))
                return 'e';
              $isMulOrDiv = false;
            }
            $ops[] = $ch;
          }
          break;
        case '*':
        case '/':
          if ($num == "")
            return 'e';

          $nums[] = $num;
          $num = "";
          if ($isMulOrDiv) {
            if (!processOp($ops, $nums))
              return 'e';
          } else
            $isMulOrDiv = true;

          $ops[] = $ch;
          break;
        default: // digit
          $num .= $ch;
      }
    }
    if ($num == "")
      return 'e';
    else
      $nums[] = $num;

    while (count($ops)) {
      if (!processOp($ops, $nums))
        return 'e';
    }

    if (count($nums) != 1)
       return 'e';

    return array_pop($nums);
  }

  if ($expr = addslashes($_GET['expr'])) {
    $expr = preg_replace('/\s+/', '', $expr);
    $content = "<h2>Result</h2>";

    if (preg_match('/[^0-9+\-.*\/]/', $expr))
      $result = $content . "Invalid expression!";
    else {
      $result = computeExpr($expr);
      if ($result == 'e')
        $result = $content . "Invalid expression!";       
      else
        $result = $content . $expr. " = " . $result;
    }
  }
?>

<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="utf-8">
  <title>Calculator</title>
</head>

<body>
  <h1>Calculator</h1>
  (Ver 1.1 by Siran Shen)
  <p>Type an expression in the following box (e.g., 1.2+15*4/10).</p>

  <form method="GET">
    <input name="expr" type="text" placeholder="Type here"></input>
    <input type="submit" value="Compute"></input>
  </form>

  <ul>
    <li>Only numbers and +, -, *, and / operators are allowed.</li>
    <li>The evaluation follows the standard operator precedence.</li>
    <li>The calculator does not support parenthese.</li>
    <li>The calculator handles invalid input "gracefully." It does not output PHP error messages.</li>
  </ul>

    </body>
</html>