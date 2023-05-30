<!DOCTYPE html>
<html>
<head>
    <title>下注统计</title>
    <style>
        #unrecognized {
           color: red;
        }
    </style>
    <script src="https://kj.syazi.com/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#bet_data").focus(function(){
                $(this).attr("placeholder", ""、 "" ""  ""、"");
            });
            $("#bet_data").blur(function(){
                if($(this).val() === ""){
                    $(this).attr("placeholder", "请输入下注内容，例如：\n单个号码投注：5:100\n多个号码共享投注：5,6,7各100\n生肖投注：虎各100（等同于2,14,26,38各100）");
                }
            });
        });
    </script>
  </head>
   <body>
    <?php
    session_start();  //启动session

    // 初始化号码统计数组
    if (!isset($_SESSION['numberAmounts'])) {
        $_SESSION['numberAmounts'] = array();
        for ($i = 1; $i <= 49; $i++) {
            $_SESSION['numberAmounts'][$i] = 0;
        }
    }

    // 初始化总金额
    if (!isset($_SESSION['totalAmount'])) {
        $_SESSION['totalAmount'] = 0;
    }

    // 检查是否需要清空统计
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear'])) {
        for ($i = 1; $i <= 49; $i++) {
            $_SESSION['numberAmounts'][$i] = 0;
        }
        $_SESSION['totalAmount'] = 0;
        $_SESSION['unrecognized'] = '';
    }

    // Unrecognized lines
    if (!isset($_SESSION['unrecognized'])) {
        $_SESSION['unrecognized'] = '';
    }

    // 处理表单提交
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // 获取下注内容
    $betData = $_POST['bet_data'];

    // 解析下注数据
    $lines = explode("\n", $betData);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            // Handle lines with '各'
            if (strpos($line, '各') !== false) {
                list($numbers, $amount) = explode('各', $line);
                $amount = intval(trim($amount));
                
                $numbers = preg_split("/[,\.：:，、\s\/-]+/", trim($numbers));
                foreach ($numbers as $number) {
                    $number = trim($number);
                    $zodiacNumbers = getZodiacNumbers($number);
                    if (is_numeric($number)) {
                        $_SESSION['numberAmounts'][intval($number)] += $amount;
                        $_SESSION['totalAmount'] += $amount;
                    } else if ($zodiacNumbers) {
                        foreach ($zodiacNumbers as $zodiacNumber) {
                            $_SESSION['numberAmounts'][$zodiacNumber] += $amount;
                            $_SESSION['totalAmount'] += $amount; // move this line here
                        }
                    } else {
                        $_SESSION['unrecognized'] .= $line . "\n";
                        break;
                    }
                }
                } else {
    // Handle lines with ':', '号', and '买'
    $amount = 0;
    $segments = [];
    if (strpos($line, ':') !== false) {
        $segments = explode(':', $line);
    } else if (strpos($line, '号') !== false) {
        $segments = explode('号', $line);
    } else if (strpos($line, '买') !== false) {
        $segments = explode('买', $line);
    }

    if (count($segments) == 2) {
        $amount = intval(trim(end($segments)));
        array_pop($segments);

        foreach ($segments as $number) {
            $number = trim($number);
            if (is_numeric($number)) {
                $_SESSION['numberAmounts'][intval($number)] += $amount;
                $_SESSION['totalAmount'] += $amount;
            } else {
                $_SESSION['unrecognized'] .= $line . "\n";
                break;
            }
        }
    } else {
        $_SESSION['unrecognized'] .= $line . "\n";
    }
}
            }
        }
    }

    // 返回生肖对应的号码数组
function getZodiacNumbers($zodiac)
{
    $zodiacNumbers = array(
        '子' => array(4, 16, 28, 40),
        '牛' => array(3, 15, 27, 39),
        '虎' => array(2, 14, 26, 38),
        '兔' => array(1, 13, 25, 37, 49),
        '龙' => array(12, 24, 36, 48),
        '蛇' => array(11, 23, 35, 47),
        '马' => array(10, 22, 34, 46),
        '羊' => array(9, 21, 33, 45),
        '申' => array(8, 20, 32, 44),
        '鸡' => array(7, 19, 31, 43),
        '狗' => array(6, 18, 30, 42),
        '猪' => array(5, 17, 29, 41)
    );

    return $zodiacNumbers[$zodiac] ?? null;
}
    ?>
       <style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

body {
    font-family: 'Roboto', sans-serif;
    background: #1A1A1A;
    color: #FFFFFF;
    margin: 0;
    padding: 0;
    transition: all 0.25s ease;
}

.page-title {
    text-align: center;
    font-size: 50px;
    color: #00B4DB;
    margin-top: 30px;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
}

form textarea {
    width: 60%;
    padding: 20px;
    border: none;
    background: #232323;
    color: #FFFFFF;
    border-radius: 10px;
    font-size: 16px;
    box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.25s ease;
}

.form-buttons {
    display: flex;
    justify-content: space-between;
    width: 50%;
}

form input[type="submit"] {
    padding: 15px 30px;
    background-color: #00B4DB;
    color: #FFFFFF;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.5s ease;
}

form input[type="submit"]:hover {
    background-color: #0083B0;
}

#statistics {
    border-collapse: collapse;
    width: 50%;
    margin: 2em auto;
    font-size: 1em;
    box-shadow: 0 0 40px rgba(0,255,255,0.5);
    border-radius: 10px;
    overflow: hidden;
    background-color: rgba(0,0,0,0.8);
    transform-style: preserve-3d;
    perspective: 1000px;
}

#statistics td, #statistics th {
    padding: 15px 20px;
    text-align: center;
    position: relative;
    transition: all 0.6s ease;
}

#statistics th {
    background: linear-gradient(to right, #36d1dc, #5b86e5);
    color: white;
    text-transform: uppercase;
    letter-spacing: .3em;
}

#statistics tr:nth-child(even) {
    background-color: rgba(255,255,255,0.05);
}

#statistics td span.number {
    color: #0ff;
    font-weight: 500;
}

#statistics td span.zodiac {
    color: #999;
    font-style: italic;
}

#statistics td.amount-red {
    color: #ff3860;
    font-weight: bold;
}

#statistics td:hover {
    transform: rotateY(180deg);
    background-color: rgba(255,255,255,0.1);
}

#statistics td {
    transition: all 0.3s ease-out;
    opacity: 0.7;
}

#statistics td:hover {
    transform: scale(1.1);
    background-color: rgba(255,255,255,0.1);
    opacity: 1.0;
}


h3 {
    font-weight: bold;
    color: #00B4DB;
    text-align: center;
    margin-top: 30px;
}

.container {
    max-width: 1200px;
    margin: auto;
    padding: 10px;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #333;
    color: #fff;
    padding: 15px 20px;
}

.navbar .logo {
    font-size: 24px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.navbar .menu {
    display: flex;
    align-items: center;
}

.navbar .menu li {
    list-style: none;
    margin-left: 20px;
}

.navbar .menu li a {
    color: #fff;
    text-decoration: none;
    font-size: 18px;
    transition: color 0.3s ease;
}

.navbar .menu li a:hover {
    color: #36d1dc;
}

.footer {
    background: #333;
    color: #fff;
    text-align: center;
    padding: 20px;
}

.button-container {
    display: flex;
    justify-content: space-between;
    width: 50%;
}

.button-container input[type="submit"] {
    width: 45%;
}

p.note {
    text-align: center;
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #FFFFFF;
    line-height: 1.5;
}
.betting-instructions {
    text-align: center;
    color: #FFFFFF;
    padding: 20px;
}

.betting-instructions h3 {
    font-size: 24px;
    color: #00B4DB;
    margin-bottom: 20px;
}

.betting-instructions ol {
    text-align: left;
    display: inline-block;
    list-style-position: inside;
}

.betting-instructions li {
    font-size: 16px;
    line-height: 1.5;
    margin-bottom: 10px;
}

.betting-instructions strong {
    color: #00B4DB;
}
.note {
    color: #b3b3b3;
    text-align: center;
    font-size: 16px;
    line-height: 1.5;
}

.important-note {
    color: #FF3860;
    font-weight: bold;
}
.highlighted-text {
    text-align: center;
    color: #FFD700;
    font-size: 2em;
    margin-top: 20px;
    margin-bottom: 20px;
}

</style>
   <h1 class="page-title">下注统计</h1>
    <form method="post" action="">
        <textarea id="bet_data" name="bet_data" rows="10" cols="50" placeholder="请输入下注内容例如：
单个号码投注：5:100
多个号码共享投注：5,6,7各100
生肖投注：虎各100（等同于2,14,26,38各100）
号码+生肖组合下注：5,6,虎各100"></textarea><br>
        <textarea id="unrecognized" name="unrecognized" rows="10" cols="50" disabled><?php echo $_SESSION['unrecognized']; ?></textarea><br>
        <div class="button-container">
        <input type="submit" name="submit" value="提交">
        <input type="submit" name="clear" value="清空统计">
    </div>
</form>
<h2 class="highlighted-text">下注统计 总金额：<?php echo isset($_SESSION['totalAmount']) ? $_SESSION['totalAmount'] : '未知'; ?></h2>
<table id="statistics">
    <tr>
        <th>投注号码</th>
        <th>下注金额</th>
    </tr>
<?php
// 定义生肖与号码的对应关系
$zodiacMap = array(
    '鼠' => array(4, 16, 28, 40),
    '牛' => array(3, 15, 27, 39),
    '虎' => array(2, 14, 26, 38),
    '兔' => array(1, 13, 25, 37, 49),
    '龙' => array(12, 24, 36, 48),
    '蛇' => array(11, 23, 35, 47),
    '马' => array(10, 22, 34, 46),
    '羊' => array(9, 21, 33, 45),
    '猴' => array(8, 20, 32, 44),
    '鸡' => array(7, 19, 31, 43),
    '狗' => array(6, 18, 30, 42),
    '猪' => array(5, 17, 29, 41)
);

if (!isset($_SESSION['numberAmounts'])) {
    echo '<tr><td colspan="2">暂无下注信息</td></tr>';
    exit;
}

// 检查是否有下注的号码
$hasBets = false;
foreach ($_SESSION['numberAmounts'] as $amount) {
    if ($amount > 0) {
        $hasBets = true;
        break;
    }
}

// 根据下注金额从高到低排序号码
if ($hasBets) {
    arsort($_SESSION['numberAmounts']);
    // 针对金额相同的情况，按照号码从小到大排序
    $sortedNumbers = [];
    $sameAmounts = [];
    foreach ($_SESSION['numberAmounts'] as $number => $amount) {
        if (!isset($sameAmounts[$amount])) {
            $sameAmounts[$amount] = [];
        }
        $sameAmounts[$amount][] = $number;
    }
    foreach ($sameAmounts as $amount => $numbers) {
        sort($numbers); // 号码从小到大排序
        $sortedNumbers = array_merge($sortedNumbers, $numbers);
    }
    $_SESSION['numberAmounts'] = array_combine($sortedNumbers, $_SESSION['numberAmounts']);

    // 显示号码和对应的下注金额
    foreach ($_SESSION['numberAmounts'] as $number => $amount) {
        $zodiac = '';
        foreach ($zodiacMap as $zodiacName => $zodiacNumbers) {
            if (in_array($number, $zodiacNumbers)) {
                $zodiac = $zodiacName;
                break;
            }
        }

        $numberDisplay = ($number <= 49) ? $number : sprintf('%02d', $number);
        echo '<tr><td><span class="number">' . $numberDisplay . '</span>（<span class="zodiac">' . $zodiac . '</span>）</td><td class="' . ($amount > 1000 ? 'amount-red' : '') . '">' . $amount . '</td></tr>';
    }
} else {
    echo '<tr><td colspan="2">暂无下注</td></tr>';
}
?>
</table>

<div class="betting-instructions">
    <h3>下注格式说明</h3>
    <ol>
        <li>
            <strong>单个号码下注：</strong>
            下注格式为“号码:金额”，例如“5:100”。在这个例子中，你下注了100个单位（如美元、元、欧元等）在号码5上。
        </li>
        <li>
            <strong>多个号码共享下注：</strong>
            下注格式为“号码,号码,号码各金额”，例如“5,6,7各100”。在这个例子中，你下注了100个单位在号码5、6和7上，每个号码都投注了100。
        </li>
        <li>
            <strong>单个生肖下注：</strong>
            下注格式为“生肖各金额”，例如“虎各100”。在这个例子中，你下注了100个单位在虎的所有号码上（例如：2,14,26,38各100）。
        </li>
        <li>
            <strong>多个生肖共享下注：</strong>
            下注格式为“生肖1,生肖2,生肖3各金额”，例如“虎,兔,龙各100”。在这个例中，你下注了100个单位在虎、兔和龙的所有号码上。
        </li>
        <li>
            <strong>号码+生肖组合下注：</strong>
            你可以在同一行中组合号码和生肖，例如“5,6,虎各100”。在这个例子中，你下注了100个单位在号码5、6和虎的所有号码上。
            <br>
        </li>
    </ol>
</div>
<p class="note">
    <span class="important-note">注意：</span>
    上述所有示例都假设你的下注单位为100，你可以根据你的实际情况改变下注金额。另外，目前仅支持号码和以下12个生肖的中文名称：鼠、牛、虎、兔、龙、蛇、马、羊、猴、鸡、狗和猪。
</p>

    <style>
  .result-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
  }

  .result-container > div {
    box-sizing: border-box;
    padding: 10px;
    background-color: #f5f5f5;
    border-radius: 5px;
    margin: 10px;
    text-align: center;
    max-width: 400px;
    width: 100%;
  }

  .result-container h2 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
  }

  .result-container ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  .result-container li {
    margin-bottom: 5px;
  }
</style>
</div>
</body>  <BR>  <BR>
<footer>
  <div class="container">
<p style="text-align: center; font-weight: bold; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;">© 2023 syazi.com. All Rights Reserved.</p><br><br>  <BR>
  </div>
</footer>
<style>
  #back-to-top {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99;
    font-size: 20px;
    background-color: #333;
    color: #fff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    text-align: center;
    line-height: 40px;
    cursor: pointer;
  }

  #back-to-top.show {
    display: block;
  }
</style>
<button id="back-to-top"><i class="fa fa-arrow-up"></i></button>
<script>
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      document.getElementById("back-to-top").classList.add("show");
    } else {
      document.getElementById("back-to-top").classList.remove("show");
    }
  }

  document.getElementById("back-to-top").onclick = function() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  };
</script>
</body>
</html>